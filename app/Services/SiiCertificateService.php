<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class SiiCertificateService
{
    protected $certificatePath;
    protected $certificatePassword;
    protected $publicKey;
    protected $privateKey;
    protected $certificate;

    public function __construct()
    {
        $this->certificatePath = config('sii.certificado.ruta');
        $this->certificatePassword = config('sii.certificado.password');
    }

    /**
     * Cargar certificado desde archivo P12
     */
    public function loadCertificate(): bool
    {
        try {
            if (!file_exists($this->certificatePath)) {
                throw new Exception("Archivo de certificado no encontrado: {$this->certificatePath}");
            }

            $p12cert = file_get_contents($this->certificatePath);
            $certs = [];

            if (!openssl_pkcs12_read($p12cert, $certs, $this->certificatePassword)) {
                throw new Exception("Error al leer el certificado P12. Verifique la contraseña.");
            }

            $this->certificate = $certs['cert'];
            $this->privateKey = $certs['pkey'];
            $this->publicKey = openssl_pkey_get_public($this->certificate);

            Log::info("Certificado SII cargado exitosamente");
            return true;

        } catch (Exception $e) {
            Log::error("Error al cargar certificado SII: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener información del certificado
     */
    public function getCertificateInfo(): array
    {
        if (!$this->certificate) {
            $this->loadCertificate();
        }

        $parsed = openssl_x509_parse($this->certificate);
        
        return [
            'subject' => $parsed['subject'],
            'issuer' => $parsed['issuer'],
            'valid_from' => date('Y-m-d H:i:s', $parsed['validFrom_time_t']),
            'valid_to' => date('Y-m-d H:i:s', $parsed['validTo_time_t']),
            'serial_number' => $parsed['serialNumber'],
            'rut' => $this->extractRutFromCertificate($parsed),
        ];
    }

    /**
     * Verificar si el certificado es válido
     */
    public function isValid(): bool
    {
        try {
            if (!$this->certificate) {
                $this->loadCertificate();
            }

            $parsed = openssl_x509_parse($this->certificate);
            $now = time();

            return ($now >= $parsed['validFrom_time_t'] && $now <= $parsed['validTo_time_t']);
        } catch (Exception $e) {
            Log::error("Error al validar certificado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Firmar datos con el certificado
     */
    public function sign(string $data): string
    {
        if (!$this->privateKey) {
            $this->loadCertificate();
        }

        $signature = '';
        if (!openssl_sign($data, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
            throw new Exception("Error al firmar los datos");
        }

        return base64_encode($signature);
    }

    /**
     * Obtener el certificado en formato PEM
     */
    public function getCertificatePem(): string
    {
        if (!$this->certificate) {
            $this->loadCertificate();
        }

        return $this->certificate;
    }

    /**
     * Extraer RUT del certificado
     */
    protected function extractRutFromCertificate(array $parsed): ?string
    {
        // El RUT suele estar en el CN (Common Name) del subject
        $cn = $parsed['subject']['CN'] ?? '';
        
        // Buscar patrón de RUT chileno
        if (preg_match('/(\d{1,8}-[\dkK])/', $cn, $matches)) {
            return $matches[1];
        }

        // También puede estar en el serialNumber
        $serial = $parsed['subject']['serialNumber'] ?? '';
        if (preg_match('/(\d{1,8}-[\dkK])/', $serial, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Crear solicitud de token firmada
     */
    public function createSignedTokenRequest(string $seed): string
    {
        if (!$this->privateKey) {
            $this->loadCertificate();
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
               "<getToken>" .
               "<item><Semilla>{$seed}</Semilla></item>" .
               "</getToken>";

        // Firmar el XML
        $signature = '';
        openssl_sign($xml, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);

        // Crear el XML con la firma
        $signedXml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
                     "<getToken>" .
                     "<item>" .
                     "<Semilla>{$seed}</Semilla>" .
                     "</item>" .
                     "<Signature>" . base64_encode($signature) . "</Signature>" .
                     "</getToken>";

        return $signedXml;
    }

    public function __destruct()
    {
        if ($this->publicKey) {
            openssl_free_key($this->publicKey);
        }
    }
}
