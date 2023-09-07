<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;

class Tuser extends Model
{
    use HasFactory, Authenticatable;

    protected $fillable = [
        'uid',
        'name',
        'code',
        'max_photos',
        "autodelete_on",
        "valid_until"
    ];
    protected $hidden = [
        'code',
        'uid'
    ];
    protected $casts = [
        'autodelete_on' => 'datetime',
        'valid_until' => 'date',
        'code'
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->code;
    }

}
