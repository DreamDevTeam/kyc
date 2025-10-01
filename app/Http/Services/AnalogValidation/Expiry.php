<?php


namespace App\Http\Services\AnalogValidation;


class Expiry
{
    public static function validation(): string
    {
        return "Document expire or field 'Expire date' hard to read!";
    }
}
