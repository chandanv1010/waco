{{--
    Khoi "Mang luoi phan phoi WACO toan quoc" + form dang ky dai ly.

    Xuat hien o gan nhu moi trang trong ban thiet ke nen tach rieng ra day.
    Du lieu ($intro, $features) do WacoComposer cap cho moi view frontend.
--}}
@php
    $stats = $features['stat'] ?? collect();
@endphp

<section class="waco__section waco-network" id="dang-ky-dai-ly">
    <div class="waco-network__inner">
        <div>
            <p class="waco-network__label">{{ $intro['network_label'] ?? '' }}</p>
            <h2 class="waco-network__heading">{{ $intro['network_heading'] ?? '' }}</h2>

            <div class="waco-network__body">
                @if($stats->count())
                    <div class="waco-network__stats">
                        @foreach($stats as $stat)
                            <div class="waco-stat">
                                <div class="waco-stat__value">{{ $stat->value }}</div>
                                <div class="waco-stat__label">{{ $stat->title }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="waco-network__map">
                    @if(!empty($system['homepage_map_image']))
                        <img src="{{ $system['homepage_map_image'] }}" alt="Bản đồ hệ thống đại lý WACO toàn quốc"
                             width="440" height="520" loading="lazy" decoding="async">
                    @endif
                </div>
            </div>

            <a href="{{ write_url('he-thong-dai-ly') }}" class="waco-btn waco-network__cta">
                {{ $intro['network_cta_label'] ?? 'Xem bản đồ đại lý' }}
                @include('frontend.component.waco-icon', ['name' => 'arrow-right'])
            </a>
        </div>

        @include('frontend.component.waco-dealer-form')
    </div>
</section>
