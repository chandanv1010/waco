@extends('frontend.homepage.layout')

@section('content')
@php
    $tenDanhMuc = $postCatalogue->name ?? 'Tin tức';
    $urlDanhMuc = !empty($postCatalogue->canonical) ? write_url($postCatalogue->canonical, true, true) : '';
@endphp

<main class="waco">

    @include('frontend.component.waco-banner', [
        'title' => $tenDanhMuc,
        'crumbs' => [$tenDanhMuc => $urlDanhMuc, Str::limit($post->name, 60) => ''],
    ])

    <section class="waco__section">
        <div class="waco-article__inner">
            <article class="waco-article">
                <h1 class="waco-article__title">{{ $post->name }}</h1>

                <div class="waco-article__meta">
                    @if($post->released_at)
                        <span>{{ \Carbon\Carbon::parse($post->released_at)->format('d/m/Y') }}</span>
                    @endif
                    <span>{{ number_format((int) $post->viewed) }} lượt xem</span>
                </div>

                @if(!empty($post->description))
                    <p class="waco-article__lead">{{ plain_text($post->description) }}</p>
                @endif

                @if(!empty($post->image))
                    <div class="waco-article__cover">
                        <img src="{{ $post->image }}" alt="{{ $post->name }}"
                             fetchpriority="high" decoding="async">
                    </div>
                @endif

                {{-- Noi dung do quan tri soan bang trinh soan thao trong admin. --}}
                <div class="waco-article__content">
                    {!! $post->content !!}
                </div>
            </article>
        </div>
    </section>

    @if($related->count())
        <section class="waco__section waco-news">
            <div class="waco__container">
                <h2 class="waco__heading">Bài viết liên quan</h2>

                <div class="waco-news__grid waco-news__grid--3">
                    @foreach($related as $item)
                        @include('frontend.component.waco-news-card', ['post' => $item])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('frontend.component.waco-network')

</main>
@endsection
