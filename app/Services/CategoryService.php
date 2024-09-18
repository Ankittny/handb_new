<?php

namespace App\Services;

use App\Traits\FileManagerTrait;
use Illuminate\Support\Str;

class CategoryService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        $storage = config('filesystems.disks.default') ?? 'public';
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'description' => $request['description'] ?? null,
            'slug' => Str::slug($request['name'][array_search('en', $request['lang'])]),
            'icon' => $this->upload('category/', 'webp', $request->file('image')),
            'icon_storage_type' => $request->has('image') ? $storage : null,
            'parent_id' => $request->get('parent_id', 0),
            'position' => $request['position'],
            'priority' => $request['priority'],
        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
        $storage = config('filesystems.disks.default') ?? 'public';
        // dd( $request->file('image'));
        $image = $request->file('image') ? $this->update('category/', $data['image'], 'webp', $request->file('image')) : $data['icon'];
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'description' => $request['description'] ?? null,
            'slug' => Str::slug($request['name'][array_search('en', $request['lang'])]),
            'icon' => $image,
            'icon_storage_type' => $request->has('image') ? $storage : $data['icon_storage_type'],
            'priority' => $request['priority'],
        ];
    }

    public function getSelectOptionHtml(object $data): string
    {
        $output = '<option value="" disabled selected>' . (translate('select_sub_category')) . '</option>';
        foreach ($data as $row) {
            $output .= '<option value="' . $row->id . '">' . $row->defaultName . '</option>';
        }
        return $output;
    }

    public function deleteImages(object $data): bool
    {
        if ($data->childes) {
            foreach ($data->childes as $child) {
                if ($child->childes) {
                    foreach ($child->childes as $item) {
                        if ($item['icon']) {
                            $this->delete('category/' . $item['icon']);
                        }
                    }
                }
                if ($child['icon']) {
                    $this->delete('category/' . $child['icon']);
                }
            }
        }
        if ($data['icon']) {
            $this->delete('category/' . $data['icon']);
        }
        return true;
    }
}