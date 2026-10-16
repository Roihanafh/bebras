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

    /**
     * Scope query untuk status disetujui (approved)
     */
    public function scopeApproved($query)
    {
        return $query->where('status_validasi', 'approved');
    }

    /**
     * Scope query untuk status pending
     */
    public function scopePending($query)
    {
        return $query->where('status_validasi', 'pending');
    }

    /**
     * Scope query untuk status ditolak (rejected)
     */
    public function scopeRejected($query)
    {
        return $query->where('status_validasi', 'rejected');
    }

    /**
     * Generate nomor anggota 3 digit berikutnya khusus untuk biro tertentu.
     * Contoh: Jika belum ada -> 001. Jika nomor terakhir 002 -> 003.
     */
    public static function generateNextNoAnggota(int $biroId): string
    {
        $maxNo = static::where('biro_id', $biroId)
            ->selectRaw('MAX(CAST(no_anggota AS UNSIGNED)) as max_no')
            ->value('max_no');

        $nextNum = ($maxNo !== null) ? ((int)$maxNo + 1) : 1;
        return str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT);
    }
}
