<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;

trait FileManagerTrait
{
    /**
     * Upload method working for image
     * @param string $dir
     * @param string $format
     * @param $image
     * @return string
     */
    protected function upload(string $dir, string $format, $image = null): string
    {
        $basePath = public_path('assets/back-end/' . $dir . '/');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0775, true);
        }
        if (!is_null($image)) {
            $isOriginalImage = in_array($image->getClientOriginalExtension(), ['gif', 'svg']);
            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . ($isOriginalImage ? $image->getClientOriginalExtension() : $format);

            if ($isOriginalImage) {
                $image->move($basePath, $imageName);
            } else {
                $image_webp = Image::make($image)->encode($format);
                $image_webp->save($basePath . $imageName);
            }
        } else {
            $imageName = 'def.png';
        }

        return $imageName;
    }

    /**
     * @param string $dir
     * @param string $format
     * @param $file
     * @return string
     */
    public function fileUpload(string $dir, string $format, $file = null): string
    {
        $basePath = public_path('assets/back-end/' . $dir . '/');

        if (!is_null($file)) {
            $fileName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            file_put_contents($basePath . $fileName, file_get_contents($file));
        } else {
            $fileName = 'def.png';
        }

        return $fileName;
    }

    /**
     * @param string $dir
     * @param $oldImage
     * @param string $format
     * @param $image
     * @param string $fileType image/file
     * @return string
     */
    public function update(string $dir, $oldImage, string $format, $image, string $fileType = 'image'): string
    {
        $basePath = public_path('assets/back-end/' . $dir . '/');
        $oldImagePath = $basePath . $oldImage;

        if (!empty($oldImage) && file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }

        return $fileType === 'file' ? $this->fileUpload($dir, $format, $image) : $this->upload($dir, $format, $image);
    }

    /**
     * @param string $filePath
     * @return array
     */
    protected function delete(string $filePath): array
    {
        $fullPath = public_path($filePath);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        return [
            'success' => 1,
            'message' => 'Removed successfully'
        ];
    }

    public function setStorageConnectionEnvironment(): void
    {
        // This method is no longer necessary if using direct file system paths
    }
}
