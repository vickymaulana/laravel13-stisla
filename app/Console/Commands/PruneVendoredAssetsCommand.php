<?php

namespace App\Console\Commands;

use App\Support\Assets\VendoredAssetPruner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PruneVendoredAssetsCommand extends Command
{
    protected $signature = 'assets:prune-vendored {--dry-run : Show files that would be removed without deleting them}';

    protected $description = 'Remove development artifacts from vendored public assets.';

    public function handle(VendoredAssetPruner $pruner): int
    {
        $libraryPath = public_path('library');

        if (! File::isDirectory($libraryPath)) {
            $this->warn('public/library does not exist.');

            return self::SUCCESS;
        }

        $files = $pruner->prunableFiles($libraryPath);
        $bytes = array_sum(array_column($files, 'bytes'));
        $dryRun = (bool) $this->option('dry-run');

        if ($files === []) {
            $this->info('No vendored asset development artifacts found.');

            return self::SUCCESS;
        }

        foreach ($files as $file) {
            if ($dryRun) {
                $this->line($file['relative_path']);

                continue;
            }

            File::delete($file['path']);
        }

        if (! $dryRun) {
            $this->deleteEmptyDirectories($libraryPath);
        }

        $action = $dryRun ? 'Would remove' : 'Removed';
        $this->info(sprintf(
            '%s %d file(s), freeing %s.',
            $action,
            count($files),
            $this->formatBytes($bytes)
        ));

        return self::SUCCESS;
    }

    private function deleteEmptyDirectories(string $path): void
    {
        $directories = File::directories($path);

        foreach ($directories as $directory) {
            $this->deleteEmptyDirectories($directory);

            if (File::files($directory) === [] && File::directories($directory) === []) {
                File::deleteDirectory($directory);
            }
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / 1024 / 1024, 2).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }
}
