<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SiiConvertCertificate extends Command
{
    protected $signature = 'sii:convert-certificate 
                           {input : Ruta del archivo PFX/P12 de entrada}
                           {output : Ruta del archivo P12 de salida}
                           {password : Password del certificado}';
    
    protected $description = 'Convertir certificado PFX a P12 para SII';

    public function handle()
    {
        $input = $this->argument('input');
        $output = $this->argument('output');
        $password = $this->argument('password');

        $this->info("🔄 Convirtiendo certificado...");
        $this->line("📂 Origen: {$input}");
        $this->line("📁 Destino: {$output}");

        try {
            if (!file_exists($input)) {
                $this->error("❌ No se encontró el archivo: {$input}");
                return 1;
            }

            $p12cert = file_get_contents($input);
            $certs = [];

            if (!openssl_pkcs12_read($p12cert, $certs, $password)) {
                $this->error("❌ Error al leer el certificado. Verifique el password.");
                $this->line("💡 Errores de OpenSSL:");
                while ($msg = openssl_error_string()) {
                    $this->line("   • {$msg}");
                }
                return 1;
            }

            $outputDir = dirname($output);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
                $this->info("📁 Creada carpeta: {$outputDir}");
            }

            if (!openssl_pkcs12_export_to_file($certs['cert'], $output, $certs['pkey'], $password)) {
                $this->error("❌ Error al exportar el certificado");
                return 1;
            }

            $this->info("✅ Certificado convertido exitosamente");
            
            $certInfo = openssl_x509_parse($certs['cert']);
            
            $this->line("");
            $this->info("📋 INFORMACIÓN DEL CERTIFICADO:");
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['CN (Nombre)', $certInfo['subject']['CN'] ?? 'N/A'],
                    ['Válido desde', date('d/m/Y H:i:s', $certInfo['validFrom_time_t'])],
                    ['Válido hasta', date('d/m/Y H:i:s', $certInfo['validTo_time_t'])],
                    ['Emisor', $certInfo['issuer']['CN'] ?? 'N/A'],
                    ['Número serie', $certInfo['serialNumber'] ?? 'N/A'],
                ]
            );

            $now = time();
            if ($now < $certInfo['validFrom_time_t']) {
                $this->warn("⚠️ El certificado aún no es válido");
            } elseif ($now > $certInfo['validTo_time_t']) {
                $this->error("❌ El certificado ha expirado");
            } else {
                $this->info("✅ El certificado es válido");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            return 1;
        }
    }
}
