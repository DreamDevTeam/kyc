<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaggedCustomers extends Model
{
    use HasFactory;

    protected $table = 'taggedcustomers';

    protected $fillable = [
        'cid',
        'tag',
        'color',
    ];

    public $timestamps = false;
}
