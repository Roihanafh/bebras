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

    /**
     * Format ID Member (10 digit): yyyxxzzaaa
     * - yyy: Kode biro (3 digit)
     * - xx: 2 digit tahun masuk
     * - zz: 2 digit tahun lahir
     * - aaa: 3 digit no urut anggota
     */
    public function getFormattedIdMemberAttribute(): string
    {
        $kodeBiro = str_pad((string)($this->biro?->kode_biro ?? '000'), 3, '0', STR_PAD_LEFT);
        $tahunMasuk = $this->tahun_masuk ? substr((string)$this->tahun_masuk, -2) : '00';
        $tahunLahir = $this->tanggal_lahir ? substr((string)\Carbon\Carbon::parse($this->tanggal_lahir)->format('Y'), -2) : '00';
        $noUrut = str_pad((string)($this->no_anggota ?? '000'), 3, '0', STR_PAD_LEFT);

        return $kodeBiro . $tahunMasuk . $tahunLahir . $noUrut;
    }

    /**
     * Tanggal masa aktif (1 tahun dari tanggal pendaftaran / created_at)
     */
    public function getExpiryDateAttribute(): \Carbon\Carbon
    {
        $date = $this->created_at ?? now();
        return \Carbon\Carbon::parse($date)->addYear();
    }

    /**
     * Format tanggal expiry: exp DD/MM/YYYY
     */
    public function getFormattedExpiryDateAttribute(): string
    {
        return 'exp ' . $this->expiry_date->format('d/m/Y');
    }

    /**
     * Nama biro
     */
    public function getNamaBiroAttribute(): string
    {
        return $this->biro?->name ?? 'BEBRAS INDONESIA';
    }

    /**
     * Generate QR Code SVG string
     */
    public function getQrCodeSvgAttribute(): string
    {
        return \SimpleSoftwareIO\QrCode\Facades\QrCode::size(214)
            ->margin(1)
            ->generate($this->formatted_id_member);
    }

    /**
     * Generate QR Code data-uri base64 SVG
     */
    public function getQrCodeBase64Attribute(): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode($this->qr_code_svg);
    }
}

