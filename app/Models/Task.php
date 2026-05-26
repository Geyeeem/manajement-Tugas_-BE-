<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_tugas',
        'deskripsi',
        'kategori',
        'tipe_task',   // ← BARU: 'daily' | 'weekly' | 'monthly'
        'deadline',
        'selesai',
    ];

    protected $casts = [
        'selesai'  => 'boolean',
        'deadline' => 'date',
    ];

    // =========================================================
    //  SCOPES
    // =========================================================

    /** Tugas yang belum selesai */
    public function scopeBelumSelesai($query)
    {
        return $query->where('selesai', false);
    }

    /** Filter berdasarkan tipe_task */
    public function scopeTipeTask($query, string $tipe)
    {
        return $query->where('tipe_task', $tipe);
    }

    /** Hanya daily task */
    public function scopeDaily($query)
    {
        return $query->where('tipe_task', 'daily');
    }

    /** Hanya weekly task */
    public function scopeWeekly($query)
    {
        return $query->where('tipe_task', 'weekly');
    }

    /** Hanya monthly task */
    public function scopeMonthly($query)
    {
        return $query->where('tipe_task', 'monthly');
    }

    // =========================================================
    //  ACCESSORS / HELPERS
    // =========================================================

    /**
     * Label tipe task yang ramah dibaca
     */
    public function getTipeTaskLabelAttribute(): string
    {
        return match ($this->tipe_task) {
            'weekly'  => 'Weekly',
            'monthly' => 'Monthly',
            default   => 'Daily',
        };
    }

    /**
     * Warna badge Bootstrap untuk tipe task
     */
    public function getTipeTaskBadgeAttribute(): string
    {
        return match ($this->tipe_task) {
            'weekly'  => 'info',
            'monthly' => 'warning',
            default   => 'secondary',
        };
    }

    /**
     * Apakah task sudah mendekati deadline?
     * Daily  : ≤ 1 hari
     * Weekly : ≤ 2 hari
     * Monthly: ≤ 5 hari
     */
    public function getMendekatiDeadlineAttribute(): bool
    {
        $batasHari = match ($this->tipe_task) {
            'weekly'  => 2,
            'monthly' => 5,
            default   => 1,
        };

        return Carbon::today()->diffInDays($this->deadline, false) <= $batasHari
            && !$this->selesai;
    }

    // =========================================================
    //  RELATIONS
    // =========================================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}