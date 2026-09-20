{{--
    Form "Tro thanh dai ly phan phoi WACO".

    Gui ve route dealer.register, luu vao bang dealer_registrations. Dung o nhieu
    trang nen tach rieng; nhan va mo ta lay tu bang introduces de admin sua duoc.
--}}
<div class="waco-dealer-form">
    <h2 class="waco-dealer-form__heading">{!! $intro['dealer_form_heading'] ?? '' !!}</h2>
    <p class="waco-dealer-form__description">{{ $intro['dealer_form_description'] ?? '' }}</p>

    @if(session('dealer_success'))
        <div class="waco-dealer-form__alert waco-dealer-form__alert--success">
            {{ session('dealer_success') }}
        </div>
    @endif

    {{-- Chi hien loi cua chinh form nay. Trang lien he cung co mot form khac
         tren cung trang, neu in ca $errors->all() thi loi cua form kia se nhay
         sang day. --}}
    @if($errors->dealer->any())
        <div class="waco-dealer-form__alert waco-dealer-form__alert--error">
            <ul>
                @foreach($errors->dealer->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dealer.register') }}" method="POST">
        @csrf
        <input type="hidden" name="source" value="{{ request()->path() }}">

        <div class="waco-dealer-form__row">
            <div class="waco-dealer-form__group">
                <label for="dealer-name">Họ và tên: (*)</label>
                <input type="text" id="dealer-name" name="name" value="{{ old('name') }}"
                       placeholder="Nhập thông tin" required>
            </div>
            <div class="waco-dealer-form__group">
                <label for="dealer-phone">Số điện thoại: (*)</label>
                <input type="tel" id="dealer-phone" name="phone" value="{{ old('phone') }}"
                       placeholder="Nhập thông tin" required>
            </div>
        </div>

        <div class="waco-dealer-form__group">
            <label for="dealer-company">Tên công ty/Cửa hàng</label>
            <input type="text" id="dealer-company" name="company" value="{{ old('company') }}"
                   placeholder="Nhập thông tin">
        </div>

        <div class="waco-dealer-form__group">
            <label for="dealer-area">Địa chỉ khu vực kinh doanh</label>
            <input type="text" id="dealer-area" name="business_area" value="{{ old('business_area') }}"
                   placeholder="Nhập thông tin">
        </div>

        <button type="submit" class="waco-btn">Gửi yêu cầu</button>
    </form>
</div>
