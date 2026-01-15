<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FilesystemHelper
{
    /**
     * Get the configured disk (local or s3)
     */
    protected static function getDisk(): string
    {
        return config('filesystems.default', 'local');
    }

    /**
     * Store a file and return the path
     */
    public static function setFile($file, string $directory = 'files'): string
    {
        $disk = self::getDisk();
        $path = $file->store($directory, $disk);

        return $path;
    }

    /**
     * Store a file with a custom filename
     */
    public static function setFileAs($file, string $directory = 'files', ?string $filename = null): string
    {
        $disk = self::getDisk();
        $filename = $filename ?? $file->hashName();
        $path = $file->storeAs($directory, $filename, $disk);

        return $path;
    }

    /**
     * Get the full URL for a file path
     */
    public static function getFileUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $disk = self::getDisk();

        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Get a temporary URL for private S3 files
     */
    public static function getTemporaryUrl(?string $path, int $minutes = 60): ?string
    {
        if (empty($path)) {
            return null;
        }

        $disk = self::getDisk();

        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }

        // Only S3 supports temporary URLs
        if ($disk === 's3') {
            return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes($minutes));
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Delete a file from storage
     */
    public static function deleteFile(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        $disk = self::getDisk();

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Check if a file exists
     */
    public static function fileExists(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk(self::getDisk())->exists($path);
    }

    /**
     * Move file from one disk to another (useful for migration)
     */
    public static function moveFileBetweenDisks(string $path, string $fromDisk, string $toDisk): bool
    {
        if (!Storage::disk($fromDisk)->exists($path)) {
            return false;
        }

        $content = Storage::disk($fromDisk)->get($path);
        Storage::disk($toDisk)->put($path, $content);

        return Storage::disk($toDisk)->exists($path);
    }
}
