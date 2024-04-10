<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LocalService
{
    /**
     * Format a Local.
     *
     * @param  string  $local
     * @return array|null
     */
    public static function formatLocal($local): array | null
    {
        $local = trim(preg_replace('/\s+/', ' ', $local));
        $patterns = [
            '/([\p{L}\s]+)\s?\/\s?([\p{L}\s]{2,})/iu',
            '/([\p{L}\s]+)\s?-\s?([\p{L}]{2})/iu',
            '/([\p{L}\s]+)-([\p{L}]{2})/iu',
            '/([\p{L}\s]+)([\p{L}]{2})/iu',
            '/([\p{L}\s]+)\s?[\.\-]\s?([\p{L}]{2})/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $local, $matches)) {
                $cidade = ucwords(trim($matches[1]));
                $uf = strtoupper(trim($matches[2]));
                if (self::verifyCityInState($cidade, $uf)) {
                    return ['cidade' => $cidade, 'uf' => $uf];
                } else {
                    return [
                        'cidade' => $cidade,
                        'uf' => $uf,
                        'message' => 'Uncertain value!'
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Verify if is a valid city based on the state uf.
     *
     * @param  string  $cidade
     * @param  string  $uf
     * @return bool
     */
    private static function verifyCityInState($cidade, $uf): bool
    {
        $response = Http::get("https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$uf}/municipios");

        if ($response->successful()) {
            $cidades = $response->json();

            foreach ($cidades as $cidadeData) {
                if (strcoll(mb_strtolower($cidadeData['nome'], 'UTF-8'), mb_strtolower($cidade, 'UTF-8')) === 0) {
                    return true; 
                }
            }
        }

        return false;
    }
}