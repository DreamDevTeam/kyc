<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomersOptions extends Model
{
    use HasFactory;

    protected $table = 'customers_options';

    protected $fillable = [
        'cid',
        'deposit_cashtocode',
        'deposit_facilero',
        'deposit_changelly',
        'withdraw_cashtocode',
        'deposit_payler',
        'withdraw_paycombat',
        'deposit_getkollo',
        'withdraw_payler',
        'deposit_tunzer_cc',
    ];

    protected $primaryKey = 'cid';
    public $incrementing = false;
}
