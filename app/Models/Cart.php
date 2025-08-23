<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    protected $primaryKey = "id";
    protected $guarded = [];
    protected $fillable = [
        'user_id',
        'item_id',
    ];

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function items(){
        return $this->belongsTo(Items::class,'item_id','id');
    }
}
