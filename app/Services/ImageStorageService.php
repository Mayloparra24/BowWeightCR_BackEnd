<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageStorageService
{
    public function __construct(
        private readonly string $disk = 'r2'
    ) {
    }

    /**
     * Sube una imagen al disco configurado y retorna la ruta relativa.
     */
    public function upload(UploadedFile $file, ?string $directory = 'fotografias'): string
    {
        $path = $this->buildPath($file, $directory);

        Storage::disk($this->disk)->put(
            $path,
            file_get_contents($file->getRealPath()),
            [
                'visibility' => 'public',
                'ContentType' => $file->getMimeType(),
            ]
        );

        return $path;
    }

    /**
     * Retorna la URL pública de una imagen almacenada.
     */
    public function url(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Elimina una imagen del disco configurado.
     */
    public function delete(string $path): bool
    {
        if (Storage::disk($this->disk)->missing($path)) {
            return false;
        }

        return Storage::disk($this->disk)->delete($path);
    }

    /**
     * Verifica si una imagen existe en el disco configurado.
     */
    public function exists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    private function buildPath(UploadedFile $file, ?string $directory): string
    {
        $filename = $this->generateFilename($file);
        $basePath = $directory ? rtrim($directory, '/') : 'fotografias';

        return sprintf('%s/%s/%s', $basePath, now()->format('Y/m'), $filename);
    }

    private function generateFilename(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $unique = Str::uuid()->toString();
        $timestamp = now()->format('Ymd_His');

        return sprintf('%s_%s.%s', $timestamp, $unique, $extension);
    }
}
