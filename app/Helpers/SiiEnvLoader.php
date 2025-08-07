<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Dotenv\Dotenv;

class SiiEnvLoader
{
    /**
     * Cargar variables de entorno del archivo .env.sii
     */
    public static function load()
    {
        $siiEnvPath = base_path('.env.sii');
        
        if (!File::exists($siiEnvPath)) {
            // Si no existe .env.sii, intentar cargar desde .env principal
            return;
        }

        try {
            // Leer el contenido del archivo .env.sii
            $siiEnvContent = File::get($siiEnvPath);
            
            // Parsear las líneas y cargar en $_ENV
            $lines = explode("\n", $siiEnvContent);
            
            foreach ($lines as $line) {
                $line = trim($line);
                
                // Saltar líneas vacías y comentarios
                if (empty($line) || str_starts_with($line, '#')) {
                    continue;
                }
                
                // Parsear la línea KEY=VALUE
                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remover comentarios al final de la línea
                    if (str_contains($value, '#')) {
                        $value = trim(explode('#', $value)[0]);
                    }
                    
                    // Remover comillas si las hay
                    $value = trim($value, '"\'');
                    
                    // Solo cargar variables SII
                    if (str_starts_with($key, 'SII_')) {
                        // Cargar en el entorno
                        $_ENV[$key] = $value;
                        putenv("{$key}={$value}");
                    }
                }
            }
            
        } catch (\Exception $e) {
            // Log del error pero no detener la aplicación
            \Log::warning('Error al cargar .env.sii: ' . $e->getMessage());
        }
    }
    
    /**
     * Verificar si la configuración SII está cargada
     */
    public static function isLoaded(): bool
    {
        return !empty(env('SII_EMISOR_RUT'));
    }
    
    /**
     * Obtener una variable SII con valor por defecto
     */
    public static function get(string $key, $default = null)
    {
        return env($key, $default);
    }
}
