<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RumahDuka extends Model
{
    protected $table = 'rumah_duka';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
