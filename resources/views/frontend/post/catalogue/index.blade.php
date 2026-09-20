@extends('frontend.homepage.layout')

@section('content')
@php
    $tenDanhMuc = $postCatalogue->name;
    // Bai noi bat da hien rieng o dau trang thi bo khoi luoi ben duoi.
    $danhSach = $featured
        ? $posts->getCollection()->reject(fn($p) => $p->id === $featured->id)
        : $posts->getCollection();
@endphp

<main class="waco">

    @include('frontend.component.waco-banner', [
        'title' => $tenDanhMuc,
        'crumbs' => [$tenDanhMuc => ''],
    ])

    <section class="waco__section">
        <div class="waco__container">

            {{-- Bai noi bat: anh lon ben trai, tieu de va trich dan ben phai. --}}
            @if($featured)
                @php
                    // PostService da join san post_language nen ten/duong dan nam
                    // thang tren ban ghi, khong phai trong quan he languages.
                    $fUrl = write_url($featured->canonical, true, true);
                @endphp
                <article class="waco-feature-post">
                    <a href="{{ $fUrl }}" class="waco-feature-post__media" title="{{ $featured->name }}">
                        @if(!empty($featured->image))
                            <img src="{{ $featured->image }}" alt="{{ $featured->name }}"
                                 width="640" height="400" fetchpriority="high" decoding="async">
                        @endif
                    </a>

                    <div class="waco-feature-post__body">
                        @if($featured->released_at)
                            <div class="waco-feature-post__date">
                                {{ \Carbon\Carbon::parse($featured->released_at)->format('d/m/Y') }}
                            </div>
                        @endif
                        <h2 class="waco-feature-post__title">
                            <a href="{{ $fUrl }}">{{ $featured->name }}</a>
                        </h2>
                        <p class="waco-feature-post__description">
                            {{ Str::limit(strip_tags($featured->description ?? ''), 260) }}
                        </p>
                        <a href="{{ $fUrl }}" class="waco-btn waco-btn--sm">
                            Xem chi tiết
                            @include('frontend.component.waco-icon', ['name' => 'arrow-right'])
                        </a>
                    </div>
                </article>
            @endif

            {{-- Dai loc theo chuyen muc. Chi ve khi co tu hai chuyen muc tro len. --}}
            @if($tabs->count() > 1)
                <nav class="waco-tabs" aria-label="Chuyên mục">
                    @foreach($tabs as $tab)
                        @php $tLang = $tab->languages->first(); @endphp
                        @continue(!$tLang)
                        <a href="{{ write_url($tLang->pivot->canonical ?? $tLang->canonical, true, true) }}"
                           class="waco-tabs__item{{ $tab->id === $postCatalogue->id ? ' is-active' : '' }}">
                            {{ $tLang->pivot->name ?? $tLang->name }}
                        </a>
                    @endforeach
                </nav>
            @endif

            @if($danhSach->count())
                <div class="waco-news__grid waco-news__grid--3">
                    @foreach($danhSach as $post)
                        @include('frontend.component.waco-news-card', ['post' => $post])
                    @endforeach
                </div>
            @else
                <p class="waco-empty">Chưa có bài viết nào trong chuyên mục này.</p>
            @endif

            @include('frontend.component.waco-pagination', ['model' => $posts])

        </div>
    </section>

    @include('frontend.component.waco-network')

</main>
@endsection
