{{--
    Mot muc menu tren man hinh hep, tu goi lai chinh no cho cac cap con.

    Man hinh hep khong co cho cho menu tha xuong nen do phang tat ca cac cap ra
    mot danh sach doc, cap cang sau thi thut vao cang nhieu.

    Tham so:
      $node      - ['item' => <Menu>, 'children' => [...]]
      $cap       - 1 la muc ngoai cung
      $menuLabel - ham lay ten + duong dan (dinh nghia mot lan trong header)
--}}
@php
    $m = $menuLabel($node);
@endphp

@if(!is_null($m))
    <li class="waco-mobile-menu__muc waco-mobile-menu__muc--cap{{ min($cap, 4) }}">
        <a href="{{ $m['url'] }}">{{ $m['name'] }}</a>
    </li>

    @foreach($node['children'] as $con)
        @include('frontend.component.waco-mobile-menu-node', [
            'node' => $con,
            'cap' => $cap + 1,
            'menuLabel' => $menuLabel,
        ])
    @endforeach
@endif
