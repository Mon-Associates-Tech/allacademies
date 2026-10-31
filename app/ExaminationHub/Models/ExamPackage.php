<?php

namespace App\ExaminationHub\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamPackage extends Model
{
    use HasFactory;

    protected $table = 'general_exam_packages';

    protected $fillable = [
        'exam_uuid',
        'revision',
        'payload',
        'checksum',
        'signature',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'revision' => 'integer',
        ];
    }

    /**
     * The user who created this package.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the latest package for a given exam UUID.
     */
    public static function latestForExam(string $exam_uuid): ?self
    {
        return static::where('exam_uuid', $exam_uuid)
            ->orderByDesc('revision')
            ->first();
    }

    /**
     * Get a specific revision for an exam.
     */
    public static function findExamRevision(string $exam_uuid, int $revision): ?self
    {
        return static::where('exam_uuid', $exam_uuid)
            ->where('revision', $revision)
            ->first();
    }

    /**
     * Decompress and decode the payload.
     */
    public function getDecompressedPayload(): ?array
    {
        $payload = $this->payload;
        if (!$payload) {
            return null;
        }

        $decompressed = @gzdecode($payload);
        if ($decompressed === false) {
            return null;
        }

        return json_decode($decompressed, true) ?: null;
    }

    /**
     * Verify the checksum of the payload.
     */
    public function verifyChecksum(): bool
    {
        $payload = $this->payload;
        if (!$payload) {
            return false;
        }

        $decompressed = @gzdecode($payload);
        if ($decompressed === false) {
            return false;
        }

        $calculated = hash('sha256', $decompressed);
        return hash_equals($this->checksum, $calculated);
    }

    /**
     * Verify the Ed25519 signature using the central public key.
     */
    public function verifySignature(string $publicKeyBase64): bool
    {
        $publicKey = base64_decode($publicKeyBase64);
        $signature = base64_decode($this->signature);

        if ($publicKey === false || $signature === false) {
            return false;
        }

        // libsodium signature verification
        try {
            return sodium_crypto_sign_verify_detached(
                $signature,
                $this->checksum,
                $publicKey
            );
        } catch (\Exception $e) {
            return false;
        }
    }
}
