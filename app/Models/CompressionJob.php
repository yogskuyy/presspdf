<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompressionJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'original_filename',
        'original_path',
        'compressed_path',
        'original_size',
        'compressed_size',
        'compression_ratio',
        'status',
        'error_message',
        'compressed_at',
    ];

    protected $casts = [
        'compressed_at' => 'datetime',
        'original_size'  => 'integer',
        'compressed_size' => 'integer',
        'compression_ratio' => 'float',
    ];

    /**
     * Get human-readable original size.
     */
    public function getOriginalSizeHumanAttribute(): string
    {
        return $this->formatBytes($this->original_size);
    }

    /**
     * Get human-readable compressed size.
     */
    public function getCompressedSizeHumanAttribute(): string
    {
        if ($this->compressed_size === null) return '-';
        return $this->formatBytes($this->compressed_size);
    }

    /**
     * Format bytes to human-readable string.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Scope to filter by session.
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Check if job is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if job failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
