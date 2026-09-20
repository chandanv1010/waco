@extends('frontend.homepage.layout')

@section('content')
@php
    // $intro va $features do WacoComposer cap cho moi view frontend.
    $heroUsp = $features['hero_usp'] ?? collect();
    $aboutBadges = $features['about_badge'] ?? collect();

    // Banner chinh: module Slide, tu khoa main-slide.
    $mainSlide = $slides[\App\Enums\SlideEnum::MAIN]['item'] ?? [];
@endphp

<main class="waco">

    {{-- 1. HERO ------------------------------------------------------------ --}}
    <section class="waco-hero">
        @if(count($mainSlide))
            {{-- Anh trai HET chieu ngang va lam nen, chu de len tren. Day la anh
                 lon nhat cua man hinh dau tien nen KHONG lazy load: anh dau
                 fetchpriority=high de trinh duyet tai truoc moi anh khac.
                 Nhieu hon mot anh thi swiper tu chuyen. --}}
            <div class="waco-hero__bg{{ count($mainSlide) > 1 ? ' waco-hero__bg--slider swiper' : '' }}">
                <div class="swiper-wrapper">
                    @foreach($mainSlide as $key => $item)
                        <div class="swiper-slide">
                            <img src="{{ $item['image'] }}"
                                 alt="{{ $item['alt'] ?: ($item['name'] ?: ($system['homepage_company'] ?? 'WACO')) }}"
                                 @if($key === 0) fetchpriority="high" @else loading="lazy" @endif
                                 decoding="async">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="waco-hero__inner">
            <div>
                <h1 class="waco-hero__title">
                    {{ $intro['hero_title_line1'] ?? '' }}<br>
                    {{-- Dong 2 co chua <span> to mau chu "WACO KOREA" nen in
                         khong escape; noi dung do admin nhap trong bang introduces. --}}
                    {!! $intro['hero_title_line2'] ?? '' !!}<br>
                    {{ $intro['hero_title_line3'] ?? '' }}
                </h1>

                <p class="waco-hero__description">{{ $intro['hero_description'] ?? '' }}</p>

                @if($heroUsp->count())
                    <div class="waco-hero__usp">
                        @foreach($heroUsp as $usp)
                            <div class="waco-usp">
                                @if(!empty($usp->icon))
                                    <span class="waco-usp__icon">
                                        <img src="{{ $usp->icon }}" alt="" aria-hidden="true" loading="lazy">
                                    </span>
                                @endif
                                {{-- Tieu de luu kem ky tu xuong dong that trong CSDL
                                     (hai tu mot dong nhu thiet ke) -> nl2br de giu
                                     dung cho ngat, admin sua duoc ma khong dung code. --}}
                                <div class="waco-usp__title">{!! nl2br(e($usp->title)) !!}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="waco-hero__actions">
                    <a href="#dang-ky-dai-ly" class="waco-btn">
                        {{ $intro['hero_cta_label'] ?? 'Trở thành đại lý' }}
                        @include('frontend.component.waco-icon', ['name' => 'arrow-right'])
                    </a>

                    @if(!empty($intro['hero_cta2_label']))
                        <a href="{{ !empty($system['homepage_company_profile']) ? $system['homepage_company_profile'] : '#' }}"
                           class="waco-btn waco-btn--outline"
                           @if(!empty($system['homepage_company_profile'])) download @endif>
                            {{ $intro['hero_cta2_label'] }}
                            @include('frontend.component.waco-icon', ['name' => 'download'])
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- 1b. DAI CAM KET - de len day hero ---------------------------------- --}}
    @include('frontend.component.waco-commit', ['overlap' => true])

    {{-- 2. GIOI THIEU ------------------------------------------------------ --}}
    <section class="waco__section waco-about">
        <div class="waco-about__inner">
            <div>
                <p class="waco-about__label">{{ $intro['about_label'] ?? '' }}</p>
                {{-- Tieu de co the xuong dong (chua <br> do admin nhap). --}}
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
                         width="580" height="380" loading="lazy" decoding="async">
                @endif
            </div>
        </div>
    </section>

    {{-- 3. HE SINH THAI SAN PHAM ------------------------------------------- --}}
    @include('frontend.component.waco-ecosystem')

    {{-- 4. VI SAO WACO ----------------------------------------------------- --}}
    @include('frontend.component.waco-why')

    {{-- 5. MANG LUOI PHAN PHOI + DANG KY DAI LY ---------------------------- --}}
    @include('frontend.component.waco-network')

    {{-- 6. TIN TUC --------------------------------------------------------- --}}
    @if($news->count())
        <section class="waco__section waco-news">
            <div class="waco__container">
                <h2 class="waco__heading">{{ $intro['news_heading'] ?? '' }}</h2>

                <div class="waco-news__grid">
                    @foreach($news as $post)
                        @include('frontend.component.waco-news-card', ['post' => $post])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</main>

@if(count($mainSlide) > 1)
    {{-- Chi khoi dong slider khi quan tri them tu anh thu hai tro len. Mot anh
         thi de nguyen the img cho nhe trang. --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swiper === 'undefined') return;

            new Swiper('.waco-hero__bg--slider', {
                loop: true,
                effect: 'fade',
                fadeEffect: { crossFade: true },
                autoplay: { delay: 6000, disableOnInteraction: false },
            });
        });
    </script>
@endif
@endsection
