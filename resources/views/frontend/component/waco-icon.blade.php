{{--
    Bo icon dung chung cho trang chu WACO.

    Chi chua icon giao dien: mui ten, mui ten xuong, tai ve, dien thoai, thu,
    dia chi, menu... Ve bang SVG inline de an theo currentColor - cung mot icon
    dung duoc ca tren nen trang lan nen navy ma khong can hai file anh.

    Icon cua ban thiet ke (4 diem manh hero, dai cam ket, huy hieu gioi thieu,
    icon danh muc, icon "Vi sao WACO") KHONG nam o day: chung la file anh trich
    tu Figma, duong dan luu trong cot `icon` cua home_features va
    product_catalogues de quan tri thay duoc trong admin.

    Cach dung: @include('frontend.component.waco-icon', ['name' => 'arrow-right'])
--}}
@php
    $size = $size ?? 24;
    $name = $name ?? '';
@endphp

@switch($name)
    @case('medal')
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <circle cx="12" cy="9" r="6"/>
            <path d="m8.2 14.3-1.7 6.2 5.5-2.8 5.5 2.8-1.7-6.2"/>
        </svg>
        @break

    @case('arrow-right')
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>
        @break

    @case('download')
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M12 3v12M7 11l5 5 5-5M5 21h14"/>
        </svg>
        @break

    @case('chevron-down')
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
            <path d="m6 9 6 6 6-6"/>
        </svg>
        @break

    @case('pin')
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
            <circle cx="12" cy="10" r="3"/>
        </svg>
        @break

    @case('phone')
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>
        </svg>
        @break

    @case('mail')
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="m22 7-10 6L2 7"/>
        </svg>
        @break

    @case('globe')
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
        @break

    @case('menu')
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M3 6h18M3 12h18M3 18h18"/>
        </svg>
        @break
    @case('messenger')
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 2C6.48 2 2 6.15 2 11.26c0 2.91 1.46 5.52 3.73 7.23V22l3.36-1.84c.92.26 1.9.4 2.91.4 5.52 0 10-4.15 10-9.26S17.52 2 12 2zm1 12.43-2.55-2.72-4.97 2.72 5.47-5.8 2.6 2.72 4.92-2.72-5.47 5.8z"/>
        </svg>
        @break

    @case('shield-check')
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
            <path d="M12 2.5 4.5 5.8V11c0 4.6 3.2 8.9 7.5 10.5 4.3-1.6 7.5-5.9 7.5-10.5V5.8L12 2.5z"/>
            <path d="m8.8 11.9 2.2 2.2 4.2-4.3"/>
        </svg>
        @break

    @default
        {{-- Khong co icon khop ten -> ve mot vong tron trung tinh, de bo cuc
             khong vo khi admin nhap ten icon la. --}}
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <circle cx="12" cy="12" r="9"/>
        </svg>
@endswitch
