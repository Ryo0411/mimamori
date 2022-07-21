<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wanderers extends Model
{
    use HasFactory;

    // テーブル名
    protected $table = 'wanderers';

    // 可変項目
    protected $fillable = [
        'id',
        'wanderer_name',
        'email',
        'sex',
        'age',
        'user_id',
        'profile_id',
        'emergency_tel',
        'wandering_flg',
        'discover_flg',
        'voiceprint_flg',
        'wanderer_id',
        'wanderer_time',
        'latitude',
        'longitude',
    ];
}
