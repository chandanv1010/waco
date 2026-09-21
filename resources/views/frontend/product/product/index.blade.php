@extends('frontend.homepage.layout')

@section('content')
@php
    $tenDanhMuc = $productCatalogue->name ?? 'Sản phẩm';
    $urlDanhMuc = !empty($productCatalogue->canonical) ? write_url($productCatalogue->canonical, true, true) : '';

    // Ten san pham la tieu de chinh, ma san pham la thong tin phu.
    $tenHienThi = $product->name ?: $product->code;
    // Chi hien dong "Ma san pham" khi that su co ma va ma khac ten - tranh
    // lap lai y het tieu de ngay ben duoi no.
    $maSanPham = ($product->code && $product->code !== $product->name) ? $product->code : '';

    // Anh chinh dung dau, sau do la cac anh trong album.
    $anh = array_values(array_filter(array_merge([$product->image], $album)));

    // Quan tri dan link YouTube vao o "Video YouTube" trong module San pham.
    $maVideo = youtube_id($product->iframe ?? '');

    // Mo ta ngan luu moi y mot dong -> tach ra thanh danh sach gach dau dong.
    $diemManh = array_values(array_filter(array_map(
        'trim',
        preg_split('/\r\n|\r|\n/', strip_tags($product->description ?? ''))
    )));

    // Thong so ky thuat: moi dong mot dong "Ten: Gia tri" -> bang hai cot.
    $thongSo = [];
    foreach (preg_split('/\r\n|\r|\n/', (string) ($product->specification ?? '')) as $dong) {
        $dong = trim(strip_tags($dong));
        if ($dong === '') {
            continue;
        }
        $phan = explode(':', $dong, 2);
        $thongSo[] = count($phan) === 2
            ? ['ten' => trim($phan[0]), 'giaTri' => trim($phan[1])]
            : ['ten' => $dong, 'giaTri' => ''];
    }
@endphp

