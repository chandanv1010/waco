{{--
    Phan trang.

    Duong dan cua site co dang /chuyen-muc/trang-2.html chu khong phai ?page=2,
    nen phai doi lai duong dan ma Laravel sinh ra.
--}}
@if($model->hasPages())
    @php
        $suffix = config('apps.general.suffix');

        $doiDuongDan = function ($url) use ($suffix) {
            if (!$url) {
                return null;
            }
            $url = str_replace('?page=', '/trang-', $url) . $suffix;
            // Trang 1 khong co hau to /trang-1.
            return str_replace('/trang-1' . $suffix, $suffix, $url);
        };
    @endphp

    <nav class="waco-pagination" aria-label="Phân trang">
        @php $truoc = $doiDuongDan($model->previousPageUrl()); @endphp
        @if($truoc)
            <a href="{{ $truoc }}" class="waco-pagination__nav" rel="prev" aria-label="Trang trước">‹</a>
        @else
            <span class="waco-pagination__nav is-disabled" aria-hidden="true">‹</span>
        @endif

        @foreach($model->getUrlRange(max(1, $model->currentPage() - 2), min($model->lastPage(), $model->currentPage() + 2)) as $trang => $url)
            @if($trang === $model->currentPage())
                <span class="waco-pagination__item is-active" aria-current="page">{{ $trang }}</span>
            @else
                <a href="{{ $doiDuongDan($url) }}" class="waco-pagination__item">{{ $trang }}</a>
            @endif
        @endforeach

        @if($model->lastPage() > $model->currentPage() + 2)
            <span class="waco-pagination__gap">…</span>
            <a href="{{ $doiDuongDan($model->url($model->lastPage())) }}" class="waco-pagination__item">
                {{ $model->lastPage() }}
            </a>
        @endif

        @php $sau = $doiDuongDan($model->nextPageUrl()); @endphp
        @if($sau)
            <a href="{{ $sau }}" class="waco-pagination__nav" rel="next" aria-label="Trang sau">›</a>
        @else
            <span class="waco-pagination__nav is-disabled" aria-hidden="true">›</span>
        @endif
    </nav>
@endif
