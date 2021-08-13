<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealAllergy extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $fillable = [
        'meal_id',
        'allergy_type_id'
    ];
}
