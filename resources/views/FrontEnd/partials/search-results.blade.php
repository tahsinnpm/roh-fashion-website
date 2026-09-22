@if($products->count())
    <ul class="list-group">
        @foreach($products as $product)
            <li class="list-group-item d-flex align-items-center">
                <a href="{{ route('product.details', $product->slug) }}" 
                   class="d-flex align-items-center text-decoration-none text-dark w-100">

                    {{-- Product Image --}}
                   <img src="{{ asset($product->product_thumbnail) }}" 
                         alt="{{ $product->name_en }}" 
                         style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">

                    {{-- Product Info --}}
                    <div>
                        <div><strong>{{ $product->name_en }}</strong></div>
                        <div class="text-muted">৳ {{ number_format($product->regular_price, 2) }}</div>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
@else
    <p class="p-2">No products found.</p>
@endif
