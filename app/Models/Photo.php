<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    // use HasFactory;
    public const DEFAULT_DIR = "photos";

    protected $fillable = [
        'tuser_id',
        'filename'
    ];
    // protected $hidden = [
    // ];
    // protected $casts = [
    // ];

    /**
     * Get the tuser that owns the Photo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tuser()
    {
        return $this->belongsTo(Tuser::class, 'tuser_id');
    }

    function getAssetPath() {
        $folder = Photo::DEFAULT_DIR . '/' . $this->tuser->uid;
        return asset('storage/'.$folder.'/'.$this->filename);
    }

    function getRealPath() {
        $storage_rel_path = 'app/public/'.Photo::DEFAULT_DIR . '/' . $this->tuser->uid.'/'.$this->filename;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $storage_rel_path = str_replace('/', '\\', $storage_rel_path);
        }
        return storage_path($storage_rel_path);
    }
}
