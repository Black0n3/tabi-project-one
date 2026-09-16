<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class ImageUploads
{
    protected static ?ImageManager $manager = null;

    /**
     * Pretvara uploadanu sliku u WebP (manje datoteke, brže učitavanje, bolje za SEO)
     * i sprema je na dani disk/direktorij. Vraća relativnu putanju spremljene datoteke.
     */
    public static function storeAsWebp(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        int $maxWidth = 2000,
        int $quality = 82,
    ): string {
        $image = static::manager()->read($file->getRealPath())->scaleDown(width: $maxWidth);

        $path = trim($directory, '/').'/'.Str::uuid()->toString().'.webp';

        Storage::disk($disk)->put($path, (string) $image->toWebp($quality));

        return $path;
    }

    protected static function manager(): ImageManager
    {
        return static::$manager ??= ImageManager::gd();
    }
}
