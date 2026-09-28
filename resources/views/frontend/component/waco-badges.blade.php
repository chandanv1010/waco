{{--
    Ba huy hieu trong khoi gioi thieu (nhom `about_badge` cua home_features).

    Dung chung cho trang chu va trang gioi thieu - hai noi in y het nhau, de
    roi hai cho thi sua mot ben quen ben kia.

    Quan tri co the gan mot bai viet cho tung huy hieu (o "Bai viet mo ra khi
    bam" trong Noi dung trang chu). Co gan thi huy hieu thanh the <a>, khong
    gan thi van la mot the <div> tinh nhu cu.

    Tham so:
      $badges - Collection cac dong home_features nhom about_badge
--}}
@if(isset($badges) && $badges->count())
    <div class="waco-about__badges">
        @foreach($badges as $badge)
            @php
                // post_canonical do WacoComposer join san; null khi khong gan
                // bai viet, hoac bai do da an / da xoa.
                $duongDan = !empty($badge->post_canonical)
                    ? write_url($badge->post_canonical, true, true)
                    : null;
                $the = $duongDan ? 'a' : 'div';
            @endphp

            <{{ $the }} class="waco-badge{{ $duongDan ? ' waco-badge--link' : '' }}"
                @if($duongDan) href="{{ $duongDan }}" title="{{ trim(str_replace("\n", ' ', $badge->title)) }}" @endif>
                @if(!empty($badge->icon))
                    <span class="waco-badge__icon">
                        <img src="{{ $badge->icon }}" alt="" aria-hidden="true" loading="lazy">
                    </span>
                @endif
                <span class="waco-badge__title">{!! nl2br(e($badge->title)) !!}</span>
            </{{ $the }}>
        @endforeach
    </div>
@endif
