<?php


namespace App\Helpers;


class Str
{
    public static function generatePayId(object $request): string
    {
        return str_replace(' ', '_', strtolower($request->firstname)) . '_' . Str::generateRandom(7) . '@paymentiq.cc';
    }

    public static function generateRandom(int $n = 0)
    {
        $al = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k'
            , 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u',
            'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E',
            'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '0', '2', '3', '4', '5', '6', '7', '8', '9'];

        $len = !$n ? random_int(7, 12) : $n; // Chose length randomly in 7 to 12

        $ddd = array_map(function ($a) use ($al) {
            $key = random_int(0, 60);
            return $al[$key];
        }, array_fill(0, $len, 0));
        return implode('', $ddd);
    }

    public static function unHash(string $hash) : array
    {
        if (self::checkHash($hash)) {
            $str = explode('BCC', $hash);
            return [
                'hallId' => $str[0],
                'inviting' => base64_decode($str[1]),
                'tgName' => base64_decode($str[2]),
                'realName' => base64_decode($str[3]),
            ];
        }
        else {
            return [
                'inviting' => null,
                'tgName' => null,
                'realName' => null,
            ];
        }

    }

    public static function checkRef(string $ref): int
    {
        $findHash = strpos($ref, 'BCC');
        if ($findHash) {
            $hallId = explode('BCC', $ref)[0];
        } else {
            $hallId = $ref;
        }
        return (int)$hallId;
    }

    public static function checkHash(string $ref) :mixed
    {
        $findHash = strpos($ref,'BCC');
        if ($findHash){
            $hash = $ref;
        }else{
            $hash = null;
        }
        return $hash;
    }
}
