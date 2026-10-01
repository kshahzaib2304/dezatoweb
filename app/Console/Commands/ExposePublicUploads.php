<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Shared-hosting friendly alternative to `storage:link`.
 * Copies legacy storage/app/public files into public/storage and ensures the folder exists.
 */
class ExposePublicUploads extends Command
{
    protected $signature = 'uploads:expose {--fresh : Empty public/storage before copying}';

    protected $description = 'Expose uploaded media under /storage without symlink or exec (Hostinger-safe)';

    public function handle(): int
    {
        $target = public_path('storage');
        $legacy = storage_path('app/public');

        if ($this->option('fresh') && is_dir($target)) {
            File::deleteDirectory($target);
        }

        if (! is_dir($target) && ! File::makeDirectory($target, 0755, true)) {
            $this->error('Could not create '.$target);

            return self::FAILURE;
        }

        File::put($target.'/.gitignore', "*\n!.gitignore\n");

        if (is_dir($legacy)) {
            File::copyDirectory($legacy, $target);
            $this->info('Copied files from storage/app/public → public/storage');
        } else {
            $this->comment('No legacy storage/app/public folder to copy.');
        }

        $this->info('Uploads will be served from '.$target);
        $this->line('New uploads go straight there (no php artisan storage:link needed).');

        return self::SUCCESS;
    }
}
