<?php


namespace App\Http\Services\AnalogValidation;


class DocType
{
    public static function validation(array|null $data): string|null
    {
        if (is_null($data)) {
            return 'Unsupported  document type!';
        }

        if (!is_null($data) && count($data) < 2) {
            return 'Document type is not valid.';
        }

        return null;
    }
}
