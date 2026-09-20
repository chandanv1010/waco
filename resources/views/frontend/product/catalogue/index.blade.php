@extends('frontend.homepage.layout')

@section('content')
<main class="waco">

    @include('frontend.component.waco-banner', [
        'title' => $tieuDe,
        'crumbs' => $danhMucHienTai
            ? ['Sản phẩm' => write_url('san-pham', true, true), $tieuDe => '']
            : ['Sản phẩm' => ''],
    ])

    <section class="waco__section">
        <div class="waco-shop__inner">

            {{-- COT TRAI: danh muc + o tu van ----------------------------- --}}
            <aside class="waco-shop__aside">
                <div class="waco-catnav">
                    <h2 class="waco-catnav__heading">Danh mục sản phẩm</h2>
                    <ul class="waco-catnav__list">
                        <li>
                            <a href="{{ write_url('san-pham', true, true) }}"
                               class="waco-catnav__link{{ $danhMucHienTai ? '' : ' is-active' }}">
                                <span>Tất cả sản phẩm</span>
                            </a>
                        </li>
                        @foreach($danhMuc as $cat)
                            <li>
                                <a href="{{ write_url($cat->canonical, true, true) }}"
                                   class="waco-catnav__link{{ (int) $cat->id === $danhMucHienTai ? ' is-active' : '' }}">
                                    @if(!empty($cat->icon))
                                        <img src="{{ $cat->icon }}" alt="" aria-hidden="true" loading="lazy">
                                    @endif
                                    <span>{{ $cat->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- O tu van: noi dung lay tu bang introduces de admin sua duoc. --}}
                @if(!empty($intro['product_sidebar_heading']))
                    <div class="waco-advisor">
                        <h3 class="waco-advisor__heading">{{ $intro['product_sidebar_heading'] }}</h3>
                        <p class="waco-advisor__description">{{ $intro['product_sidebar_description'] ?? '' }}</p>

                        @php
                            // Moi loi ich mot dong trong bang introduces -> tach ra
                            // thanh danh sach co dau tich, admin sua khong can code.
                            $loiIch = array_values(array_filter(array_map(
                                'trim',
                                preg_split('/\r\n|\r|\n/', strip_tags($intro['product_sidebar_benefits'] ?? ''))
                            )));
                        @endphp

                        @if(count($loiIch))
                            <ul class="waco-advisor__list">
                                @foreach($loiIch as $dong)
                                    <li>{{ $dong }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <button type="button" class="waco-btn waco-btn--sm waco-advisor__cta"
                                data-waco-consult="consult">
                            {{ $intro['product_sidebar_cta'] ?? 'Nhận tư vấn ngay' }}
                            @include('frontend.component.waco-icon', ['name' => 'arrow-right'])
                        </button>
                    </div>
                @endif
            </aside>

            {{-- COT PHAI: danh sach san pham ------------------------------ --}}
            <div class="waco-shop__main">
                <div class="waco-shop__head">
                    <div>
                        <h2 class="waco-shop__title">{{ $tieuDe }}</h2>
                        @php
                            // Danh muc co mo ta rieng thi dung; trang "tat ca san
                            // pham" khong co nen lui ve doan gioi thieu chung.
                            $doanMoTa = $moTa ?: ($intro['ecosystem_description'] ?? '');
                        @endphp
                        @if(!empty($doanMoTa))
                            <p class="waco-shop__description">{{ strip_tags($doanMoTa) }}</p>
                        @endif
                    </div>

                    {{-- Sap xep: gui lai chinh duong dan hien tai kem tham so sort,
                         ProductService da doc san tham so nay. --}}
                    <form method="GET" class="waco-shop__sort">
                        <label for="sort" class="visually-hidden">Sắp xếp</label>
                        <select name="sort" id="sort" onchange="this.form.submit()">
                            @foreach([
                                'newest' => 'Mới nhất',
                                'oldest' => 'Cũ nhất',
                            ] as $giaTri => $nhan)
                                <option value="{{ $giaTri }}" @selected(request('sort', 'newest') === $giaTri)>
                                    {{ $nhan }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if($products->count())
                    <div class="waco-product__grid">
                        @foreach($products as $product)
                            @include('frontend.component.waco-product-card', ['product' => $product])
                        @endforeach
                    </div>
                @else
                    <p class="waco-empty">Chưa có sản phẩm nào trong danh mục này.</p>
                @endif

                @include('frontend.component.waco-pagination', ['model' => $products])
            </div>

        </div>
    </section>

    @include('frontend.component.waco-network')

</main>
@endsection
