{{--
    Khoi "He sinh thai san pham WACO" - 6 the danh muc.

    $categories do controller truyen vao, lay qua widget homepage-categories.
--}}
@if(!empty($categories) && $categories->count())
    <section class="waco__section waco-ecosystem">
        <div class="waco__container">
            <h2 class="waco__heading">{{ $intro['ecosystem_heading'] ?? '' }}</h2>
            <p class="waco__subheading">{{ $intro['ecosystem_description'] ?? '' }}</p>

            <div class="waco-ecosystem__grid">
                @foreach($categories as $cat)
                    @php $lang = $cat->languages->first(); @endphp
                    <a href="{{ write_url($lang->canonical, true, true) }}" class="waco-cat-card" title="{{ $lang->name }}">
                        @if(!empty($cat->icon))
                            <span class="waco-cat-card__icon">
                                <img src="{{ $cat->icon }}" alt="" aria-hidden="true" loading="lazy">
                            </span>
                        @endif
                        <h3 class="waco-cat-card__title">{{ $lang->name }}</h3>
                        <p class="waco-cat-card__description">{{ Str::limit(strip_tags($lang->description ?? ''), 70) }}</p>

                        <div class="waco-cat-card__media">
                            @if(!empty($cat->image))
                                <img src="{{ $cat->image }}" alt="{{ $lang->name }}"
                                     width="180" height="150" loading="lazy" decoding="async">
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
