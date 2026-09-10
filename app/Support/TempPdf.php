<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Short-lived, on-disk storage for generated PDFs used by the "print via browser
 * dialog" flow — files are never web-accessible directly, only through
 * TempPdfController, which enforces the expiry below.
 */
class TempPdf
{
    protected const DIR = 'temp-pdfs';
    protected const TTL_MINUTES = 60;

    public static function store(string $contents): string
    {
        static::sweep();

        $token = (string) Str::uuid();

        Storage::disk('local')->put(static::DIR . "/{$token}.pdf", $contents);

        return $token;
    }

    public static function retrieve(string $token): ?string
    {
        $disk = Storage::disk('local');
        $path = static::DIR . "/{$token}.pdf";

        if (! $disk->exists($path)) {
            return null;
        }

        if ($disk->lastModified($path) < static::cutoff()) {
            $disk->delete($path);

            return null;
        }

        return $disk->get($path);
    }

    public static function sweep(): void
    {
        $disk = Storage::disk('local');
        $cutoff = static::cutoff();

        foreach ($disk->files(static::DIR) as $file) {
            if ($disk->lastModified($file) < $cutoff) {
                $disk->delete($file);
            }
        }
    }

    protected static function cutoff(): int
    {
        return now()->subMinutes(static::TTL_MINUTES)->timestamp;
    }
}
