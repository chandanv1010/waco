{{--
    Mot muc trong menu chinh, tu goi lai chinh no cho cac cap con.

    Truoc day dau trang viet tay dung HAI cap long nhau, nen menu cap 3 tao
    trong quan tri khong hien ra ngoai. Goi de quy thi co bao nhieu cap deu ra
    het, khong phai sua view moi lan them mot cap.

    Tham so:
      $node      - ['item' => <Menu>, 'children' => [...]]
      $cap       - 1 la muc ngoai cung tren thanh menu
      $menuLabel - ham lay ten + duong dan (dinh nghia mot lan trong header)
--}}
@php
    $m = $menuLabel($node);
    $coCon = count($node['children']) > 0;
@endphp

@if(!is_null($m))
    <li class="{{ $cap === 1 ? 'waco-menu__item' : 'waco-menu__sub-item' }}{{ $coCon ? ' has-sub' : '' }}">
        <a href="{{ $m['url'] }}"
           class="{{ $cap === 1 ? 'waco-menu__link' : 'waco-menu__sub-link' }}"
           title="{{ $m['name'] }}">
            {{ $m['name'] }}
            @if($coCon)
                {{-- Cap 1 tha xuong duoi nen dung mui ten xuong; cac cap sau
                     tha sang ben canh nen dung mui ten phai. --}}
                @include('frontend.component.waco-icon', [
                    'name' => $cap === 1 ? 'chevron-down' : 'chevron-right',
                ])
            @endif
        </a>

        @if($coCon)
            <ul class="waco-menu__sub{{ $cap > 1 ? ' waco-menu__sub--canh' : '' }}">
                @foreach($node['children'] as $con)
                    @include('frontend.component.waco-menu-node', [
                        'node' => $con,
                        'cap' => $cap + 1,
                        'menuLabel' => $menuLabel,
                    ])
                @endforeach
            </ul>
        @endif
    </li>
@endif
