<?php

namespace App\Core;

class Upload
{
    /**
     * Validates an uploaded $_FILES entry against an extension+MIME whitelist and,
     * if it passes, moves it into public/uploads/{subdir}/ under a random filename
     * (the original name is never trusted for the path). Returns the relative path
     * to store in the DB, or null with $error set to a user-facing message.
     */
    public static function store(
        ?array $file,
        string $subdir,
        array $allowedExtensions,
        array $allowedMimeTypes,
        ?string &$error = null
    ): ?string {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Please choose a file to upload.';
            return null;
        }

        $maxMb = (int) Env::get('UPLOAD_MAX_MB', 10);
        if ($file['size'] > $maxMb * 1024 * 1024) {
            $error = "That file is larger than the {$maxMb}MB limit.";
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($file['tmp_name']);

        if (!in_array($ext, $allowedExtensions, true) || !in_array($mime, $allowedMimeTypes, true)) {
            $error = 'That file type is not allowed.';
            return null;
        }

        $uploadDir = self::publicPath($subdir);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $storedName = bin2hex(random_bytes(16)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $storedName)) {
            $error = 'Could not save the uploaded file.';
            return null;
        }

        return "uploads/{$subdir}/{$storedName}";
    }

    /** Deletes a file previously stored via self::store(), given its DB-stored relative path. */
    public static function delete(string $relativePath): void
    {
        $fullPath = __DIR__ . '/../../public/' . $relativePath;
        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    private static function publicPath(string $subdir): string
    {
        return __DIR__ . '/../../public/uploads/' . $subdir;
    }
}
