<?php

namespace App\Services;

class CpfService
{
    /**
     * Extract numbers from a string.
     *
     * @param  string  $str
     * @return string
     */
    public static function extractNumbers(string $str): string
    {
        return preg_replace('/\D/', '', $str);
    }

    /**
     * Format a CPF number.
     *
     * @param  string  $cpf
     * @return string
     */
    public static function formatCpf(string $cpf): string
    {
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
}
