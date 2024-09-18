<?php

namespace App\Http\Controllers\Admin\Hsn;
use App\Enums\ViewPaths\Admin\Hsn;
use App\Contracts\Repositories\HsnRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\HsnRequest;
use App\Services\HsnService;
use Brian2694\Toastr\Facades\Toastr;

use App\Models\Hsncode;


class HsnController extends Controller
{
    public function __construct(
        private readonly HsnRepositoryInterface             $hsnRepo,
        private readonly HsnService                         $hsnService,
    )
    {
    } 
    
    public function list(Request $request){
        $searchValue = $request->input('searchValue');
        $query = Hsncode::select('id', 'hsn_code_under_gst', 'description', 'tax', 'created_at', 'updated_at')
                          ->where('status', 1);
        if (!empty($searchValue)) {
        $query->where(function($q) use ($searchValue) {
            $q->where('hsn_code_under_gst', 'LIKE', "%{$searchValue}%")
                ->orWhere('description', 'LIKE', "%{$searchValue}%");
        });
    }
        $hsnCode = $query->paginate(8);
        return view(Hsn::LIST[VIEW], compact('hsnCode'));
    }

    public function add(HsnRequest $request, HsnService $hsnservice)
    {
        $dataArray = $hsnservice->getAddhsnData(request: $request, addedBy: 'admin');
        if($this->hsnRepo->add(request: $request)){
            Toastr::success(translate('hsn_added_successfully'));
            return redirect()->route('admin.hsn.list');
        }
        
    }

    public function updateview(Request $request){
        $data  = Hsncode::where('id',$request->id)->first();
        return view(Hsn::UPDATE[VIEW], compact('data'));
    }

    public function updatedata(Request $request, HsnService $hsnservice){
        $dataArray = $hsnservice->getUpdatehsnData(request: $request, addedBy: 'admin');
        if($this->hsnRepo->update(request: $request)){
            Toastr::success(translate('hsn_updated_successfully'));
            return redirect()->route('admin.hsn.list');
        }
    } 

    public function delete(Request $request, HsnService $hsnservice)
    {
        $dataArray = $hsnservice->getdeletehsnData(request: $request, addedBy: 'admin');
        if($this->hsnRepo->delete(request: $request)){
            Toastr::success(translate('hsn_deleted_successfully'));
            return redirect()->route('admin.hsn.list');
        }
    }
    
}
