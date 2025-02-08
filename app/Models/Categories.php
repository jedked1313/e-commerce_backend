<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $guarded = [];
    protected $primaryKey = "id";
    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'description_ar',
        'image'
    ];

    public function items()
    {
        return $this->hasMany(Items::class);
    }
}
