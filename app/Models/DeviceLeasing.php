<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLeasing extends Model
{
    use HasFactory;
    protected $table = 'device_leasing';
    protected $fillable = ['id', 'device_id', 'lease_construction_id', 'lease_max_training', 'lease_max_date', 'lease_actual_start_date', 'lease_next_check', 'created_at', 'updated_at'];
    protected $primaryKey = 'id';
}
