<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['judul', 'keterangan', 'file_path'];

    public function accessRequests()
    {
        return $this->hasMany(AccessRequest::class);
    }

}
