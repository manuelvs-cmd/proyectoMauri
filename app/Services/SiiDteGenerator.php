<?php

namespace App\Services;

use App\Models\Factura;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Log;
use Exception;

class SiiDteGenerator
{
    protected $certificateService;

    public function __construct(SiiCertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Generar DTE completo desde una factura
     */
    public function generateDTE(Factura $factura): array
    {
        Log::info("Iniciando generación de DTE", ['factura_id' => $factura->id]);

        $factura->load(['pedido.cliente', 'pedido.mercancias']);

        $dte = [
            'Encabezado' => $this->generateEncabezado($factura),
            'Detalle' => $this->generateDetalle($factura),
        ];

        // Agregar referencias si es necesario (notas de crédito, etc.)
        if ($factura->tipo_documento === 'nota_credito') {
            $dte['Referencia'] = $this->generateReferencia($factura);
        }

        Log::info("DTE generado exitosamente", ['factura_id' => $factura->id]);
        
        return $dte;
    }

    /**
     * Generar encabezado del DTE
     */
    protected function generateEncabezado(Factura $factura): array
    {
        $cliente = $factura->pedido->cliente;
        $emisor = config('sii.emisor');
        $tipoDoc = config("sii.tipos_documento.{$factura->tipo_documento}");

        return [
            'IdDoc' => [
                'TipoDTE' => $tipoDoc['codigo'],
                'Folio' => $this->extractFolio($factura->numero_documento),
                'FchEmis' => $factura->fecha_emision->format('Y-m-d'),
                'IndNoRebaja' => 1, // No rebaja
                'TipoDespacho' => 1, // Por cuenta del receptor
                'IndTraslado' => 1, // Operación constituye venta
                'TpoImpresion' => 'N', // Normal
                'IndServicio' => 3, // Factura de servicios
                'MntBruto' => 1, // Montos brutos
                'FmaPago' => $this->getFormaPago($factura->pedido->formas_pago),
                'FchCancel' => $factura->fecha_emision->format('Y-m-d'),
            ],
            'Emisor' => [
                'RUTEmisor' => $emisor['rut'],
                'RznSoc' => $this->cleanString($emisor['razon_social']),
                'GiroEmis' => $this->cleanString($emisor['giro']),
                'Acteco' => $this->getActeco($emisor['giro']),
                'DirOrigen' => $this->cleanString($emisor['direccion']),
                'CmnaOrigen' => $this->cleanString($emisor['comuna']),
                'CiudadOrigen' => $this->cleanString($emisor['ciudad']),
            ],
            'Receptor' => [
                'RUTRecep' => $cliente->rut,
                'RznSocRecep' => $this->cleanString($cliente->razon_social),
                'GiroRecep' => $this->cleanString($cliente->giro ?? 'Varios'),
                'DirRecep' => $this->cleanString($cliente->direccion_exacta),
                'CmnaRecep' => $this->cleanString($cliente->comuna),
                'CiudadRecep' => $this->cleanString($cliente->ciudad),
            ],
            'Totales' => [
                'MntNeto' => round($factura->subtotal),
                'TasaIVA' => 19,
                'IVA' => round($factura->iva),
                'MntTotal' => round($factura->total),
            ],
        ];
    }

    /**
     * Generar detalle del DTE (productos/servicios)
     */
    protected function generateDetalle(Factura $factura): array
    {
        $detalle = [];
        $lineNumber = 1;

        foreach ($factura->pedido->mercancias as $mercancia) {
            $cantidad = $mercancia->pivot->cantidad_solicitada ?? 1;
            $precioUnitario = $mercancia->pivot->precio_unitario ?? $mercancia->precio_venta;
            $montoLinea = $cantidad * $precioUnitario;

            $detalle[] = [
                'NroLinDet' => $lineNumber,
                'IndExe' => 0, // No exento
                'NmbItem' => $this->cleanString($mercancia->nombre),
                'DscItem' => $this->cleanString($mercancia->nombre), // Descripción
                'QtyItem' => $cantidad,
                'UnmdItem' => 'UN', // Unidad
                'PrcItem' => round($precioUnitario),
                'MontoItem' => round($montoLinea),
            ];

            $lineNumber++;
        }

        return $detalle;
    }

    /**
     * Generar referencias (para notas de crédito/débito)
     */
    protected function generateReferencia(Factura $factura): array
    {
        // Implementar si se necesitan referencias
        return [];
    }

    /**
     * Convertir DTE a XML
     */
    public function dteToXml(array $dte, Factura $factura): string
    {
        $tipoDoc = config("sii.tipos_documento.{$factura->tipo_documento}");
        $folio = $this->extractFolio($factura->numero_documento);

        $xml = [
            '_attributes' => [
                'version' => '1.0',
                'xmlns' => 'http://www.sii.cl/SiiDte',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.sii.cl/SiiDte DTE_v10.xsd',
            ],
            'Documento' => [
                '_attributes' => [
                    'ID' => "DTE-{$tipoDoc['codigo']}-{$folio}"
                ],
                'Encabezado' => $dte['Encabezado'],
                'Detalle' => $dte['Detalle'],
            ]
        ];

        // Si hay referencias, agregarlas
        if (!empty($dte['Referencia'])) {
            $xml['Documento']['Referencia'] = $dte['Referencia'];
        }

        return ArrayToXml::convert($xml, [
            'rootElementName' => 'DTE',
            '_attributes' => [
                'version' => '1.0',
            ],
        ], true, 'UTF-8');
    }

    /**
     * Firmar DTE
     */
    public function signDTE(string $xmlDte): string
    {
        try {
            Log::info("Iniciando firma de DTE");

            // Cargar certificado
            $this->certificateService->loadCertificate();

            // Calcular hash del documento
            $hash = hash('sha256', $xmlDte);
            
            // Firmar el hash
            $signature = $this->certificateService->sign($xmlDte);

            // Obtener información del certificado
            $certInfo = $this->certificateService->getCertificateInfo();

            // Crear elemento de firma XML
            $signatureXml = [
                'Signature' => [
                    '_attributes' => [
                        'xmlns' => 'http://www.w3.org/2000/09/xmldsig#'
                    ],
                    'SignedInfo' => [
                        'CanonicalizationMethod' => [
                            '_attributes' => ['Algorithm' => 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315']
                        ],
                        'SignatureMethod' => [
                            '_attributes' => ['Algorithm' => 'http://www.w3.org/2000/09/xmldsig#rsa-sha256']
                        ],
                        'Reference' => [
                            '_attributes' => ['URI' => ''],
                            'DigestMethod' => [
                                '_attributes' => ['Algorithm' => 'http://www.w3.org/2000/09/xmldsig#sha256']
                            ],
                            'DigestValue' => base64_encode(hash('sha256', $xmlDte, true))
                        ]
                    ],
                    'SignatureValue' => $signature,
                    'KeyInfo' => [
                        'KeyValue' => [
                            'RSAKeyValue' => [
                                'Modulus' => base64_encode('placeholder'), // Se debe extraer del certificado
                                'Exponent' => base64_encode('AQAB')
                            ]
                        ],
                        'X509Data' => [
                            'X509Certificate' => base64_encode($this->certificateService->getCertificatePem())
                        ]
                    ]
                ]
            ];

            // Insertar la firma en el XML
            $dom = new \DOMDocument();
            $dom->loadXML($xmlDte);
            
            $signatureDom = new \DOMDocument();
            $signatureDom->loadXML(ArrayToXml::convert($signatureXml, 'Signature'));
            
            $signatureNode = $dom->importNode($signatureDom->documentElement, true);
            $dom->documentElement->appendChild($signatureNode);

            Log::info("DTE firmado exitosamente");

            return $dom->saveXML();

        } catch (Exception $e) {
            Log::error("Error al firmar DTE: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extraer número de folio del número de documento
     */
    protected function extractFolio(string $numeroDocumento): int
    {
        // Remover prefijo (F, B, etc.) y obtener solo el número
        return (int) preg_replace('/[^0-9]/', '', $numeroDocumento);
    }

    /**
     * Obtener código de forma de pago
     */
    protected function getFormaPago(string $formaPago): int
    {
        $formasPago = [
            'Efectivo' => 1,
            'Transferencia' => 2,
            'Tarjeta' => 3,
        ];

        return $formasPago[$formaPago] ?? 1;
    }

    /**
     * Obtener código de actividad económica
     */
    protected function getActeco(string $giro): int
    {
        // Mapear giros a códigos de actividad económica
        // Por defecto usar 741000 (Actividades jurídicas)
        return 741000;
    }

    /**
     * Limpiar string para XML
     */
    protected function cleanString(string $string): string
    {
        // Remover caracteres especiales y limitar longitud
        $cleaned = strip_tags($string);
        $cleaned = preg_replace('/[^\p{L}\p{N}\s\-\.\,]/u', '', $cleaned);
        return mb_substr(trim($cleaned), 0, 80);
    }
}
