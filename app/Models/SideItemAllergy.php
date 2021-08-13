<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SideItemAllergy extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $fillable = [
        'side_item_id',
        'allergy_type_id'
    ];
}
