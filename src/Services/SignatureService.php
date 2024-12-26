<?php

namespace Novaday\Moadian\Services;

use Novaday\Moadian\Exceptions\MoadianException;

class SignatureService
{
    private string $privateKey;

    public function __construct(string $privateKey)
    {
        $this->privateKey = $privateKey;
    }

    public function sign(array $data, array $headers)
    {
        $text = $this->normalizer($data, $headers);
        $signature = '';

        if (openssl_sign($text, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
            return base64_encode($signature);
        } else {
            throw new MoadianException('Failed to sign the text with message ' . openssl_error_string());
        }
    }


    public static function normalizer(array $data, array $headers): string
    {
        $data = $data + $headers;

        $normalizedData = [];

        $flatted = self::dot($data);

        ksort($flatted);

        foreach ($flatted as $value) {
            if (blank($value)) {
                $value = '#';
            } else {
                if (blank($value = strval($value))) {
                    $value = '#';
                } else {
                    $value = str_replace('#', '##', $value);
                }
            }

            $normalizedData[] = $value;
        }

        return implode("#", $normalizedData);
    }

    private static function dot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, self::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }
}
