<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSnapResult extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function snap_user()
    {
        return $this->belongsTo(SnapUser::class, 'snap_user_id');
    }
}
