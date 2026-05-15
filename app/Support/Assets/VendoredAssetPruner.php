<?php

namespace App\Support\Assets;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class VendoredAssetPruner
{
    /**
     * Directories that are useful for package development but not for serving assets.
     *
     * @var array<int, string>
     */
    private const DEVELOPMENT_DIRECTORIES = [
        '.github',
        'builder',
        'create',
        'docs',
        'docs_src',
        'example',
        'examples',
        'grunt',
        'less',
        'node_modules',
        'sass',
        'scss',
        'src',
        'test',
        'tests',
        'ts3.1-typings',
    ];

    /**
     * Package metadata and build artifacts that should not be shipped in public assets.
     *
     * @var array<int, string>
     */
    private const DEVELOPMENT_FILENAMES = [
        'authors.txt',
        'bower.json',
        'changelog.md',
        'component.json',
        'composer.json',
        'gruntfile.js',
        'karma.conf.js',
        'package.json',
        'readme.md',
    ];

    /**
     * Generated or source-only file types that are not needed at runtime.
     *
     * @var array<int, string>
     */
    private const DEVELOPMENT_EXTENSIONS = [
        'hbs',
        'less',
        'map',
        'psd',
        'scss',
        'zip',
    ];

    public function shouldPrune(string $relativePath): bool
    {
        $relativePath = $this->normalizeRelativePath($relativePath);
        $segments = explode('/', $relativePath);

        foreach ($segments as $segment) {
            if (in_array(strtolower($segment), self::DEVELOPMENT_DIRECTORIES, true)) {
                return true;
            }
        }

        $filename = strtolower((string) end($segments));

        if (in_array($filename, self::DEVELOPMENT_FILENAMES, true)) {
            return true;
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return in_array($extension, self::DEVELOPMENT_EXTENSIONS, true);
    }

    /**
     * @return array<int, array{path: string, relative_path: string, bytes: int}>
     */
    public function prunableFiles(string $libraryPath): array
    {
        $libraryPath = rtrim(str_replace('\\', '/', realpath($libraryPath) ?: $libraryPath), '/');
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($libraryPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $files = [];

        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            if (! $item->isFile()) {
                continue;
            }

            $path = str_replace('\\', '/', $item->getPathname());
            $relativePath = ltrim(substr($path, strlen($libraryPath)), '/');

            if (! $this->shouldPrune($relativePath)) {
                continue;
            }

            $files[] = [
                'path' => $item->getPathname(),
                'relative_path' => $relativePath,
                'bytes' => $item->getSize(),
            ];
        }

        return $files;
    }

    private function normalizeRelativePath(string $relativePath): string
    {
        return trim(str_replace('\\', '/', $relativePath), '/');
    }
}
