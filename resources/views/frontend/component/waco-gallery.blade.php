{{--
    Luoi anh album: ba anh mot hang, bam vao thi phong to bang lightbox.

    Anh lay tu o "Album" trong man hinh soan bai viet (cot posts.album, luu JSON
    la mang duong dan anh).

    Tham so:
      $album  - chuoi JSON hoac mang duong dan anh
      $ten    - ten bai viet, dung lam alt cho anh (khong bat buoc)
--}}
@php
    // Cot album luu JSON. Bai cu de trong, hoac quan tri xoa het anh thi con
    // chuoi rong - json_decode tra ve null nen phai loc lai truoc khi dem.
    $danhSachAnh = is_array($album ?? null) ? $album : json_decode((string) ($album ?? ''), true);
    $danhSachAnh = array_values(array_filter((array) $danhSachAnh));
    $tenBai = $ten ?? '';
@endphp

@if(count($danhSachAnh))
    <div class="waco-gallery">
        @foreach($danhSachAnh as $chiSo => $anh)
            <a href="{{ $anh }}" class="waco-gallery__item" data-waco-lightbox>
                <img src="{{ $anh }}"
                     alt="{{ $tenBai ? $tenBai . ' - ảnh ' . ($chiSo + 1) : 'Ảnh ' . ($chiSo + 1) }}"
                     loading="lazy" decoding="async">
            </a>
        @endforeach
    </div>

    @include('frontend.component.waco-lightbox')
@endif
