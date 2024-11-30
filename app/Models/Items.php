<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    protected $table = 'items';
    protected $primaryKey = "id";
    protected $guarded = ['image'];
    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'description_ar',
        'image',
        'quantity',
        'is_active',
        'price',
        'discount'
    ];

    public function categories()
    {
        return $this->belongsTo('App\Models\Categories', 'id');
    }
}
