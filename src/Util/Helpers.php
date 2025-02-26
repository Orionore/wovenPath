<?php

namespace App\Util;

use Random\RandomException;

class Helpers
{

    /**
     * @throws RandomException
     */
    public static function generateToken(int $strength = 32, ?array $chars = NULL): string
    {
        if ($chars != NULL) {
            $token = '';
            $countChar = count($chars);
            for ($i = 0; $i < $strength; $i++) {
                $token .= $chars[random_int(0, $countChar - 1)];
            }
            return $token;
        } else {
            return bin2hex(random_bytes($strength));
        }
    }


}