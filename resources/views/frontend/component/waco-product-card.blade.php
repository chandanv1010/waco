@php
    /**
     * The san pham.
     *
     * Theo ban thiet ke: NEN PHU CA THE (chuyen sac xanh rat nhat tu tren xuong),
     * anh nam trong cung nen do chu khong co khung rieng. Tieu de la ma may, ben
     * duoi la vai thong so gach dau dong, cuoi cung la hai nut.
     *
     * Mo ta ngan luu moi thong so mot dong. Admin chi can go moi dong mot y,
     * khong phai go the HTML - text_lines() lo phan con lai du o quan tri go
     * chu thuan hay CKEditor luu xuong thanh HTML.
     */
    $url = write_url($product->canonical, true, true);

    $thongSo = text_lines($product->description);
@endphp

<article class="waco-product-card">
    <a href="{{ $url }}" class="waco-product-card__media" title="{{ $product->name }}">
        @if(!empty($product->image))
            <img src="{{ $product->image }}" alt="{{ $product->name }}"
                 width="220" height="220" loading="lazy" decoding="async">
        @endif
    </a>

    <div class="waco-product-card__body">
        <h3 class="waco-product-card__title">
            <a href="{{ $url }}">{{ $product->code ?: $product->name }}</a>
        </h3>

        @if(count($thongSo))
            <ul class="waco-product-card__specs">
                @foreach(array_slice($thongSo, 0, 3) as $dong)
                    <li>{{ $dong }}</li>
                @endforeach
            </ul>
        @endif

        <div class="waco-product-card__actions">
            <a href="{{ $url }}" class="waco-product-card__btn">Xem chi tiết</a>
            <button type="button"
                    class="waco-product-card__btn waco-product-card__btn--primary"
                    data-waco-consult="consult"
                    data-waco-consult-product="{{ $product->code ?: $product->name }}">Nhận tư vấn</button>
        </div>
    </div>
</article>
