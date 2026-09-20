{{-- Khoi "Vi sao WACO duoc tin dung" - 6 ly do tren nen navy. --}}
@php
    $whyItems = $features['why_waco'] ?? collect();
@endphp

@if($whyItems->count())
    <section class="waco__section waco-why">
        <div class="waco__container">
            <h2 class="waco__heading">{{ $intro['why_heading'] ?? '' }}</h2>

            <div class="waco-why__grid">
                @foreach($whyItems as $item)
                    <div class="waco-why-item">
                        @if(!empty($item->icon))
                            <div class="waco-why-item__icon">
                                <img src="{{ $item->icon }}" alt="" aria-hidden="true" loading="lazy">
                            </div>
                        @endif
                        <h3 class="waco-why-item__title">{{ $item->title }}</h3>
                        <p class="waco-why-item__description">{{ $item->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
