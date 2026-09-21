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
                        @php
                            // Truoc day cat bang Str::limit(70): no dem theo KY TU nen cat
                            // ngang giua tu, "nhieu khoang chat" thanh "nhieu khoa..." -
                            // doc ra mot tu khac han. Str::words cat theo tu nen khong bao
                            // gio lam hong chu.
                            //
                            // html_entity_decode chay TRUOC strip_tags: quan tri go trong
                            // trinh soan thao nen mo ta hay lan &nbsp; &amp; &#39;, khong
                            // giai ma thi in nguyen chuoi do ra man hinh.
                            $moTaNgan = Str::words(
                                trim(preg_replace('/\s+/u', ' ',
                                    html_entity_decode(strip_tags($lang->description ?? ''), ENT_QUOTES, 'UTF-8')
                                )),
                                20,
                                '…'
                            );
                        @endphp
                        <p class="waco-cat-card__description">{{ $moTaNgan }}</p>

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
