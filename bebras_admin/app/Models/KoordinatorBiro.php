<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoordinatorBiro extends Model
{
    use HasFactory;

    protected $table = 'koordinator_biro';

    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'username',
        'password',
        'telepon',
    ];

    protected $hidden = [
        'password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function biro()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
