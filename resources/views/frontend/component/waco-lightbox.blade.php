{{--
    Lightbox xem anh phong to.

    Bat moi the <a data-waco-lightbox> trong trang, nen dung lai duoc cho bat ky
    luoi anh nao chu khong rieng album bai viet. Chi in MOT lan cho ca trang -
    @once lo viec do, include bao nhieu lan cung khong nhan doi.

    Viet tay, khong keo them thu vien: ca trang WACO chi can bay nhieu day.
    Khong co JS thi the <a> van mo thang file anh - khong bam gi bi hong.
--}}
@once
    <div class="waco-lightbox" data-waco-lightbox-hop hidden>
        <div class="waco-lightbox__nen" data-waco-lightbox-dong></div>

        <button type="button" class="waco-lightbox__nut waco-lightbox__nut--dong"
                data-waco-lightbox-dong aria-label="Đóng">&times;</button>

        <button type="button" class="waco-lightbox__nut waco-lightbox__nut--truoc"
                data-waco-lightbox-truoc aria-label="Ảnh trước">&lsaquo;</button>

        <figure class="waco-lightbox__khung">
            <img src="" alt="" data-waco-lightbox-anh>
            <figcaption data-waco-lightbox-dem></figcaption>
        </figure>

        <button type="button" class="waco-lightbox__nut waco-lightbox__nut--sau"
                data-waco-lightbox-sau aria-label="Ảnh sau">&rsaquo;</button>
    </div>

    <script>
        (function () {
            var hop = document.querySelector('[data-waco-lightbox-hop]');
            if (!hop) return;

            var anh = hop.querySelector('[data-waco-lightbox-anh]');
            var dem = hop.querySelector('[data-waco-lightbox-dem]');
            var nutTruoc = hop.querySelector('[data-waco-lightbox-truoc]');
            var nutSau = hop.querySelector('[data-waco-lightbox-sau]');

            // Danh sach anh cua lan mo hien tai va nut da bam - de tra tieu diem
            // ve dung cho cu sau khi dong, nguoi dung ban phim khong bi nhay len
            // dau trang.
            var danhSach = [];
            var viTri = 0;
            var nutGoc = null;

            function hien() {
                var muc = danhSach[viTri];
                if (!muc) return;
                anh.src = muc.getAttribute('href');
                anh.alt = (muc.querySelector('img') || {}).alt || '';
                dem.textContent = (viTri + 1) + ' / ' + danhSach.length;

                // Mot anh thi khong hien hai nut chuyen.
                var nhieuAnh = danhSach.length > 1;
                nutTruoc.hidden = !nhieuAnh;
                nutSau.hidden = !nhieuAnh;
            }

            function chuyen(buoc) {
                if (!danhSach.length) return;
                // Cong them do dai truoc khi chia du: -1 % 3 trong JS ra -1
                // chu khong phai 2, nen bam lui o anh dau se trong tron.
                viTri = (viTri + buoc + danhSach.length) % danhSach.length;
                hien();
            }

            function mo(nut) {
                // Gom cac anh CUNG MOT luoi voi nut vua bam; trang co hai luoi
                // thi moi luoi la mot bo rieng.
                var khoi = nut.closest('.waco-gallery') || document;
                danhSach = Array.prototype.slice.call(khoi.querySelectorAll('[data-waco-lightbox]'));
                viTri = danhSach.indexOf(nut);
                if (viTri < 0) viTri = 0;

                nutGoc = nut;
                hop.hidden = false;
                // Khoa cuon giong popup tu van, de ca trang chi co mot cach lam.
                document.body.style.overflow = 'hidden';
                hien();
            }

            function dong() {
                hop.hidden = true;
                document.body.style.overflow = '';
                // Go han thuoc tinh src de trinh duyet khong giu anh lon trong
                // bo nho. Dat src = '' thi trinh duyet hieu la duong dan rong -
                // no se di tai chinh trang hien tai ve lam anh.
                anh.removeAttribute('src');
                if (nutGoc) { nutGoc.focus(); nutGoc = null; }
            }

            // Bat o cap document: luoi anh co the duoc chen sau khi trang da
            // tai xong (vi du tai them bai viet), khong phai gan lai tung nut.
            document.addEventListener('click', function (e) {
                var nut = e.target.closest ? e.target.closest('[data-waco-lightbox]') : null;
                if (nut) {
                    e.preventDefault();
                    mo(nut);
                    return;
                }
                if (e.target.closest('[data-waco-lightbox-dong]')) { dong(); return; }
                if (e.target.closest('[data-waco-lightbox-truoc]')) { chuyen(-1); return; }
                if (e.target.closest('[data-waco-lightbox-sau]')) { chuyen(1); }
            });

            document.addEventListener('keydown', function (e) {
                if (hop.hidden) return;
                if (e.key === 'Escape') dong();
                if (e.key === 'ArrowLeft') chuyen(-1);
                if (e.key === 'ArrowRight') chuyen(1);
            });
        })();
    </script>
@endonce
