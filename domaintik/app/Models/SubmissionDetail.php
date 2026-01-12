<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionDetail extends Model
{
    protected $table = 'transaksi.submission_details';

    protected $fillable = [
        'submission_id',
        'request_type',
        'requested_domain',
        'requested_quota_gb',
        'initial_password_hint',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    public function getRequestTypeLabelAttribute(): string
    {
        return match($this->request_type) {
            'domain' => 'Domain (.unila.ac.id)',
            'hosting' => 'Hosting (cPanel)',
            'vps' => 'VPS (Virtual Private Server)',
            default => $this->request_type,
        };
    }
}
