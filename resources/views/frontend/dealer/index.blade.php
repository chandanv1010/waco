@extends('frontend.homepage.layout')

@section('content')
<main class="waco">

    @include('frontend.component.waco-banner', [
        'title' => 'Hệ thống đại lý',
        'crumbs' => ['Hệ thống đại lý' => ''],
    ])

    @if(!empty($intro['dealer_page_heading']))
        <section class="waco__section waco-intro-text">
            <div class="waco__container">
                <h2 class="waco__heading">{{ $intro['dealer_page_heading'] }}</h2>
                <div class="waco-intro-text__body">
                    {!! $intro['dealer_page_content'] ?? '' !!}
                </div>
            </div>
        </section>
    @endif

    @include('frontend.component.waco-network')

</main>
@endsection
