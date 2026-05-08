<?php

namespace App\Services;

class TextNormalizeService
{
    public function normalize(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $value = mb_strtolower($value, 'UTF-8');
        $value = str_replace('đ', 'd', $value);
        $value = str_replace('Đ', 'd', $value);

        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($converted !== false) {
            $value = $converted;
        }

        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? '';

        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }

    /**
     * @return list<string>
     */
    public function tokens(?string $value): array
    {
        $normalized = $this->normalize($value);
        if ($normalized === '') {
            return [];
        }

        return array_values(array_unique(array_filter(explode(' ', $normalized))));
    }
}
