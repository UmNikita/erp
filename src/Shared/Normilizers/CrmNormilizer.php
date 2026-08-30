<?php

namespace App\Shared\Normilizers;

final readonly class CrmNormilizer
{
    public static function normalizePhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $phone = preg_replace('/\D/', '', $phone);

        if ($phone === '') {
            return null;
        }

        if (str_starts_with($phone, '8') && strlen($phone) === 11) {
            $phone = '7' . substr($phone, 1);
        }

        return $phone;
    }

    public static function normalizeEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }

        $email = mb_strtolower(trim($email));

        return $email === '' ? null : $email;
    }
}