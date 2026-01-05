<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class XatUser extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $fillable = [
        'name',
        'email',
        'whatsapp_number',
    ];    
}
