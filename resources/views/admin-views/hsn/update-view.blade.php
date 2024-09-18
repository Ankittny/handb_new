@extends('layouts.back-end.app')
@section('title', translate('order_List'))

@section('content')
    <div class="content container-fluid">
        <div>
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <h2 class="h1 mb-0">
                    <img src="{{dynamicAsset(path: 'public/assets/back-end/img/all-orders.png')}}" class="mb-1 mr-1" alt="">
                    
                    {{translate('Update HSN Master')}}
                </h2>
                <span class="badge badge-soft-dark radius-50 fz-14"></span>
            </div>
            <div class="card">
            <div class="card-body text-start">
                        <form action="{{route('admin.hsn.updatedata')}}" method="post">
                        @csrf
                                        <span class="nav-link form-system-language-tab cursor-pointer active" id="en-link">
                                            English(EN)
                                        </span>
                                    </li>
                                                            </ul>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div>
                                                <div class="form-group  form-system-language-form" id="en-form">
                                                <label class="title-color">Hsn code under gst<span class="text-danger">*</span> (EN)</label>
                                                <input type="hidden" name="id" value="{{ $data->id }}">
                                                <input type="text" name="hsn_code_under_gst" class="form-control" value="{{ $data->hsn_code_under_gst }}" placeholder="hsn_code_under_gst" required="">
                                    </div>
            
                                    </div>
                                  
                                    <div class="form-group">
                                        <label class="title-color" for="priority">Description
                                            <span>
                                            <i class="tio-info-outined" data-toggle="tooltip" data-placement="top" title="" data-original-title="Description"></i>
                                            </span>
                                        </label>

                                         <textarea name="description" id="" value="{{ $data->description }}" cols="" rows="" class="form-control">{{ $data->description }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="title-color" for="priority">Tax
                                           
                                        </label>
                                        <input type="text" name="tax" class="form-control" value="{{ $data->tax }}" placeholder="Tax" required="">
                                    </div>
                                    <div class="from_part_2">
                                        <!-- <label class="title-color">Category Logo</label>
                                        <span class="text-info"><span class="text-danger">*</span> Ratio 1:1 (500 x 500 px)</span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="category-image" class="custom-file-input image-preview-before-upload" data-preview="#viewer" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required="">
                                            <label class="custom-file-label" for="category-image">Choose File</label>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-4 mt-lg-0 from_part_2">
                                    <div class="form-group">
                                        <div class="text-center mx-auto">
                                            <!-- <img class="upload-img-view" id="viewer" alt="" src="http://127.0.0.1:8000/assets/back-end/img/image-place-holder.png"> -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <button type="reset" id="reset" class="btn btn-secondary">Reset</button>
                                <button type="submit" class="btn btn--primary">Submit</button>
                            </div>
                        </form>
                    </div>
            </div>
            <br>
            
            
        </div>
    </div>

    
@endsection

@push('script_2')
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/admin/order.js')}}"></script>
@endpush
