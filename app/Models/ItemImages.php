<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemImages extends Model
{
    protected $table = 'item_images';
    protected $primaryKey = "id";
    protected $guarded = [];
    protected $fillable = [
        'item_id',
        'image',
    ];

    public function items()
    {
        return $this->belongsTo(Items::class);
    }
}
