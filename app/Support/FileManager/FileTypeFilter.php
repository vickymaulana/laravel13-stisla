<?php

namespace App\Support\FileManager;

use App\Models\File;
use Illuminate\Database\Eloquent\Builder;

class FileTypeFilter
{
    /**
     * @return array<int, string>
     */
    public static function documentMimeTypes(): array
    {
        return [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
    }

    /**
     * @param  Builder<File>  $query
     * @return Builder<File>
     */
    public static function apply(Builder $query, ?string $type): Builder
    {
        return match ($type) {
            'image' => $query->where('mime_type', 'like', 'image/%'),
            'document' => $query->whereIn('mime_type', self::documentMimeTypes()),
            default => $query,
        };
    }
}
