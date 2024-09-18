@extends('layouts.front-end.app')

@section('title', translate('all_Categories'))

@push('css_or_js')
    <meta property="og:image" content="{{ $web_config['web_logo']['path'] }}"/>
    <meta property="og:title" content="Categories of {{ $web_config['name']->value }}"/>
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:description"
          content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <meta property="twitter:card" content="{{ $web_config['web_logo']['path'] }}"/>
    <meta property="twitter:title" content="Categories of {{ $web_config['name']->value }}"/>
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:description"
          content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
@endpush

@section('content')

<div class="container mt-5">
    <div class="row">
        <!-- Image on the left side -->
        <div class="col-md-6">
        <img alt="{{ $categoris->name }}" src="{{ getStorageImages(path:$categoris->icon_full_url, type: 'category') }}">
                                               
        </div>
        <!-- Description on the right side -->
        <div class="col-md-6">
            <h2>{{ $categoris->name }}</h2>
            <p>{{ $categoris->description }}</p>
            <!-- Add more content here as needed -->
        </div>
    </div>
    
    <div class="card-body">
    <div class="row flex-between">
        <div class="ms-1">
            <h4 class="text-capitalize font-bold fs-16">Similar products</h4>
        </div>
    </div>

    <div class="row g-3 mt-1">
    @foreach($product as $item)
        <div class="col-xl-2 col-sm-3 col-6">
            <div class="product-single-hover style--card">
                <div class="overflow-hidden position-relative">
                    <div class="inline_product clickable d-flex justify-content-center">
                        <div class="d-flex justify-content-end">
                            <span class="for-discount-value-null"></span>
                        </div>
                        <div class="p-10px pb-0">
                            <a href="{{route('product',$item->slug)}}" class="w-100">
                                <img alt="{{ $item->name }}" src="{{ getStorageImages(path: $item->thumbnail_full_url, type: 'product') }}" />
                            </a>
                            <div class="quick-view">
                                <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:" data-product-id="{{ $item->id }}">
                                    <i class="czi-eye align-middle"></i>
                                </a>
                            </div>
                            @if($item->product_type == 'physical' && $item->current_stock <= 0)
                                <span class="out_fo_stock">{{translate('out_of_stock')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="single-product-details">
                        <div class="text-center">
                            <a href="{{ url('product/' . $item->slug) }}">
                                {{ Str::limit($item->name, 30) }} <!-- Limit the name length -->
                            </a>
                        </div>
                        <div class="justify-content-between text-center">
                        <div class="product-price">
                                @if($item->discount > 0)
                                    <del class="category-single-product-price">
                                        {{ webCurrencyConverter(amount: $item->unit_price) }}
                                    </del>
                                @endif
                                <span class="text-accent text-dark">
                                    {{ webCurrencyConverter(amount:
                                        $item->unit_price-(getProductDiscount(product: $item, price: $item->unit_price))
                                    ) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

</div>

</div>

@endsection

@push('script')
    <script src="{{ asset('public/assets/front-end/js/categories.js') }}"></script>
@endpush
