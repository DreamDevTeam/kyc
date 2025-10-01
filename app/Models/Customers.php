<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    protected $fillable = [
        'payId',
        'customerid',
        'regulaTid',
        'ref',
        'email',
        'firstName',
        'middleName',
        'lastName',
        'mobile',
        'playerid',
        'telegramid',
        'dob',
        'city',
        'state',
        'address',
        'postcode',
        'mobilePrefix',
        'description',
        'deactivated',
        'autoprocessing',
        'status',
        'weekLimit',
        'weekLimitUsed',
        'kyc',
        'label',
        'tg_name',
        'hash',
        'emailVerified',
        'register_date',
        'hcbt',
        'contacted',
        'checked',
        'kyc_needed_label',
        'docs_update',
    ];
}
