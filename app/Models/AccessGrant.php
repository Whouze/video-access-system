<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessGrant extends Model
{
    protected $fillable = ['access_request_id', 'approved_by', 'tanggal_mulai', 'tanggal_selesai'];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function accessRequest()
    {
        return $this->belongsTo(AccessRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
