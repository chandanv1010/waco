{{--
    Dai cam ket: 5 chi so tren nen navy.

    Tren trang chu no de len day hero (lop .waco-commit--overlap), o cac trang
    khac thi dung binh thuong trong luong noi dung.
--}}
@php
    $commit = $features['commit'] ?? collect();
@endphp

@if($commit->count())
    <section class="waco-commit{{ !empty($overlap) ? ' waco-commit--overlap' : '' }}">
        <div class="waco-commit__inner">
            <div class="waco-commit__panel">
                @foreach($commit as $item)
                    <div class="waco-commit-item">
                        @if(!empty($item->icon))
                            <span class="waco-commit-item__icon">
                                <img src="{{ $item->icon }}" alt="" aria-hidden="true" loading="lazy">
                            </span>
                        @endif
                        <div>
                            <div class="waco-commit-item__value">{{ $item->value }}</div>
                            <div class="waco-commit-item__label">{{ $item->title }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
