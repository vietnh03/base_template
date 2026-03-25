<?php

namespace App\Models;

use App\AppMain\Core\BaseModel;
use App\AppMain\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends BaseModel
{
    use HasFactory, HasUuid;

    protected $dbConnection = 'mysql';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'full_name',
        'phone_number',
        'email',
        'customer_type',
        'customer_status',
        'assigned_staff_id',
        'address',
        'date_of_birth',
        'gender',
        'source',
        'notes',
    ];
}
