<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkripsiSimilarity extends Model
{
    protected $table = 'skripsi_similarities';

    protected $fillable = [
        'skripsi_id',
        'compared_skripsi_id',
        'similarity_score',
        'category',
    ];

    protected $casts = [
        'similarity_score' => 'float',
    ];

    /**
     * The skripsi being checked
     */
    public function skripsi()
    {
        return $this->belongsTo(Skripsi::class, 'skripsi_id');
    }

    /**
     * The skripsi being compared against
     */
    public function comparedSkripsi()
    {
        return $this->belongsTo(Skripsi::class, 'compared_skripsi_id');
    }

    /**
     * Categorize a similarity score
     */
    public static function categorize(float $score): string
    {
        if ($score >= 90) return 'sangat_mirip';
        if ($score >= 80) return 'mirip';
        if ($score >= 70) return 'perlu_ditinjau';
        return 'tidak_mirip';
    }
}
