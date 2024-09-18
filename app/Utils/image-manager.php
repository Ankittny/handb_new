<?php

namespace App\Utils;

use Carbon\Carbon;
use Intervention\Image\Facades\Image;

class ImageManager
{
    /**
     * Upload an image or file
     * @param string $dir
     * @param string $format
     * @param $file
     * @param string $file_type 'image' or 'file'
     * @return string
     */
    public static function upload(string $dir, string $format, $file, string $file_type = 'image'): string
    {
        $basePath = public_path('assets/back-end/' . $dir . '/');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0775, true);
        }

        if ($file !== null) {
            $extension = $file->getClientOriginalExtension();
            $isOriginalImage = in_array($extension, ['gif', 'svg']);
            $fileName = Carbon::now()->toDateString() . "-" . uniqid() . "." . ($isOriginalImage ? $extension : $format);

            if ($isOriginalImage) {
                // Save the original image directly
                $file->move($basePath, $fileName);
            } else {
                // Process and save the image in the specified format
                $image = Image::make($file)->encode($format, 90);
                $image->save($basePath . $fileName);
            }
        } else {
            $fileName = 'def.webp';
        }

        return $fileName;
    }

    /**
     * Upload a file
     * @param string $dir
     * @param string $format
     * @param $file
     * @return string
     */
    public static function fileUpload(string $dir, string $format, $file = null): string
    {
        $basePath = public_path('assets/back-end/' . $dir . '/');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0775, true);
        }

        if ($file !== null) {
            $fileName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            file_put_contents($basePath . $fileName, file_get_contents($file));
        } else {
            $fileName = 'def.png';
        }

        return $fileName;
    }

    /**
     * Update an image or file
     * @param string $dir
     * @param string $oldImage
     * @param string $format
     * @param $file
     * @param string $fileType 'image' or 'file'
     * @return string
     */
    public static function update(string $dir, string $oldImage, string $format, $file, string $fileType = 'image'): string
    {
        $basePath = public_path('assets/back-end/' . $dir . '/');
        $oldImagePath = $basePath . $oldImage;

        if (!empty($oldImage) && file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }

        return $fileType === 'file' ? self::fileUpload($dir, $format, $file) : self::upload($dir, $format, $file);
    }

    /**
     * Delete a file
     * @param string $filePath
     * @return array
     */
    public static function delete(string $filePath): array
    {
        $fullPath = public_path($filePath);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        return [
            'success' => 1,
            'message' => 'Removed successfully!'
        ];
    }
}
