{{--
    Popup "Nhan tu van" / "Yeu cau bao gia".

    Mo bang cach dat data-waco-consult tren mot nut bat ky:
        data-waco-consult="quote"            -> tieu de "Yeu cau bao gia"
        data-waco-consult-product="HW-LP500" -> ghi kem ten san pham vao yeu cau

    Gui bang fetch nen nguoi dung khong bi tai lai trang, van giu nguyen cho dang
    xem. Tone mau lay theo mau thuong hieu: nen navy, nut xanh.
--}}
<div class="waco-modal" id="waco-consult" role="dialog" aria-modal="true"
     aria-labelledby="waco-consult-title" hidden>
    <div class="waco-modal__backdrop" data-waco-modal-close></div>

    <div class="waco-modal__panel">
        <button type="button" class="waco-modal__close" data-waco-modal-close aria-label="Đóng">&times;</button>

        <h2 class="waco-modal__title" id="waco-consult-title">Nhận tư vấn miễn phí</h2>
        <p class="waco-modal__description">
            Để lại thông tin, chuyên viên WACO sẽ liên hệ lại với bạn trong thời gian sớm nhất.
        </p>

        {{-- Ten san pham khach dang xem, chi hien khi mo tu trang chi tiet. --}}
        <p class="waco-modal__product" data-waco-consult-product-label hidden></p>

        <div class="waco-modal__alert" data-waco-consult-alert hidden></div>

        <form data-waco-consult-form novalidate>
            <input type="hidden" name="type" value="consult">
            <input type="hidden" name="product" value="">

            <div class="waco-modal__group">
                <label for="waco-consult-name">Họ và tên (*)</label>
                <input type="text" id="waco-consult-name" name="name" placeholder="Nhập họ và tên" required>
            </div>

            <div class="waco-modal__group">
                <label for="waco-consult-phone">Số điện thoại (*)</label>
                <input type="tel" id="waco-consult-phone" name="phone" placeholder="Nhập số điện thoại" required>
            </div>

            <div class="waco-modal__group">
                <label for="waco-consult-message">Lời nhắn</label>
                <textarea id="waco-consult-message" name="message" rows="3"
                          placeholder="Nội dung bạn cần tư vấn"></textarea>
            </div>

            <button type="submit" class="waco-btn waco-modal__submit">
                Gửi yêu cầu
                @include('frontend.component.waco-icon', ['name' => 'arrow-right'])
            </button>
        </form>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('waco-consult');
    if (!modal) return;

    var form = modal.querySelector('[data-waco-consult-form]');
    var alertBox = modal.querySelector('[data-waco-consult-alert]');
    var title = modal.querySelector('#waco-consult-title');
    var nhanSanPham = modal.querySelector('[data-waco-consult-product-label]');
    var oLoai = form.querySelector('[name="type"]');
    var oSanPham = form.querySelector('[name="product"]');
    var nutDaMo = null;

    function hienThongBao(noiDung, thanhCong) {
        alertBox.textContent = noiDung;
        alertBox.className = 'waco-modal__alert ' +
            (thanhCong ? 'waco-modal__alert--success' : 'waco-modal__alert--error');
        alertBox.hidden = false;
    }

    function mo(nut) {
        nutDaMo = nut;
        var loai = nut.getAttribute('data-waco-consult') || 'consult';
        var sanPham = nut.getAttribute('data-waco-consult-product') || '';

        oLoai.value = loai;
        oSanPham.value = sanPham;
        title.textContent = loai === 'quote' ? 'Yêu cầu báo giá' : 'Nhận tư vấn miễn phí';

        if (sanPham) {
            nhanSanPham.textContent = 'Sản phẩm quan tâm: ' + sanPham;
            nhanSanPham.hidden = false;
        } else {
            nhanSanPham.hidden = true;
        }

        alertBox.hidden = true;
        form.hidden = false;
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        modal.querySelector('#waco-consult-name').focus();
    }

    function dong() {
        modal.hidden = true;
        document.body.style.overflow = '';
        if (nutDaMo) nutDaMo.focus();
    }

    document.addEventListener('click', function (e) {
        var nut = e.target.closest('[data-waco-consult]');
        if (nut) {
            e.preventDefault();
            mo(nut);
            return;
        }
        if (e.target.closest('[data-waco-modal-close]')) dong();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hidden) dong();
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var nutGui = form.querySelector('[type="submit"]');
        nutGui.disabled = true;

        fetch('{{ route('consult.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new FormData(form)
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
        .then(function (kq) {
            if (kq.ok && kq.d.ok) {
                hienThongBao(kq.d.message, true);
                form.reset();
                // An form de thong bao thanh cong khong bi lap lai do bam hai lan.
                form.hidden = true;
            } else {
                hienThongBao((kq.d.errors || ['Gửi không thành công.']).join(' '), false);
            }
        })
        .catch(function () {
            hienThongBao('Không gửi được. Vui lòng kiểm tra kết nối rồi thử lại.', false);
        })
        .finally(function () { nutGui.disabled = false; });
    });
})();
</script>
