{{--
    Dau trang WACO.

    Menu lay tu bang menus (nhom main-menu) qua MenuComposer da chia se san bien
    $menu cho moi view frontend.* - khong truy van lai o day.

    $menu['main-menu_array'] co dang:
        [ ['item' => <Menu>, 'children' => [ ['item' => ..., 'children' => []] ]], ... ]
--}}
@php
    $mainMenu = $menu['main-menu_array'] ?? [];

    // Lay ten + duong dan cua mot muc menu. Muc chua gan ngon ngu se tra ve null
    // de ben duoi bo qua, thay vi no loi pivot on null.
    $menuLabel = function ($node) {
        $lang = $node['item']->languages->first();
        if (is_null($lang) || is_null($lang->pivot)) {
            return null;
        }
        return [
            'name' => $lang->pivot->name,
            'url' => $lang->pivot->canonical === '' ? url('/') : write_url($lang->pivot->canonical, true, true),
        ];
    };
@endphp

<header class="waco-header">
    <div class="waco-header__inner">
        <a href="{{ url('/') }}" class="waco-header__logo" title="{{ $system['homepage_company'] ?? 'WACO' }}">
            @if(!empty($system['homepage_logo']))
                <img src="{{ $system['homepage_logo'] }}" alt="{{ $system['homepage_company'] ?? 'WACO' }}" width="160" height="52">
            @else
                <strong style="color:#0b3a7a;font-size:20px;letter-spacing:.04em">WACO</strong>
            @endif
        </a>

        <nav class="waco-header__nav" aria-label="Menu chính">
            <ul class="waco-menu">
                @foreach($mainMenu as $node)
                    @include('frontend.component.waco-menu-node', [
                        'node' => $node,
                        'cap' => 1,
                        'menuLabel' => $menuLabel,
                    ])
                @endforeach
            </ul>
        </nav>

        <a href="{{ url('/#dang-ky-dai-ly') }}" class="waco-btn waco-header__cta">
            {{ $intro['header_cta_label'] ?? 'Trở thành đại lý' }}
            @include('frontend.component.waco-icon', ['name' => 'arrow-right'])
        </a>

        <button type="button" class="waco-header__toggle" aria-label="Mở menu"
                aria-expanded="false" aria-controls="waco-mobile-menu" data-waco-menu-toggle>
            @include('frontend.component.waco-icon', ['name' => 'menu'])
        </button>
    </div>

    {{-- Menu cho man hinh hep: do phang MOI cap ra mot danh sach, cap sau thut
         vao sau hon. O be ngang nay khong co cho cho menu tha xuong. --}}
    <div class="waco-mobile-menu" id="waco-mobile-menu">
        <ul>
            @foreach($mainMenu as $node)
                @include('frontend.component.waco-mobile-menu-node', [
                    'node' => $node,
                    'cap' => 1,
                    'menuLabel' => $menuLabel,
                ])
            @endforeach
            <li><a href="{{ url('/#dang-ky-dai-ly') }}">{{ $intro['header_cta_label'] ?? 'Trở thành đại lý' }}</a></li>
        </ul>
    </div>
</header>

<script>
    // Mo/dong menu tren man hinh hep. Viet truc tiep o day thay vi bundle: chi
    // vai dong va can chay ngay khi dau trang xuat hien, khong doi tai het JS.
    (function () {
        var toggle = document.querySelector('[data-waco-menu-toggle]');
        var panel = document.getElementById('waco-mobile-menu');
        if (!toggle || !panel) return;

        toggle.addEventListener('click', function () {
            var open = panel.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Đóng menu' : 'Mở menu');
        });
    })();
</script>
