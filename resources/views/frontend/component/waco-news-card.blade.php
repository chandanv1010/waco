@php
    /**
     * The bai viet: anh, ngay dang, tieu de, mo ta ngan.
     *
     * $post den tu hai nguon co hinh dang khac nhau:
     *   - Widget (trang chu): ten/duong dan nam trong quan he languages
     *   - Phan trang cua PostService: da join san nen nam thang tren ban ghi
     * Doc theo thu tu duoi day de dung duoc ca hai ma khong goi them truy van
     * (dung isset chu khong goi ->languages neu khong can, tranh N+1).
     */
    $ten = $post->name ?? null;
    $duongDan = $post->canonical ?? null;
    $moTa = $post->description ?? null;

    if ($ten === null) {
        $lang = $post->languages->first();
        $ten = $lang->pivot->name ?? $lang->name ?? '';
        $duongDan = $lang->pivot->canonical ?? $lang->canonical ?? '';
        $moTa = $lang->description ?? '';
    }

    $ngay = $post->released_at ?? ($post->created_at ?? null);
@endphp

<a href="{{ write_url($duongDan, true, true) }}" class="waco-news-card" title="{{ $ten }}">
    <div class="waco-news-card__media">
        @if(!empty($post->image))
            <img src="{{ $post->image }}" alt="{{ $ten }}"
                 width="300" height="190" loading="lazy" decoding="async">
        @endif
    </div>
    <div class="waco-news-card__body">
        @if($ngay)
            <div class="waco-news-card__date">{{ \Carbon\Carbon::parse($ngay)->format('d/m/Y') }}</div>
        @endif
        <h3 class="waco-news-card__title">{{ $ten }}</h3>
        @if(!empty($moTa))
            <p class="waco-news-card__description">{{ Str::limit(plain_text($moTa), 110) }}</p>
        @endif
    </div>
</a>