<main class="waco">

    {{-- Tieu de banner lay theo danh muc cua san pham chu khong ghi cung
         "Chi tiet san pham" - vao may loc nuoc thi phai thay "May loc nuoc". --}}
    @include('frontend.component.waco-banner', [
        'title' => $tenDanhMuc,
        'crumbs' => [
            'Sản phẩm' => write_url('san-pham', true, true),
            $tenDanhMuc => $urlDanhMuc,
            $tenHienThi => '',
        ],
    ])

    <section class="waco__section waco-detail">
        <div class="waco-detail__inner">

            {{-- THU VIEN ANH: dai anh nho CHAY DOC ben trai, khung lon ben phai.
                 Muc cuoi cung la video YouTube neu quan tri co dan link. --}}
            <div class="waco-gallery">
                @if(count($anh) > 1 || $maVideo)
                    <div class="waco-gallery__rail">
                        <button type="button" class="waco-gallery__nav waco-gallery__nav--prev"
                                aria-label="Ảnh trước">&#8249;</button>

                        <div class="waco-gallery__thumbs swiper">
                            <div class="swiper-wrapper">
                                @foreach($anh as $i => $duongDan)
                                    <div class="swiper-slide">
                                        <button type="button"
                                                class="waco-gallery__thumb{{ $i === 0 ? ' is-active' : '' }}"
                                                data-waco-gallery-image="{{ $duongDan }}"
                                                aria-label="Xem ảnh {{ $i + 1 }}">
                                            <img src="{{ $duongDan }}" alt="" loading="lazy">
                                        </button>
                                    </div>
                                @endforeach

                                @if($maVideo)
                                    <div class="swiper-slide">
                                        <button type="button" class="waco-gallery__thumb waco-gallery__thumb--video"
                                                data-waco-gallery-video="{{ $maVideo }}"
                                                aria-label="Xem video sản phẩm">
                                            <span class="waco-gallery__play" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <button type="button" class="waco-gallery__nav waco-gallery__nav--next"
                                aria-label="Ảnh sau">&#8250;</button>
                    </div>
                @endif

                <div class="waco-gallery__main" data-waco-gallery-stage>
                    @if(!empty($anh[0]))
                        <img src="{{ $anh[0] }}" alt="{{ $product->name }}"
                             data-waco-gallery-main fetchpriority="high" decoding="async">
                    @endif
                </div>
            </div>

            {{-- THONG TIN CHINH ------------------------------------------- --}}
            <div class="waco-detail__info">
                @if($urlDanhMuc)
                    <a href="{{ $urlDanhMuc }}" class="waco-detail__category">{{ $tenDanhMuc }}</a>
                @endif

                <h1 class="waco-detail__title">{{ $tenHienThi }}</h1>

                @if($maSanPham)
                    <p class="waco-detail__code">Mã sản phẩm: <strong>{{ $maSanPham }}</strong></p>
                @endif

                @if(count($diemManh))
                    <ul class="waco-detail__facts">
                        @foreach($diemManh as $dong)
                            <li>{{ $dong }}</li>
                        @endforeach
                    </ul>
                @endif

                <div class="waco-detail__actions">
                    <button type="button" class="waco-btn"
                            data-waco-consult="consult"
                            data-waco-consult-product="{{ $tenHienThi }}">
                        Nhận tư vấn miễn phí
                        @include('frontend.component.waco-icon', ['name' => 'arrow-right'])
                    </button>
                    <button type="button" class="waco-btn waco-btn--outline"
                            data-waco-consult="quote"
                            data-waco-consult-product="{{ $tenHienThi }}">
                        Yêu cầu báo giá
                        @include('frontend.component.waco-icon', ['name' => 'arrow-right'])
                    </button>
                </div>

                <p class="waco-detail__note">
                    @include('frontend.component.waco-icon', ['name' => 'shield-check', 'size' => 16])
                    Tư vấn tận tâm · Khảo sát miễn phí · Giải pháp phù hợp cho bạn
                </p>
            </div>

        </div>
    </section>

    {{-- HAI THE NOI DUNG --------------------------------------------------- --}}
    <section class="waco-detail-tabs">
        <div class="waco__container">
            <div class="waco-tabs" role="tablist">
                <button type="button" class="waco-tabs__item is-active" role="tab"
                        aria-selected="true" aria-controls="tab-thong-tin" data-waco-tab="tab-thong-tin">
                    Thông tin chi tiết
                </button>
                @if(count($thongSo))
                    <button type="button" class="waco-tabs__item" role="tab"
                            aria-selected="false" aria-controls="tab-thong-so" data-waco-tab="tab-thong-so">
                        Thông số kỹ thuật
                    </button>
                @endif
            </div>

            <div class="waco-detail-tabs__panel" id="tab-thong-tin" role="tabpanel">
                <h2 class="waco-detail-tabs__heading">{{ $product->name }}</h2>

                {{-- Noi dung dai bi thu gon; bam "Xem them" moi mo het. --}}
                <div class="waco-collapse" data-waco-collapse>
                    <div class="waco-collapse__body waco-article__content">
                        {!! $product->content !!}
                    </div>
                </div>

                <div class="waco-collapse__more">
                    <button type="button" class="waco-btn" data-waco-collapse-toggle>
                        <span data-waco-collapse-label>Xem thêm</span>
                        @include('frontend.component.waco-icon', ['name' => 'arrow-right'])
                    </button>
                </div>
            </div>

            @if(count($thongSo))
                <div class="waco-detail-tabs__panel" id="tab-thong-so" role="tabpanel" hidden>
                    <table class="waco-spec">
                        <tbody>
                            @foreach($thongSo as $dong)
                                <tr>
                                    <th scope="row">{{ $dong['ten'] }}</th>
                                    <td>{{ $dong['giaTri'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    {{-- SAN PHAM LIEN QUAN -------------------------------------------------- --}}
    @if($lienQuan->count())
        <section class="waco__section waco-related">
            <div class="waco__container">
                <h2 class="waco__heading">Sản phẩm liên quan</h2>
                <p class="waco__subheading">Các sản phẩm khác cùng {{ mb_strtolower($tenDanhMuc) }}</p>

                <div class="waco-related__grid">
                    @foreach($lienQuan as $item)
                        @include('frontend.component.waco-product-card', ['product' => $item])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- HE SINH THAI SAN PHAM WACO ----------------------------------------- --}}
    @include('frontend.component.waco-ecosystem')

    @include('frontend.component.waco-network')

</main>

<script>
// Swiper nap tu CDN bang defer nen chi chac chan co mat sau khi trang doc xong.
// Chay som hon thi Swiper con chua duoc dinh nghia, dai anh se khong khoi dong
// va cac o anh bi don het vao mot hang roi bi cat mat.
document.addEventListener('DOMContentLoaded', function () {
    // --- Dai anh doc: bam anh nho thi doi khung lon --------------------------
    var stage = document.querySelector('[data-waco-gallery-stage]');

    if (stage) {
        var thumbs = document.querySelectorAll('[data-waco-gallery-image], [data-waco-gallery-video]');

        function chon(thumb) {
            thumbs.forEach(function (t) { t.classList.remove('is-active'); });
            thumb.classList.add('is-active');

            var anh = thumb.getAttribute('data-waco-gallery-image');
            var video = thumb.getAttribute('data-waco-gallery-video');

            if (video) {
                // Chi nhung iframe khi nguoi dung that su bam vao muc video -
                // khong tai san YouTube o moi luot mo trang.
                stage.innerHTML = '<iframe src="https://www.youtube-nocookie.com/embed/' + video +
                    '?autoplay=1&rel=0" title="Video sản phẩm" frameborder="0" allowfullscreen ' +
                    'allow="accelerometer; autoplay; clipboard-write; encrypted-media; picture-in-picture"></iframe>';
                stage.classList.add('is-video');
            } else if (anh) {
                stage.innerHTML = '';
                var img = document.createElement('img');
                img.src = anh;
                img.alt = @json($product->name);
                img.setAttribute('data-waco-gallery-main', '');
                stage.appendChild(img);
                stage.classList.remove('is-video');
            }
        }

        thumbs.forEach(function (t) {
            t.addEventListener('click', function () { chon(t); });
        });

        // Dai anh nho chay doc. Chi khoi dong khi co Swiper; khong co thi cot
        // anh van cuon duoc binh thuong.
        if (typeof Swiper !== 'undefined' && document.querySelector('.waco-gallery__thumbs')) {
            // slidesPerView: 'auto' chu khong phai mot con so co dinh.
            //
            // Khung anh lon la hinh vuong nen chieu cao dai anh nho doi theo be
            // ngang man hinh. Neu ep 5 o moi man hinh thi Swiper tuong tat ca
            // deu vua, khoa luon hai nut len/xuong, va o cuoi (video) bi cat
            // mat khong cach nao xem duoc. De 'auto' thi Swiper do theo chieu
            // cao that cua tung o (96px) va tu biet khi nao can cuon.
            new Swiper('.waco-gallery__thumbs', {
                direction: 'vertical',
                slidesPerView: 'auto',
                spaceBetween: 12,
                navigation: {
                    prevEl: '.waco-gallery__nav--prev',
                    nextEl: '.waco-gallery__nav--next'
                },
                breakpoints: {
                    0:   { direction: 'horizontal' },
                    769: { direction: 'vertical' }
                }
            });
        }
    }

    // --- Hai the noi dung ---------------------------------------------------
    document.querySelectorAll('[data-waco-tab]').forEach(function (nut) {
        nut.addEventListener('click', function () {
            document.querySelectorAll('[data-waco-tab]').forEach(function (n) {
                n.classList.remove('is-active');
                n.setAttribute('aria-selected', 'false');
                var o = document.getElementById(n.getAttribute('data-waco-tab'));
                if (o) o.hidden = true;
            });
            nut.classList.add('is-active');
            nut.setAttribute('aria-selected', 'true');
            var oHien = document.getElementById(nut.getAttribute('data-waco-tab'));
            if (oHien) oHien.hidden = false;
        });
    });

    // --- Nut "Xem them" -----------------------------------------------------
    var khoi = document.querySelector('[data-waco-collapse]');
    var nutMo = document.querySelector('[data-waco-collapse-toggle]');
    var nhan = document.querySelector('[data-waco-collapse-label]');

    if (khoi && nutMo) {
        var than = khoi.querySelector('.waco-collapse__body');
        var oNut = nutMo.closest('.waco-collapse__more');

        // Noi dung ngan hon muc thu gon thi mo san va bo ca nut lan lop mo dan -
        // neu khong se thay mot vet mo va mot nut khong lam gi.
        //
        // Do lai o ca hai thoi diem: luc doc xong DOM va luc tai xong phong chu
        // + anh. Chi do mot lan o DOMContentLoaded thi chieu cao chua on dinh,
        // ket qua sai va nut van hien du noi dung rat ngan.
        function capNhatThuGon() {
            if (khoi.classList.contains('da-mo-tay')) {
                return;
            }
            var ngan = than.scrollHeight <= khoi.clientHeight + 8;
            khoi.classList.toggle('is-open', ngan);
            oNut.hidden = ngan;
        }

        capNhatThuGon();
        window.addEventListener('load', capNhatThuGon);
        window.addEventListener('resize', capNhatThuGon);

        nutMo.addEventListener('click', function () {
            // Danh dau da bam tay de lan do lai sau do khong dong nguoc lai.
            khoi.classList.add('da-mo-tay');
            var daMo = khoi.classList.toggle('is-open');
            nhan.textContent = daMo ? 'Thu gọn' : 'Xem thêm';
            nutMo.classList.toggle('waco-btn--outline', daMo);
        });
    }
});
</script>
@endsection
