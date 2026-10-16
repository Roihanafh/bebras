<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaBiro extends Model
{
    use HasFactory;

    protected $table = 'anggota_biro';

    protected $fillable = [
        'biro_id',
        'nama',
        'tahun_masuk',
        'tanggal_lahir',
        'no_anggota',
        'status_validasi',
    ];

    protected $casts = [
        'tahun_masuk'   => 'integer',
        'tanggal_lahir' => 'date',
    ];

    public function biro()
    {
        return $this->belongsTo(User::class, 'biro_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'biro_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status_validasi', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status_validasi', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status_validasi', 'rejected');
    }

    public static function generateNextNoAnggota(int $biroId): string
    {
        $maxNo = static::where('biro_id', $biroId)
            ->selectRaw('MAX(CAST(no_anggota AS UNSIGNED)) as max_no')
            ->value('max_no');

        $nextNum = ($maxNo !== null) ? ((int)$maxNo + 1) : 1;
        return str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT);
    }
}
