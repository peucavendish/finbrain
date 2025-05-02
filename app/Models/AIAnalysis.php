<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AIAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'age',
        'health_conditions',
        'lifestyle_factors',
        'family_history',
        'occupation',
        'income',
        'risk_score',
        'recommendation',
        'suggested_coverage',
        'monthly_premium_estimate'
    ];

    protected $casts = [
        'health_conditions' => 'array',
        'lifestyle_factors' => 'array',
        'family_history' => 'array',
        'risk_score' => 'float',
        'suggested_coverage' => 'decimal:2',
        'monthly_premium_estimate' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recommendations()
    {
        return $this->hasMany(AIRecommendation::class);
    }
} 