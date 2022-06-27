<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterDevice extends Model
{
    use HasFactory;
    protected $table = 'register_device';
    protected $fillable = ['id', 'device_id', 'activation_code', 'device_api_key', 'device_type', 'created_at', 'updated_at'];
    protected $primaryKey = 'id';
}
