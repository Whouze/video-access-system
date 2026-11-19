<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessRequest extends Model
{
    protected $fillable = ['user_id', 'video_id', 'durasi_diminta', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
    public function grant()
    {
        return $this->hasOne(AccessGrant::class);
    }
}
