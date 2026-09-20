@php
    // Truoc day cho nay ghi cung so dien thoai va email cua website truoc lam
    // gia tri mac dinh. Neu admin de trong cau hinh thi khach bam vao se goi
    // nham sang mot don vi khac - de trong con hon la de sai.
    $hotlineRaw = $system['contact_hotline'] ?? $system['contact_phone'] ?? '';
    $primaryHotline = trim(explode('|', $hotlineRaw)[0] ?? '');
    $emailVal = trim($system['contact_email'] ?? '');

    $messengerVal = trim($system['social_messenger'] ?? $system['social_facebook'] ?? '');
    if (!empty($messengerVal) && str_starts_with($messengerVal, 'http')) {
        $messengerHref = $messengerVal;
    } elseif (!empty($messengerVal)) {
        $messengerHref = 'https://m.me/' . $messengerVal;
    } else {
        $messengerHref = '';
    }

    $zaloVal = trim($system['social_zalo'] ?? '');
    if (!empty($zaloVal) && str_starts_with($zaloVal, 'http')) {
        $zaloHref = $zaloVal;
    } elseif (!empty($zaloVal)) {
        $zaloHref = 'https://zalo.me/' . preg_replace('/[^0-9]/', '', $zaloVal);
    } elseif (!empty($primaryHotline)) {
        $zaloHref = 'https://zalo.me/' . preg_replace('/[^0-9]/', '', $primaryHotline);
    } else {
        $zaloHref = '';
    }
@endphp

{{-- Cum nut lien he noi tren goc phai man hinh.
     Chi ve nut nao co du lieu that trong cau hinh - khong ve nut tro toi '#'. --}}
<div class="waco-support">
    @if(!empty($primaryHotline))
        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $primaryHotline) }}"
           class="waco-support__btn waco-support__btn--ring" title="Gọi ngay: {{ $primaryHotline }}">
            @include('frontend.component.waco-icon', ['name' => 'phone', 'size' => 20])
            <span class="waco-support__label">{{ $primaryHotline }}</span>
        </a>
    @endif

    @if(!empty($emailVal))
        <a href="mailto:{{ $emailVal }}" class="waco-support__btn" title="Gửi email: {{ $emailVal }}">
            @include('frontend.component.waco-icon', ['name' => 'mail', 'size' => 20])
            <span class="waco-support__label">{{ $emailVal }}</span>
        </a>
    @endif

    @if(!empty($messengerHref))
        <a href="{{ $messengerHref }}" target="_blank" rel="noopener noreferrer"
           class="waco-support__btn" title="Chat Messenger">
            @include('frontend.component.waco-icon', ['name' => 'messenger', 'size' => 20])
            <span class="waco-support__label">Messenger</span>
        </a>
    @endif

    @if(!empty($zaloHref))
        <a href="{{ $zaloHref }}" target="_blank" rel="noopener noreferrer"
           class="waco-support__btn" title="Chat Zalo">
            <span class="waco-support__zalo">Zalo</span>
            <span class="waco-support__label">Zalo Chat</span>
        </a>
    @endif
</div>
