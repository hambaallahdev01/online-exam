<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MediaUploadService
{
    /**
     * Upload file with automatic proportional image resizing (max 1024x1024)
     * and strict PDF size limit (max 5MB). Direct video file uploads are prohibited.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param int $maxWidth
     * @param int $maxHeight
     * @return string Public URL of the uploaded file
     * @throws InvalidArgumentException
     */
    public static function upload(UploadedFile $file, string $folder = 'uploads', int $maxWidth = 1024, int $maxHeight = 1024): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = class_exists('finfo') ? $file->getMimeType() : ($file->getClientMimeType() ?: match($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        });
        $sizeInKb = $file->getSize() / 1024;

        // Prohibit direct video file uploads to save bandwidth & S3 storage
        if (str_starts_with($mime, 'video/') || in_array($extension, ['mp4', 'avi', 'mov', 'mkv', 'webm', '3gp', 'flv'])) {
            throw new InvalidArgumentException('Direct video file uploads are disabled to preserve storage and bandwidth. Please embed YouTube video URLs instead.');
        }

        // PDF size restriction: Max 5MB (5120 KB)
        if ($extension === 'pdf' || $mime === 'application/pdf') {
            if ($sizeInKb > 5120) {
                throw new InvalidArgumentException('PDF document size exceeds the maximum limit of 5MB.');
            }
        }

        $filename = Str::random(20) . '.' . $extension;
        $path = rtrim($folder, '/') . '/' . $filename;

        // Check if file is an image (JPEG, PNG, WEBP, GIF)
        if (str_starts_with($mime, 'image/') && extension_loaded('gd') && in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            $resizedBinary = static::resizeImageProportional($file->getRealPath(), $mime, $maxWidth, $maxHeight);
            if ($resizedBinary !== null) {
                return static::saveToDisk($path, $resizedBinary, $file, $folder, $filename);
            }
        }

        // Store PDF or fallback
        return static::saveToDisk($path, null, $file, $folder, $filename);
    }

    /**
     * Store file to configured disk with automatic fallback to local public disk if S3/Guzzle/cURL fails.
     */
    protected static function saveToDisk(string $path, ?string $binary, UploadedFile $file, string $folder, string $filename): string
    {
        $defaultDisk = config('filesystems.default', 'public');

        try {
            if ($binary !== null) {
                Storage::disk($defaultDisk)->put($path, $binary, 'public');
                return Storage::disk($defaultDisk)->url($path);
            }

            $storedPath = $file->storeAs($folder, $filename, [
                'disk' => $defaultDisk,
                'visibility' => 'public',
            ]);

            return Storage::disk($defaultDisk)->url($storedPath);
        } catch (\Throwable $e) {
            Log::error("S3 Upload Failed for disk [{$defaultDisk}]: " . $e->getMessage(), [
                'path' => $path,
                'exception' => $e->getTraceAsString(),
            ]);

            // Fallback to local 'public' disk if S3 / Guzzle / cURL fails on hosting
            if ($defaultDisk !== 'public') {
                if ($binary !== null) {
                    Storage::disk('public')->put($path, $binary, 'public');
                    return Storage::disk('public')->url($path);
                }
                $storedPath = $file->storeAs($folder, $filename, [
                    'disk' => 'public',
                    'visibility' => 'public',
                ]);
                return Storage::disk('public')->url($storedPath);
            }
            throw $e;
        }
    }

    /**
     * Proportional image resizer using native PHP GD library.
     */
    protected static function resizeImageProportional(string $filePath, string $mime, int $maxWidth, int $maxHeight): ?string
    {
        list($origWidth, $origHeight) = @getimagesize($filePath);
        if (!$origWidth || !$origHeight) {
            return null;
        }

        // Calculate proportional dimensions
        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);

        // If original image is already within bounds, don't upscale
        if ($ratio >= 1.0) {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        } else {
            $newWidth = (int) round($origWidth * $ratio);
            $newHeight = (int) round($origHeight * $ratio);
        }

        // Create GD image resource based on mime type
        $srcImg = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($filePath),
            'image/png' => @imagecreatefrompng($filePath),
            'image/webp' => @imagecreatefromwebp($filePath),
            default => null,
        };

        if (!$srcImg) {
            return null;
        }

        // Create canvas for resized image
        $dstImg = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve PNG & WebP transparency
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);
            $transparent = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
            imagefilledrectangle($dstImg, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resample image
        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // Output to memory buffer
        ob_start();
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($dstImg, null, 95); // 95% quality optimization
                break;
            case 'image/png':
                imagepng($dstImg, null, 6); // Compression level 6
                break;
            case 'image/webp':
                imagewebp($dstImg, null, 95); // 95% quality optimization
                break;
        }
        $binary = ob_get_clean();

        // Free GD memory
        imagedestroy($srcImg);
        imagedestroy($dstImg);

        return $binary ?: null;
    }

    /**
     * Delete uploaded file from S3 / Local storage by URL.
     */
    public static function deleteFile(string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        // Parse path relative to storage, e.g. "questions/abc.jpg"
        $parsedUrl = parse_url($url, PHP_URL_PATH);
        if (!$parsedUrl) {
            return false;
        }

        // Strip leading slash or 'storage/' prefix if present
        $path = ltrim($parsedUrl, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        $defaultDisk = config('filesystems.default', 'public');
        $deleted = false;

        try {
            if (Storage::disk($defaultDisk)->exists($path)) {
                $deleted = Storage::disk($defaultDisk)->delete($path);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to delete media from disk [{$defaultDisk}]: " . $e->getMessage());
        }

        // Also check fallback 'public' disk if default disk is not public
        if ($defaultDisk !== 'public') {
            try {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    $deleted = true;
                }
            } catch (\Throwable $e) {
                // Ignore fallback delete errors
            }
        }

        return $deleted;
    }

    /**
     * Extract all image/PDF URLs from HTML content or option JSON array and delete them from storage.
     */
    public static function deleteMediaFromContent(mixed $content): void
    {
        if (empty($content)) {
            return;
        }

        $urls = [];
        $text = is_array($content) || is_object($content) ? json_encode($content, JSON_UNESCAPED_SLASHES) : (string)$content;
        $text = stripslashes($text);

        // 1. Match full http(s) URLs
        preg_match_all('/https?:\/\/[^\s"\'<>]+/i', $text, $matches1);
        if (!empty($matches1[0])) {
            $urls = array_merge($urls, $matches1[0]);
        }

        // 2. Match relative src/href paths (e.g. /storage/questions/xyz.jpg or questions/xyz.jpg)
        preg_match_all('/(?:src|href)=["\']([^"\']+)["\']/i', $text, $matches2);
        if (!empty($matches2[1])) {
            $urls = array_merge($urls, $matches2[1]);
        }

        foreach (array_unique($urls) as $url) {
            $cleanUrl = trim(str_replace(['\\', '"', "'"], '', $url));
            static::deleteFile($cleanUrl);
        }
    }
}
