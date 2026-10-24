@if($products->count())
    <ul class="list-group">
        @foreach($products as $product)
            @php
                $name = session('language') === 'bangla' ? ($product->name_bn ?: $product->name_en) : $product->name_en;
                $thumbnail = $product->product_thumbnail ?: 'upload/no_image.jpg';
                $price = (float) $product->storefrontPrice();
                $regularPrice = (float) $product->regular_price;
            @endphp
            <li class="list-group-item d-flex align-items-center">
                <a href="{{ route('product.details', $product->slug) }}" 
                   class="d-flex align-items-center text-decoration-none text-dark w-100">

                    {{-- Product Image --}}
                   <img src="{{ asset($thumbnail) }}"
                         alt="{{ $name }}"
                         style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">

                    {{-- Product Info --}}
                    <div>
                        <div><strong>{{ $name }}</strong></div>
                        <div class="text-muted">
                            @if($regularPrice > $price)
                                <del class="me-1">৳ {{ number_format($regularPrice, 2) }}</del>
                            @endif
                            <strong class="text-dark">৳ {{ number_format($price, 2) }}</strong>
                        </div>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
@else
    <p class="p-2">No products found.</p>
@endif
