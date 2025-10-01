<?php


namespace App\Http\Services\AnalogValidation;


class ImageQA
{
    public static function validation(array $data): string
    {
        foreach ($data as $field) {
            if(!$field['ImageQualityCheckList']['result']){
                return 'Quality photo is incorrect or hard to read. (Front side)';
            }
        }
    }
}
