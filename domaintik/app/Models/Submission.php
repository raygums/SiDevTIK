<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasUuids;

    protected $table = 'transaksi.submissions';

    protected $fillable = [
        'ticket_number',
        'applicant_id',
        'unit_id',
        'admin_responsible_name',
        'admin_responsible_nip',
        'admin_responsible_position',
        'admin_responsible_phone',
        'application_name',
        'description',
        'status',
        'assigned_verifier_id',
        'assigned_executor_id',
        'generated_form_path',
        'signed_form_path',
        'attachment_identity_path',
        'metadata',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'metadata' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_APPROVED_ADMIN = 'approved_admin';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Diajukan',
            self::STATUS_IN_REVIEW => 'Sedang Ditinjau',
            self::STATUS_APPROVED_ADMIN => 'Disetujui Admin',
            self::STATUS_PROCESSING => 'Diproses',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_REJECTED => 'Ditolak',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_SUBMITTED => 'blue',
            self::STATUS_IN_REVIEW => 'yellow',
            self::STATUS_APPROVED_ADMIN => 'indigo',
            self::STATUS_PROCESSING => 'purple',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_REJECTED => 'red',
            default => 'gray',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Ticket Number Generator
    |--------------------------------------------------------------------------
    */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TIK';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        
        return "{$prefix}-{$date}-{$random}";
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_verifier_id');
    }

    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_executor_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(SubmissionDetail::class, 'submission_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SubmissionLog::class, 'submission_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    public function getMainDetail(): ?SubmissionDetail
    {
        return $this->details->first();
    }

    public function getRequestTypeLabelAttribute(): string
    {
        $detail = $this->getMainDetail();
        return match($detail?->request_type) {
            'domain' => 'Domain',
            'hosting' => 'Hosting',
            'vps' => 'VPS',
            default => '-',
        };
    }
}
