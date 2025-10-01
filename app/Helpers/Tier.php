<?php


namespace App\Helpers;


use App\Models\CustomersOptions;
use App\Models\TaggedCustomers;

class Tier
{
    public static function change(bool $kycStatus, int $cid): void
    {
        if($kycStatus) {
            $tag = ['cid'=> $cid, 'tag'=> 'Tier 2','color' => null];
        }else{
            $tag = ['cid'=> $cid, 'tag'=> 'Tier 3','color' =>'warning'];
        }

        TaggedCustomers::updateOrCreate(['cid'=> $cid], $tag);
        CustomersOptions::updateOrCreate(
            ['cid' => $cid],
//            GetDataTiers::handle(type: 'deposit')[$tag['tag']]
        );
    }
}
