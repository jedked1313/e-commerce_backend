<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupons extends Model
{
    protected $table = 'coupons';
    protected $primaryKey = "id";
    protected $fillable = [
        'name',
        'discout',
        'usage_count',
        'expiration_date',
    ];
}
