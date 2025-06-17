<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use SolutionForest\InspireCms\Helpers\FileHelper;
use SolutionForest\InspireCms\Helpers\TemplateHelper;

class RestoreSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:restore-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the system to its initial state.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Restoring the system to its initial state...');
        $this->restoreDatabase();
        $this->removeMediaFiles();
        $this->restoreMediaFiles();
        $this->restoreTemplates();
        Artisan::call('optimize:clear');
    }

    private function restoreDatabase()
    {
        $from = database_path('database.sqlite.example');
        $to = database_path('database.sqlite');
        if (file_exists($to)) {
            // Copy and override the database.sqlite.example file to database.sqlite
            copy($from, $to);
        }
    }

    private function removeMediaFiles()
    {
        $basicExcludedFiles = [
            '.gitignore', '.gitattributes',
        ];
        $disks = [
            'public' => [],
            'local' => [],
        ];

        foreach ($disks as $disk => $excludedFiles) {

            try {

                $fs = Storage::disk($disk);
                
                $filesToExclude = array_merge($basicExcludedFiles, $excludedFiles);

                collect($fs->files())
                    ->where(fn ($file) => ! in_array($file, $filesToExclude))
                    ->each(fn ($file) => $fs->delete($file));

                collect($fs->directories())
                    ->each(fn ($directory) => $fs->deleteDirectory($directory));

            } catch (\Throwable $th) {
                // Avoid disk not configured error
            }
        }
    }

    private function restoreMediaFiles()
    {
        $directories = [
            storage_path('media-library/backup') => storage_path('app/public'),
        ];

        foreach ($directories as $from => $to) {
            if (! is_dir($from) && ! is_dir($to)) {
                continue;
            }

            FileHelper::copyDirectory($from, $to);
        }
    }

    private function restoreTemplates()
    {
        $themedTemplatesDir = TemplateHelper::getDirectoryForThemedComponents();
        $excludedDirectories = TemplateHelper::getDefaultTemplateThemes();

        $directories = scandir($themedTemplatesDir);
        if (! $directories) {
            return;
        }
        foreach ($directories as $directory) {
            try {
                if (in_array($directory, ['.', '..']) || in_array($directory, $excludedDirectories)) {
                    continue;
                }
                
                // Delete the directory
                File::deleteDirectory($themedTemplatesDir . '/' . $directory);

            } catch (\Throwable $th) {
                //
            }
        }
    }
}
