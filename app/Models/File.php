<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Uploaded file record.
 *
 * Each file is owned by a user and stored via the `public` disk.
 * Files are organized into user-defined folders and may be flagged
 * as public or private.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $name
 * @property string      $original_name
 * @property string      $path
 * @property string      $mime_type
 * @property int         $size
 * @property string|null $extension
 * @property string      $folder
 * @property string|null $description
 * @property bool        $is_public
 */
class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'original_name',
        'path',
        'mime_type',
        'size',
        'extension',
        'folder',
        'description',
        'is_public',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    /**
     * Get the user that uploaded the file.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get file size in human-readable format.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the public URL for the file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    /**
     * Check if the file is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if the file is a document.
     */
    public function isDocument(): bool
    {
        $docMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return in_array($this->mime_type, $docMimes);
    }

    /**
     * Get the Font Awesome icon class based on file type.
     */
    public function getIconAttribute(): string
    {
        if ($this->isImage()) {
            return 'fa-file-image text-info';
        }

        if ($this->mime_type === 'application/pdf') {
            return 'fa-file-pdf text-danger';
        }

        if (in_array($this->extension, ['doc', 'docx'])) {
            return 'fa-file-word text-primary';
        }

        if (in_array($this->extension, ['xls', 'xlsx'])) {
            return 'fa-file-excel text-success';
        }

        if (in_array($this->extension, ['zip', 'rar', '7z'])) {
            return 'fa-file-archive text-warning';
        }

        if (in_array($this->extension, ['mp4', 'avi', 'mov'])) {
            return 'fa-file-video text-purple';
        }

        if (in_array($this->extension, ['mp3', 'wav', 'ogg'])) {
            return 'fa-file-audio text-orange';
        }

        return 'fa-file text-muted';
    }
}
