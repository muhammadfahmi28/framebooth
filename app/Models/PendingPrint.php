<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingPrint extends Model
{
    use HasFactory;

    protected $fillable = [
        "filename_1st",
        "first_id",
        "filename_2nd",
        "second_id",
        "printed_at",
        "filename_merged",
        'failed_at'
    ];

    protected $casts = [
        'printed_at' => 'datetime',
        'failed_at' => 'datetime'
    ];

}
