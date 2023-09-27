<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Tuser extends Model implements
    AuthenticatableContract,
    AuthorizableContract
{
    use HasFactory, Authenticatable, Authorizable;

    protected $fillable = [
        'uid',
        'name',
        'code',
        'max_photos',
        'autodelete_on',
        'valid_until'
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

    public function photos()
    {
        return $this->hasMany(Photo::class, 'tuser_id');
    }

    public function canTakePhotos()
    {
        return ($this->photos()->count() < $this->max_photos);
    }

}
