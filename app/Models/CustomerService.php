<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerService extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'is_online',
        'current_load'
    ];
}
