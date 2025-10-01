<?php


namespace App\Http\Services\AnalogValidation;


class Text
{
    public static function validation(array $data): string
    {
        $fieldList = $data['Text']['fieldList'];

        foreach ($fieldList as $field) {
            if(!$field['validityStatus']){
                return 'Field '.$field['fieldName'].' is incorrect or hard to read.';
            }
        }
    }
}
