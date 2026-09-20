@extends('frontend.homepage.layout')

@section('content')
@php
    $diaChi = $system['contact_address'] ?? '';
    $hotline = $system['contact_hotline'] ?? ($system['contact_phone'] ?? '');
    $email = $system['contact_email'] ?? '';
    $website = $system['contact_website'] ?? '';

    // Ban do: uu tien ma nhung admin dan vao cau hinh, khong co thi dung dia chi
    // de tu dung duong dan Google Maps - khong ghi cung toa do cua don vi nao.
    $mapEmbed = trim($system['contact_office_map'] ?? '');
@endphp

<main class="waco">

    @include('frontend.component.waco-banner', [
        'title' => 'Liên hệ',
        'crumbs' => ['Liên hệ' => ''],
    ])

    <section class="waco__section" id="lien-he">
        <div class="waco-contact__inner">

            <div class="waco-contact__panel">
                <h2 class="waco-contact__heading">{{ $intro['contact_heading'] ?? 'Thông tin liên hệ' }}</h2>

                <ul class="waco-contact__list">
                    @if($diaChi)
                        <li>
                            @include('frontend.component.waco-icon', ['name' => 'pin', 'size' => 17])
                            <span>{{ $diaChi }}</span>
                        </li>
                    @endif
                    @if($hotline)
                        <li>
                            @include('frontend.component.waco-icon', ['name' => 'phone', 'size' => 17])
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $hotline) }}">{{ $hotline }}</a>
                        </li>
                    @endif
                    @if($email)
                        <li>
                            @include('frontend.component.waco-icon', ['name' => 'mail', 'size' => 17])
                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                        </li>
                    @endif
                    @if($website)
                        <li>
                            @include('frontend.component.waco-icon', ['name' => 'globe', 'size' => 17])
                            <span>{{ $website }}</span>
                        </li>
                    @endif
                </ul>

                @if(session('contact_success'))
                    <div class="waco-alert waco-alert--success">{{ session('contact_success') }}</div>
                @endif

                @if($errors->contact->any())
                    <div class="waco-alert waco-alert--error">
                        <ul>
                            @foreach($errors->contact->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="waco-contact-form">
                    @csrf

                    <div class="waco-contact-form__group">
                        <label for="contact-name">Họ và tên (*)</label>
                        <input type="text" id="contact-name" name="name" value="{{ old('name') }}"
                               placeholder="Nhập họ và tên" required>
                    </div>

                    <div class="waco-contact-form__row">
                        <div class="waco-contact-form__group">
                            <label for="contact-phone">Số điện thoại (*)</label>
                            <input type="tel" id="contact-phone" name="phone" value="{{ old('phone') }}"
                                   placeholder="Nhập số điện thoại" required>
                        </div>
                        <div class="waco-contact-form__group">
                            <label for="contact-email">Email</label>
                            <input type="email" id="contact-email" name="email" value="{{ old('email') }}"
                                   placeholder="Nhập email">
                        </div>
                    </div>

                    <div class="waco-contact-form__group">
                        <label for="contact-address">Địa chỉ</label>
                        <input type="text" id="contact-address" name="address" value="{{ old('address') }}"
                               placeholder="Nhập địa chỉ">
                    </div>

                    <div class="waco-contact-form__group">
                        <label for="contact-message">Nội dung</label>
                        <textarea id="contact-message" name="message" rows="5"
                                  placeholder="Nhập nội dung cần tư vấn">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="waco-btn">Gửi thông tin</button>
                </form>
            </div>

            <div class="waco-contact__map">
                @if($mapEmbed)
                    {{-- Ma nhung do quan tri dan vao phan cau hinh. --}}
                    {!! $mapEmbed !!}
                @elseif($diaChi)
                    <iframe
                        title="Bản đồ tới {{ $system['homepage_company'] ?? 'WACO' }}"
                        src="https://www.google.com/maps?q={{ urlencode($diaChi) }}&output=embed"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen></iframe>
                @endif
            </div>

        </div>
    </section>

    @include('frontend.component.waco-network')

</main>
@endsection
