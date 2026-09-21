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
                            {{-- Gia tri that de trong data-waco-dem de JS dem len.
                                 Chu hien ra van la gia tri day du, nen tat JS hay
                                 trinh doc man hinh van thay dung so. --}}
                            <div class="waco-commit-item__value" data-waco-dem="{{ $item->value }}">{{ $item->value }}</div>
                            <div class="waco-commit-item__label">{{ $item->title }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <script>
    // Dem so tu 0 len gia tri that khi dai cam ket cuon vao tam nhin.
    //
    // Gia tri quan tri nhap co the kem ky tu: "20+", "1000+", "63". Tach rieng
    // phan so de dem, giu nguyen phan chu o hai dau ma gan lai sau.
    (function () {
        var cacO = document.querySelectorAll('[data-waco-dem]');
        if (!cacO.length) return;

        // Nguoi dung bat "giam chuyen dong" trong he dieu hanh thi khong chay
        // hieu ung - hien thang so cuoi.
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        function tach(chuoi) {
            var khop = String(chuoi).match(/^(\D*)([\d.,]+)(.*)$/);
            if (!khop) return null;

            // Bo dau phan cach hang nghin truoc khi doi sang so.
            var so = parseFloat(khop[2].replace(/[.,]/g, ''));
            if (isNaN(so)) return null;

            return { truoc: khop[1], so: so, sau: khop[3] };
        }

        function dem(o) {
            var goc = o.getAttribute('data-waco-dem');
            var phan = tach(goc);
            if (!phan) return;

            var thoiGian = 1400;
            var batDau = null;

            function buoc(nhipHienTai) {
                if (batDau === null) batDau = nhipHienTai;

                var tien = Math.min((nhipHienTai - batDau) / thoiGian, 1);

                if (tien >= 1) {
                    // Khung cuoi tra ve DUNG chuoi quan tri da nhap, khong tu
                    // dinh dang lai. Nhap "1000+" thi ket thuc van la "1000+",
                    // nhap "1.000+" thi van la "1.000+".
                    o.textContent = goc;
                    return;
                }

                // Cham dan ve cuoi cho tu nhien hon la chay deu.
                var muot = 1 - Math.pow(1 - tien, 3);
                o.textContent = phan.truoc + Math.round(phan.so * muot) + phan.sau;

                requestAnimationFrame(buoc);
            }

            o.textContent = phan.truoc + '0' + phan.sau;
            requestAnimationFrame(buoc);
        }

        // Chi dem khi khoi that su hien ra tren man hinh, va dem dung mot lan.
        if (!('IntersectionObserver' in window)) {
            cacO.forEach(dem);
            return;
        }

        var theoDoi = new IntersectionObserver(function (danhSach) {
            danhSach.forEach(function (muc) {
                if (!muc.isIntersecting) return;
                theoDoi.unobserve(muc.target);
                dem(muc.target);
            });
        }, { threshold: 0.4 });

        cacO.forEach(function (o) { theoDoi.observe(o); });
    })();
    </script>
@endif
