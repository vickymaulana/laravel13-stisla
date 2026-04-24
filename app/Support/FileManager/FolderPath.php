<?php

namespace App\Support\FileManager;

use Illuminate\Validation\ValidationException;

class FolderPath
{
    /**
     * Normalize user-provided folder path to reduce traversal risk.
     *
     * @throws ValidationException
     */
    public static function normalize(?string $folder): string
    {
        $folder = trim((string) $folder);

        if ($folder === '' || $folder === '/') {
            return '/';
        }

        $folder = str_replace('\\', '/', $folder);
        $parts = array_filter(explode('/', $folder), static fn ($part) => $part !== '' && $part !== '.');

        foreach ($parts as $part) {
            if ($part === '..') {
                throw ValidationException::withMessages([
                    'folder' => 'Invalid folder path.',
                ]);
            }
        }

        return '/'.implode('/', $parts);
    }

    /**
     * Join and normalize a parent folder with a child segment.
     *
     * @throws ValidationException
     */
    public static function join(?string $parent, string $child): string
    {
        return self::normalize(trim(self::normalize($parent).'/'.$child));
    }
}
