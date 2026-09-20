{{--
    Chan trang WACO.

    Ba cot lien ket lay tu bang menus (nhom footer-menu, muc cap 1 la tieu de
    cot, muc cap 2 la lien ket ben trong). Cot lien he lay tu bang systems.
--}}
@php
    $footerMenu = $menu['footer-menu'] ?? [];

    $label = function ($node) {
        $lang = $node['item']->languages->first();
        if (is_null($lang) || is_null($lang->pivot)) {
            return null;
        }
        return [
            'name' => $lang->pivot->name,
            'url' => $lang->pivot->canonical === '' ? url('/') : write_url($lang->pivot->canonical, true, true),
        ];
    };

    $socials = array_filter([
        // Khoa phai trung ten o nhap trong admin (nhom "Mang xa hoi"), neu
        // khong thi sua trong admin xong ngoai trang van khong doi.
        'facebook' => $system['social_facebook'] ?? '',
        'youtube' => $system['social_youtube'] ?? '',
        'tiktok' => $system['social_tiktok'] ?? '',
    ]);
@endphp

<footer class="waco-footer">
    <div class="waco-footer__inner">
        <div>
            <div class="waco-footer__logo">
                @if(!empty($system['homepage_logo']))
                    <img src="{{ $system['homepage_logo'] }}" alt="{{ $system['homepage_company'] ?? 'WACO' }}" width="150" height="56" loading="lazy">
                @else
                    <strong style="color:#fff;font-size:20px;letter-spacing:.04em">WACO</strong>
                @endif
            </div>
            <p class="waco-footer__description">{{ $intro['footer_description'] ?? '' }}</p>

            @if(count($socials))
                <div class="waco-footer__social">
                    @foreach($socials as $ten => $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $ten }}">
                            @include('frontend.component.waco-icon', ['name' => 'globe', 'size' => 18])
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @foreach($footerMenu as $cot)
            @php $c = $label($cot); @endphp
            @continue(is_null($c))
            <div>
                <h3 class="waco-footer__title">{{ $c['name'] }}</h3>
                <ul class="waco-footer__list">
                    @foreach($cot['children'] as $muc)
                        @php $m = $label($muc); @endphp
                        @continue(is_null($m))
                        <li><a href="{{ $m['url'] }}" title="{{ $m['name'] }}">{{ $m['name'] }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        <div>
            @if(!empty($system['contact_address']))
                <div class="waco-footer__contact">
                    @include('frontend.component.waco-icon', ['name' => 'pin', 'size' => 15])
                    <span>{{ $system['contact_address'] }}</span>
                </div>
            @endif
            @if(!empty($system['contact_hotline']))
                <div class="waco-footer__contact">
                    @include('frontend.component.waco-icon', ['name' => 'phone', 'size' => 15])
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $system['contact_hotline']) }}">{{ $system['contact_hotline'] }}</a>
                </div>
            @endif
            @if(!empty($system['contact_email']))
                <div class="waco-footer__contact">
                    @include('frontend.component.waco-icon', ['name' => 'mail', 'size' => 15])
                    <a href="mailto:{{ $system['contact_email'] }}">{{ $system['contact_email'] }}</a>
                </div>
            @endif
            @if(!empty($system['contact_website']))
                <div class="waco-footer__contact">
                    @include('frontend.component.waco-icon', ['name' => 'globe', 'size' => 15])
                    <span>{{ $system['contact_website'] }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="waco-footer__bottom">
        <div class="waco-footer__copyright">{{ $system['homepage_copyright'] ?? '' }}</div>
    </div>
</footer>
