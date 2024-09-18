<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\ViewPaths\Admin\Product;
use App\Models\Color;
use App\Traits\FileManagerTrait;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Boolean;
use Rap2hpoutre\FastExcel\FastExcel;
use function React\Promise\all;

class HsnService
{
    use FileManagerTrait;

    public function __construct(private readonly Color $color)
    {
    }

    public function getAddhsnData(object $request, string $addedBy): array
    {   
        return [
            'added_by' => $addedBy,
            'user_id' => $addedBy == 'admin' ? auth('admin')->id() : auth('seller')->id(),
            'hsn_code_under_gst' => $request['hsn_code_under_gst'],
            'description' => $request['description'],
            'tax' => $request['tax']
        ];
    }

    public function getUpdatehsnData(object $request, string $addedBy): array
    {   
        return [
            'added_by' => $addedBy,
            'user_id' => $addedBy == 'admin' ? auth('admin')->id() : auth('seller')->id(),
            'hsn_code_under_gst' => $request['hsn_code_under_gst'],
            'description' => $request['description'],
            'tax' => $request['tax']
        ];
    }
    public function getdeletehsnData(object $request, string $addedBy): array
    {   
        return [
            'added_by' => $addedBy,
            'user_id' => $addedBy == 'admin' ? auth('admin')->id() : auth('seller')->id(),
            'id' => $request['id']
        ];
    }
}
