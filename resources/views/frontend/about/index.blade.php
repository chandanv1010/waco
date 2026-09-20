@extends('frontend.homepage.layout')

@section('content')
@php
    $aboutBadges = $features['about_badge'] ?? collect();
@endphp

<main class="waco">

    @include('frontend.component.waco-banner', [
        'title' => 'Giới thiệu',
        'crumbs' => ['Giới thiệu' => ''],
    ])

    {{-- 1. CAM KET CHINH HANG - cung khoi voi trang chu ------------------- --}}
    <section class="waco__section waco-about">
        <div class="waco-about__inner">
            <div>
                <p class="waco-about__label">{{ $intro['about_label'] ?? '' }}</p>
                <h2 class="waco-about__heading">{!! $intro['about_heading'] ?? '' !!}</h2>
                <p class="waco-about__lead">{{ $intro['about_subheading'] ?? '' }}</p>

                <div class="waco-about__content">
                    {!! $intro['about_content'] ?? '' !!}
                </div>

                @if($aboutBadges->count())
                    <div class="waco-about__badges">
                        @foreach($aboutBadges as $badge)
                            <div class="waco-badge">
                                @if(!empty($badge->icon))
                                    <span class="waco-badge__icon">
                                        <img src="{{ $badge->icon }}" alt="" aria-hidden="true" loading="lazy">
                                    </span>
                                @endif
                                <span class="waco-badge__title">{!! nl2br(e($badge->title)) !!}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="waco-about__media">
                @if(!empty($system['homepage_about_image']))
                    <img src="{{ $system['homepage_about_image'] }}" alt="{{ $intro['about_subheading'] ?? '' }}"
                         width="580" height="380" fetchpriority="high" decoding="async">
                @endif
            </div>
        </div>
    </section>

    {{-- 2. DAI CAM KET ----------------------------------------------------- --}}
    @include('frontend.component.waco-commit')

    {{-- 3. TAM NHIN - SU MENH (anh ben trai, chu ben phai) ----------------- --}}
    @if(!empty($intro['vision_content']))
        <section class="waco__section waco-about waco-about--reverse">
            <div class="waco-about__inner">
                @php
                    // Chua co anh rieng cho khoi tam nhin thi dung tam anh tru so
                    // - dung anh trong thiet ke, va admin them anh rieng sau duoc.
                    $visionImage = $system['homepage_vision_image'] ?? ($system['homepage_about_image'] ?? '');
                @endphp
                <div class="waco-about__media">
                    @if(!empty($visionImage))
                        <img src="{{ $visionImage }}"
                             alt="{{ $intro['vision_heading'] ?? '' }}"
                             width="580" height="380" loading="lazy" decoding="async">
                    @endif
                </div>

                <div>
                    <p class="waco-about__label">{{ $intro['vision_label'] ?? '' }}</p>
                    <h2 class="waco-about__heading">{!! $intro['vision_heading'] ?? '' !!}</h2>

                    <div class="waco-about__content">
                        {!! $intro['vision_content'] !!}
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- 4. VI SAO WACO ----------------------------------------------------- --}}
    @include('frontend.component.waco-why')

    {{-- 5. MANG LUOI PHAN PHOI + DANG KY DAI LY ---------------------------- --}}
    @include('frontend.component.waco-network')

</main>
@endsection
