<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorites extends Model
{
    protected $table = 'favorites';
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
        return $this->belongsToMany(Items::class);
    }
}
