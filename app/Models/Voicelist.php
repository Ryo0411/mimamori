<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voicelist extends Model
{
    use HasFactory;

    // テーブル名
    protected $table = 'voicelist';

    // 可変項目
    protected $fillable = [
        'id',
        'user_id',
        'speech_id',
        'speaker_id',
        'delete_flg',
    ];
}
