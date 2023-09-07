<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    // use HasFactory;

    protected $fillable = [
        'tuser_id',
        'filename'
    ];
    // protected $hidden = [
    // ];
    // protected $casts = [
    // ];

}
