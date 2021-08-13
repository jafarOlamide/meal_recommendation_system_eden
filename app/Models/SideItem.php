<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SideItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'meal_id'
    ];
}
