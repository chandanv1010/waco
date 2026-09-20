{{--
    Dai tieu de dau trang con (thiet ke goi la banner nuoc).

    Tham so:
      $title  - tieu de trang, bat buoc
      $crumbs - mang [ten => duong dan] cho duong dan dieu huong, khong bat buoc.
                Muc cuoi cung de duong dan rong thi in ra chu thuong, khong link.

    Nen song nuoc ve bang SVG ngay trong CSS chu khong dung anh: mot the nen tinh
    khong dang tai them mot file anh vai tram KB.
--}}
<section class="waco-banner">
    <div class="waco-banner__inner">
        <h1 class="waco-banner__title">{{ $title }}</h1>
    </div>
</section>

@if(!empty($crumbs))
    <nav class="waco-crumbs" aria-label="Đường dẫn">
        <div class="waco-crumbs__inner">
            <a href="{{ url('/') }}">Trang chủ</a>
            @foreach($crumbs as $ten => $duongDan)
                <span class="waco-crumbs__sep" aria-hidden="true">/</span>
                @if($duongDan)
                    <a href="{{ $duongDan }}">{{ $ten }}</a>
                @else
                    <span aria-current="page">{{ $ten }}</span>
                @endif
            @endforeach
        </div>
    </nav>
@endif
