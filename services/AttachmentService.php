<?php

namespace cinghie\traits\services;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use getID3;

/**
 * Filesystem and media operations used by AttachmentTrait.
 *
 * This class deliberately has no dependency on Yii application/controller state;
 * callers provide paths and configuration explicitly.
 */
final class AttachmentService
{
    public function deleteFile($basePath, $filename)
    {
        if (empty($filename) || basename($filename) !== $filename) {
            return false;
        }

        $baseRealPath = realpath($basePath);
        if ($baseRealPath === false || !is_dir($baseRealPath)) {
            return false;
        }

        $fileRealPath = realpath($baseRealPath . DIRECTORY_SEPARATOR . $filename);
        if ($fileRealPath === false || !is_file($fileRealPath)) {
            return false;
        }

        $basePrefix = rtrim($baseRealPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (strncmp($fileRealPath, $basePrefix, strlen($basePrefix)) !== 0) {
            return false;
        }

        return unlink($fileRealPath);
    }

    public function getID3Info($attachPath)
    {
        $getID3 = new getID3();
        return $getID3->analyze($attachPath);
    }

    public function getVideoDuration($attachPath)
    {
        $fileInfo = $this->getID3Info($attachPath);
        if (isset($fileInfo['video'], $fileInfo['mime_type']) && strpos($fileInfo['mime_type'], 'video') !== false) {
            return $fileInfo['playtime_string'];
        }

        return null;
    }

    public function createVideoThumb($attachPath, $sec = 3, array $ffmpegOptions = [])
    {
        $fileInfo = $this->getID3Info($attachPath);
        if (!isset($fileInfo['video'], $fileInfo['mime_type']) || strpos($fileInfo['mime_type'], 'video') === false) {
            return null;
        }

        $ffmpeg = FFMpeg::create($ffmpegOptions);
        $video = $ffmpeg->open($attachPath);

        return $video->frame(TimeCode::fromSeconds($sec));
    }

    public function formatFileSize($size, $precision = 2)
    {
        $i = 0;
        $step = 1024;
        $units = ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];

        while (($size / $step) > 0.9 && $i < count($units) - 1) {
            $size /= $step;
            $i++;
        }

        return round($size, $precision).' '.$units[$i];
    }

    public function purgeAttachmentName($attachName)
    {
        return str_replace(["/'/", '’', '"', ':', ';', ',', '.', ' ', '__'], '_', $attachName);
    }

    /**
     * Generate a non-predictable filename while preserving the public legacy
     * method name.
     */
    public function generateMd5FileName($filename, $extension)
    {
        unset($filename); // Kept in the signature for backwards compatibility.
        $extension = ltrim((string)$extension, '.');
        $randomName = bin2hex(random_bytes(16));

        return $extension === '' ? $randomName : $randomName . '.' . $extension;
    }
}
