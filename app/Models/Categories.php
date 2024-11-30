<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categries';
    protected $guarded = [];
    protected $primaryKey = "id";
    protected $fillable = ['name','name_ar','description','description_ar','image'];

    public function items()
    {
        return $this->hasMany('App\Models\Items', 'id');
    }
}
