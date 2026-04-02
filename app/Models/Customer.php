<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use App\AppMain\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends BaseModel
{
    use HasFactory, HasUuid;

    protected $dbConnection = 'mysql';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'gender',
        'date_of_birth',
        'email',
        'phone',
        'image',
        'status',
        'password',
        'is_verified',
        'token',
        'notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'token',
        'remember_token',
    ];
}
