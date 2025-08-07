<?php

namespace App\Services;

use App\Models\Factura;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Exception;

class SiiService
{
    protected $client;
    protected $ambiente;
    protected $urls;
    protected $emisor;
    protected $certificado;
    protected $certificateService;
    protected $dteGenerator;

    public function __construct(
        SiiCertificateService $certificateService,
        SiiDteGenerator $dteGenerator
    ) {
        $this->certificateService = $certificateService;
        $this->dteGenerator = $dteGenerator;
        
        $this->client = new Client([
            'timeout' => config('sii.timeout', 30),
            'verify' => config('sii.ambiente') === 'produccion',
            'headers' => [
                'User-Agent' => 'Laravel-SII-Client/1.0'
            ]
        ]);

        $this->ambiente = config('sii.ambiente', 'certificacion');
        $this->urls = config("sii.urls.{$this->ambiente}");
        $this->emisor = config('sii.emisor');
        $this->certificado = config('sii.certificado');
    }

    /**
     * Enviar factura al SII
     */
    public function enviarFactura(Factura $factura)
    {
        try {
            Log::info("Iniciando envío de factura al SII", ['factura_id' => $factura->id]);

            // Validar prerrequisitos
            $this->validarPrerrequisitos($factura);

            // 1. Generar DTE
            $dte = $this->dteGenerator->generateDTE($factura);
            
            // 2. Convertir a XML
            $xmlDte = $this->dteGenerator->dteToXml($dte, $factura);
            
            // 3. Firmar el documento
            $xmlFirmado = $this->dteGenerator->signDTE($xmlDte);
            
            // 4. Crear sobre de envío
            $xmlEnvio = $this->crearSobreEnvio($xmlFirmado, $factura);
            
            // 5. Obtener token de autenticación
            $token = $this->obtenerToken();
            
            // 6. Enviar al SII
            $resultado = $this->enviarDocumento($xmlEnvio, $token);
            
            // 7. Procesar respuesta y actualizar estado
            $this->procesarRespuestaSii($factura, $resultado);
            
            // 8. Guardar backup del envío
            $this->guardarBackupEnvio($factura, $xmlEnvio, $resultado);
            
            Log::info("Factura enviada exitosamente al SII", [
                'factura_id' => $factura->id,
                'track_id' => $resultado['track_id'] ?? null
            ]);

            return [
                'success' => true,
                'track_id' => $resultado['track_id'] ?? null,
                'message' => 'Documento enviado exitosamente al SII',
                'estado_sii' => $factura->fresh()->sii_estado
            ];

        } catch (Exception $e) {
            Log::error("Error al enviar factura al SII", [
                'factura_id' => $factura->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Actualizar estado de error en la factura
            $factura->update([
                'sii_estado' => 'error',
                'sii_respuesta' => [
                    'error' => $e->getMessage(),
                    'timestamp' => now()->toISOString()
                ]
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error al enviar documento al SII'
            ];
        }
    }

    /**
     * Validar prerrequisitos antes del envío
     */
    protected function validarPrerrequisitos(Factura $factura): void
    {
        // Validar que el certificado sea válido
        if (!$this->certificateService->isValid()) {
            throw new Exception('Certificado digital inválido o expirado');
        }

        // Validar datos del emisor
        $emisor = config('sii.emisor');
        if (empty($emisor['rut']) || empty($emisor['razon_social'])) {
            throw new Exception('Datos del emisor incompletos en la configuración');
        }

        // Validar que la factura tenga los datos necesarios
        if (!$factura->pedido || !$factura->pedido->cliente) {
            throw new Exception('La factura debe tener un pedido y cliente asociado');
        }

        // Validar estado de la factura
        if ($factura->sii_estado === 'aceptado') {
            throw new Exception('La factura ya fue aceptada por el SII');
        }
    }

    /**
     * Crear sobre de envío para el SII
     */
    protected function crearSobreEnvio(string $xmlDteFirmado, Factura $factura): string
    {
        $emisor = config('sii.emisor');
        $tipoDoc = config("sii.tipos_documento.{$factura->tipo_documento}");
        $trackId = $this->generarTrackId();

        $xml = '<?xml version="1.0" encoding="ISO-8859-1"?>' .
               '<EnvioDTE version="1.0" xmlns="http://www.sii.cl/SiiDte" ' .
               'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' .
               'xsi:schemaLocation="http://www.sii.cl/SiiDte EnvioDTE_v10.xsd">' .
               '<SetDTE ID="SetDoc">' .
               '<Caratula version="1.0">' .
               '<RutEmisor>' . $emisor['rut'] . '</RutEmisor>' .
               '<RutEnvia>' . $emisor['rut'] . '</RutEnvia>' .
               '<RutReceptor>' . $factura->pedido->cliente->rut . '</RutReceptor>' .
               '<FchResol>' . date('Y-m-d') . '</FchResol>' .
               '<NroResol>80</NroResol>' .
               '<TmstFirmaEnv>' . date('Y-m-d\TH:i:s') . '</TmstFirmaEnv>' .
               '<SubTotDTE>' .
               '<TpoDTE>' . $tipoDoc['codigo'] . '</TpoDTE>' .
               '<NroDTE>1</NroDTE>' .
               '</SubTotDTE>' .
               '</Caratula>' .
               $xmlDteFirmado .
               '</SetDTE>' .
               '</EnvioDTE>';

        return $xml;
    }

    /**
     * Obtener token de autenticación del SII
     */
    protected function obtenerToken(): string
    {
        try {
            // 1. Obtener semilla
            $seedResponse = $this->client->get($this->urls['seed']);
            $seedXml = new \SimpleXMLElement($seedResponse->getBody()->getContents());
            $seed = (string)$seedXml->SII_RESULT->SEMILLA;

            Log::info("Semilla obtenida del SII", ['seed' => $seed]);

            // 2. Crear solicitud de token firmada
            $tokenRequest = $this->certificateService->createSignedTokenRequest($seed);

            // 3. Enviar solicitud de token
            $tokenResponse = $this->client->post($this->urls['token'], [
                'form_params' => ['pszXml' => $tokenRequest]
            ]);

            $tokenXml = new \SimpleXMLElement($tokenResponse->getBody()->getContents());
            $token = (string)$tokenXml->SII_RESULT->TOKEN;

            Log::info("Token obtenido del SII");

            return $token;

        } catch (GuzzleException $e) {
            throw new Exception("Error al obtener token del SII: " . $e->getMessage());
        }
    }

    /**
     * Enviar documento al SII
     */
    protected function enviarDocumento(string $xml, string $token): array
    {
        try {
            $response = $this->client->post($this->urls['upload'], [
                'multipart' => [
                    [
                        'name'     => 'rutSender',
                        'contents' => str_replace('-', '', $this->emisor['rut'])
                    ],
                    [
                        'name'     => 'dvSender',
                        'contents' => substr($this->emisor['rut'], -1)
                    ],
                    [
                        'name'     => 'rutCompany',
                        'contents' => str_replace('-', '', $this->emisor['rut'])
                    ],
                    [
                        'name'     => 'dvCompany',
                        'contents' => substr($this->emisor['rut'], -1)
                    ],
                    [
                        'name'     => 'archivo',
                        'contents' => $xml,
                        'filename' => 'envio.xml'
                    ]
                ],
                'headers' => [
                    'Cookie' => "TOKEN={$token}"
                ]
            ]);

            $responseBody = $response->getBody()->getContents();
            
            // Parsear respuesta XML del SII
            $responseXml = new \SimpleXMLElement($responseBody);
            $trackId = (string)$responseXml->SII_RESULT->TRACKID ?? null;
            $estado = (string)$responseXml->SII_RESULT->ESTADO ?? null;

            return [
                'track_id' => $trackId,
                'estado' => $estado,
                'respuesta_completa' => $responseBody
            ];

        } catch (GuzzleException $e) {
            throw new Exception("Error al enviar documento al SII: " . $e->getMessage());
        }
    }

    /**
     * Consultar estado de un documento en el SII
     */
    public function consultarEstado(Factura $factura): array
    {
        try {
            if (!$factura->sii_track_id) {
                throw new Exception('La factura no tiene track ID del SII');
            }

            $token = $this->obtenerToken();
            
            $response = $this->client->get($this->urls['query_envio'], [
                'query' => [
                    'rutConsultante' => str_replace('-', '', $this->emisor['rut']),
                    'dvConsultante' => substr($this->emisor['rut'], -1),
                    'trackId' => $factura->sii_track_id
                ],
                'headers' => [
                    'Cookie' => "TOKEN={$token}"
                ]
            ]);

            $responseBody = $response->getBody()->getContents();
            $responseXml = new \SimpleXMLElement($responseBody);
            
            $estado = (string)$responseXml->SII_RESULT->ESTADO ?? 'desconocido';
            
            // Actualizar estado en la factura
            $estadoSii = $this->mapearEstadoSii($estado);
            $factura->update([
                'sii_estado' => $estadoSii,
                'sii_respuesta' => [
                    'estado_consulta' => $estado,
                    'respuesta_completa' => $responseBody,
                    'timestamp' => now()->toISOString()
                ]
            ]);

            return [
                'success' => true,
                'estado' => $estadoSii,
                'estado_original' => $estado,
                'message' => "Estado consultado exitosamente: {$estadoSii}"
            ];

        } catch (Exception $e) {
            Log::error("Error al consultar estado SII", [
                'factura_id' => $factura->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error al consultar estado en el SII'
            ];
        }
    }

    /**
     * Generar Track ID único
     */
    protected function generarTrackId(): string
    {
        return time() . rand(100000, 999999);
    }

    /**
     * Procesar respuesta del SII
     */
    protected function procesarRespuestaSii(Factura $factura, array $resultado): void
    {
        $estado = 'enviado';
        $trackId = $resultado['track_id'] ?? null;

        // Determinar estado basado en la respuesta
        if (isset($resultado['estado'])) {
            $estado = $this->mapearEstadoSii($resultado['estado']);
        }

        // Actualizar factura
        $factura->update([
            'sii_track_id' => $trackId,
            'sii_estado' => $estado,
            'sii_fecha_envio' => now(),
            'sii_respuesta' => $resultado,
            'sii_enviado_automatico' => true
        ]);
    }

    /**
     * Mapear estados del SII a estados internos
     */
    protected function mapearEstadoSii(string $estadoSii): string
    {
        $mapeo = [
            'RCH' => 'rechazado',    // Rechazado
            'EPR' => 'enviado',      // En proceso
            'ACE' => 'aceptado',     // Aceptado
            'ACT' => 'aceptado',     // Aceptado con reparos (tratamos como aceptado)
            'ACD' => 'aceptado',     // Aceptado condicionalmente
            'RSV' => 'enviado',      // Recibido por SII
            'DNK' => 'error',        // Desconocido
        ];

        return $mapeo[$estadoSii] ?? 'enviado';
    }

    /**
     * Guardar backup del envío
     */
    protected function guardarBackupEnvio(Factura $factura, string $xml, array $resultado): void
    {
        try {
            $backupPath = config('sii.folios.backup_enviados');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $filename = "factura_{$factura->id}_" . date('Y-m-d_H-i-s') . '.xml';
            file_put_contents($backupPath . '/' . $filename, $xml);

            // Guardar también la respuesta
            $respuestaFilename = "respuesta_{$factura->id}_" . date('Y-m-d_H-i-s') . '.json';
            $respuestaPath = config('sii.folios.backup_respuestas');
            if (!file_exists($respuestaPath)) {
                mkdir($respuestaPath, 0755, true);
            }
            file_put_contents($respuestaPath . '/' . $respuestaFilename, json_encode($resultado, JSON_PRETTY_PRINT));

            Log::info("Backup guardado", [
                'factura_id' => $factura->id,
                'xml_file' => $filename,
                'response_file' => $respuestaFilename
            ]);

        } catch (Exception $e) {
            Log::warning("No se pudo guardar backup: " . $e->getMessage());
        }
    }

    /**
     * Envío masivo de facturas
     */
    public function envioMasivo(array $facturaIds): array
    {
        $resultados = [];
        $exitosos = 0;
        $fallidos = 0;

        foreach ($facturaIds as $facturaId) {
            $factura = Factura::find($facturaId);
            if (!$factura) {
                $resultados[] = [
                    'factura_id' => $facturaId,
                    'success' => false,
                    'error' => 'Factura no encontrada'
                ];
                $fallidos++;
                continue;
            }

            $resultado = $this->enviarFactura($factura);
            $resultados[] = array_merge(['factura_id' => $facturaId], $resultado);
            
            if ($resultado['success']) {
                $exitosos++;
            } else {
                $fallidos++;
            }

            // Pequeña pausa entre envíos para no saturar el SII
            usleep(500000); // 0.5 segundos
        }

        return [
            'total' => count($facturaIds),
            'exitosos' => $exitosos,
            'fallidos' => $fallidos,
            'resultados' => $resultados
        ];
    }

    /**
     * Validar configuración del SII
     */
    public function validarConfiguracion(): array
    {
        $errores = [];
        $warnings = [];

        // Validar certificado
        try {
            if (!$this->certificateService->isValid()) {
                $errores[] = 'Certificado digital inválido o expirado';
            } else {
                $certInfo = $this->certificateService->getCertificateInfo();
                if (strtotime($certInfo['valid_to']) - time() < 30 * 24 * 3600) { // 30 días
                    $warnings[] = 'El certificado expirará pronto: ' . $certInfo['valid_to'];
                }
            }
        } catch (Exception $e) {
            $errores[] = 'Error al validar certificado: ' . $e->getMessage();
        }

        // Validar datos del emisor
        $emisor = config('sii.emisor');
        $camposRequeridos = ['rut', 'razon_social', 'giro', 'direccion', 'comuna', 'ciudad'];
        foreach ($camposRequeridos as $campo) {
            if (empty($emisor[$campo])) {
                $errores[] = "Dato del emisor faltante: {$campo}";
            }
        }

        // Validar conectividad
        try {
            $this->client->get($this->urls['seed'], ['timeout' => 10]);
        } catch (Exception $e) {
            $errores[] = 'Error de conectividad con SII: ' . $e->getMessage();
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores,
            'warnings' => $warnings,
            'ambiente' => $this->ambiente,
            'urls' => $this->urls
        ];
    }
}
