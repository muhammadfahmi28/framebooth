<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    // use HasFactory;
    public const DEFAULT_DIR = "photos";

    protected $fillable = [
        'tuser_id',
        'filename',
        'raws',
        'uploaded_at',
        'failed_at',
        'qr_url'
    ];
    // protected $hidden = [
    // ];
    protected $casts = [
        'raws' => "array",
        'uploaded_at' => 'datetime',
        'failed_at' => 'datetime'
    ];

    /**
     * Get the tuser that owns the Photo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tuser()
    {
        return $this->belongsTo(Tuser::class, 'tuser_id');
    }

    function getAssetPath($is_small = false) {
        $small = $is_small ? 'small/' : '';
        $folder = Photo::DEFAULT_DIR . '/' . $this->tuser->uid;
        return asset('storage/'.$folder . '/' . $small . $this->filename);
    }

    function getRealPath($is_small = false) {
        $small = $is_small ? 'small/' : '';
        $storage_rel_path = 'app/public/'.Photo::DEFAULT_DIR . '/' . $this->tuser->uid.'/' . $small .$this->filename;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $storage_rel_path = str_replace('/', '\\', $storage_rel_path);
        }
        return storage_path($storage_rel_path);
    }

    function getRawsAssetPath($is_small = false) {
        $small = $is_small ? 'small/' : '';
        $folder = Photo::DEFAULT_DIR . '/' . $this->tuser->uid;
        $urls = [];
        foreach ($this->raws as $filename) {
            $urls[] = asset('storage/'.$folder.'/'.$small.$filename);
        }

        return $urls;
    }

    function getRawsRealPath($is_small = false) {
        $small = $is_small ? 'small/' : '';
        $filenames = [];
        foreach ($this->raws as $filename) {
            $storage_rel_path = 'app/public/'.Photo::DEFAULT_DIR . '/' . $this->tuser->uid.'/'.$small.$filename;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $storage_rel_path = str_replace('/', '\\', $storage_rel_path);
            }
            $filenames[] = storage_path($storage_rel_path);
        }

        return $filenames;
    }

}
