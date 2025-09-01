<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'address';
    protected $primaryKey = "id";
    protected $fillable = [
        'contact_phone',
        'city',
        'neighborhood',
        'street',
        'address_name',
        'building',
        'apartment',
        'longitude',
        'latitude',
        'postal_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
