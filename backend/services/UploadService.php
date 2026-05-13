<?php

declare(strict_types=1);

class UploadService
{
    /** @return array{path:string, public_url:string} */
    public static function storeAvatar(int $userId, array $file, array $config): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            Response::error('Upload failed', 400);
        }
        if (($file['size'] ?? 0) > ($config['upload_max_bytes'] ?? 2_097_152)) {
            Response::error('File too large', 413);
        }
        $tmp = (string) $file['tmp_name'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp) ?: '';
        $allowed = $config['upload_allowed_mimes'] ?? [];
        if (!in_array($mime, $allowed, true)) {
            Response::error('Invalid image type', 415);
        }
        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $ext = $extMap[$mime] ?? '';
        if ($ext === '') {
            Response::error('Invalid image type', 415);
        }
        $dir = dirname(__DIR__) . '/uploads/avatars';
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            Response::error('Storage error', 500);
        }
        $basename = $userId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = $dir . '/' . $basename;
        if (!move_uploaded_file($tmp, $dest)) {
            Response::error('Could not save file', 500);
        }
        $relative = 'backend/uploads/avatars/' . $basename;
        $url = ironpulse_upload_url($relative);
        return ['path' => $relative, 'public_url' => $url];
    }
}
