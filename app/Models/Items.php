<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    use HasFactory;
    protected $table = 'items';
    protected $primaryKey = "id";
    protected $guarded = [];
    protected $fillable = [
        'category_id',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'images',
        'quantity',
        'is_active',
        'price',
        'discount'
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    // Bring all images of the item (Item Details Screen)
    public function images()
    {
        return $this->hasMany(ItemImages::class, 'item_id', 'id');
    }

    // Bring only one image for the item (Home Screen, Cart Screen, Favorites Screen)
    public function singleImage()
    {
        return $this->hasOne(ItemImages::class, 'item_id')
            ->select(['id', 'item_id', 'image'])
            ->oldest('created_at');
    }

    public function favorites()
    {
        return $this->hasMany(Favorites::class, 'item_id', 'id');
    }
}
