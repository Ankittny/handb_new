<?php

namespace App\Repositories;

use App\Contracts\Repositories\HsnRepositoryInterface;
use App\Http\Requests\Request;
use App\Models\Hsncode;

class HsnRepository implements HsnRepositoryInterface
{
    public function __construct(
       private readonly Hsncode $Hsncode
    )
    {
    }

    public function add(object $request): bool
    {
        $data = $request->only([
            'hsn_code_under_gst',
            'description',
            'tax',
        ]);

        $hsncode = new Hsncode();
        $hsncode->fill($data);
        return $hsncode->save();
    }

    public function update(object $request): bool
    {
        // Extract only the relevant data from the request
        $data = $request->only([
            'id',
            'hsn_code_under_gst',
            'description',
            'tax',
        ]);

        // Find the Hsncode model by ID
        $hsn = Hsncode::find($data['id']);

        if ($hsn) {
            // Update the Hsncode model with the new data
            return $hsn->update([
                'hsn_code_under_gst' => $data['hsn_code_under_gst'],
                'description' => $data['description'],
                'tax' => $data['tax'],
            ]);
        }

        // If no record was found, return false
        return false;
    }

    public function updateData(string $model, string $id, string $lang, string $key, string $value):bool
    {
        $this->Hsncode->updateOrInsert(
            [
                'Hsncodeable_type' => $model,
                'Hsncodeable_id' => $id,
                'locale' => $lang,
                'key' => $key
            ],
            [
                'value' => $value
            ]
        );
        return true;
    }
    public function delete(object $request): bool
    {
        $id = $request->input('hsnId');
        $hsn = Hsncode::find($id); 
       
        if (!$hsn) {
            return false; 
        }

        $hsn->status = 0;
        $updated = $hsn->save(); 
    
        return $updated;
    }
    
}
