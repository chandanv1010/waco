{{--
    Mot danh muc trong cot ben trai trang san pham, tu goi lai chinh no cho
    cac danh muc con.

    Truoc day view in mot danh sach PHANG gom moi cap, nen danh muc con khong
    nam duoi danh muc cha cua no.

    Tham so:
      $cat             - dong danh muc, co them ->children
      $cap             - 1 la danh muc ngoai cung
      $danhMucHienTai  - id danh muc dang xem
      $nhanhDangXem    - mang id tu danh muc dang xem nguoc len goc
--}}
@php
    $dangXem = (int) $cat->id === (int) $danhMucHienTai;

    // Danh muc cha cua muc dang xem cung duoc danh dau, de nguoi dung biet
    // minh dang o nhanh nao chu khong chi thay moi dong cuoi sang len.
    $trongNhanh = in_array((int) $cat->id, $nhanhDangXem ?? [], true);

    $coCon = !empty($cat->children);
@endphp

<li class="waco-catnav__muc waco-catnav__muc--cap{{ min($cap, 3) }}">
    <a href="{{ write_url($cat->canonical, true, true) }}"
       class="waco-catnav__link{{ $dangXem ? ' is-active' : '' }}{{ !$dangXem && $trongNhanh ? ' is-nhanh' : '' }}">
        {{-- Chi danh muc ngoai cung moi co icon; danh muc con de chu thut vao
             cho de doc, them icon nua se thanh mot cot anh lon xon. --}}
        @if($cap === 1 && !empty($cat->icon))
            <img src="{{ $cat->icon }}" alt="" aria-hidden="true" loading="lazy">
        @endif
        <span>{{ $cat->name }}</span>
    </a>

    @if($coCon)
        <ul class="waco-catnav__con">
            @foreach($cat->children as $con)
                @include('frontend.component.waco-catnav-node', [
                    'cat' => $con,
                    'cap' => $cap + 1,
                    'danhMucHienTai' => $danhMucHienTai,
                    'nhanhDangXem' => $nhanhDangXem ?? [],
                ])
            @endforeach
        </ul>
    @endif
</li>
