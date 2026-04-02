<?php

namespace App\AppMain\Core\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Upload a file to a specific path.
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string|null $disk
     * @return string
     */
    public function upload(UploadedFile $file, string $path, string $disk = null): string
    {
        $disk = $disk ?: config('filesystems.default');
        return $file->store($path, $disk);
    }

    /**
     * Delete a file from a specific path.
     *
     * @param string|null $path
     * @param string|null $disk
     * @return bool
     */
    public function delete(?string $path, string $disk = null): bool
    {
        if (!$path) {
            return false;
        }

        $disk = $disk ?: config('filesystems.default');

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }
}
