@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method'] === 'create' ? 'create' : 'edit']['title']])
@include('backend.dashboard.component.formError')

@php
    $url = ($config['method'] == 'create')
        ? route('dealer.store')
        : route('dealer.update', $dealer->id);
@endphp

<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-content">

                        <div class="row mb15">
                            <div class="col-lg-8">
                                <div class="form-row">
                                    <label class="control-label text-left">Tên điểm bán <span class="text-danger">(*)</span></label>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name', ($dealer->name) ?? '') }}"
                                        class="form-control"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-row">
                                    <label class="control-label text-left">Điện thoại</label>
                                    <input
                                        type="text"
                                        name="phone"
                                        value="{{ old('phone', ($dealer->phone) ?? '') }}"
                                        class="form-control"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-4">
                                <div class="form-row">
                                    <label class="control-label text-left">Tỉnh/Thành</label>
                                    <select name="province_code" class="form-control setupSelect2 province location" data-target="districts">
                                        <option value="">[Chọn Tỉnh/Thành]</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->code }}" {{ old('province_code', ($dealer->province_code) ?? '') == $province->code ? 'selected' : '' }}>{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-row">
                                    <label class="control-label text-left">Quận/Huyện</label>
                                    <select name="district_code" class="form-control districts setupSelect2 location" data-target="wards">
                                        <option value="">[Chọn Quận/Huyện]</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-row">
                                    <label class="control-label text-left">Phường/Xã</label>
                                    <select name="ward_code" class="form-control wards setupSelect2">
                                        <option value="">[Chọn Phường/Xã]</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left">Địa chỉ chi tiết</label>
                                    <input
                                        type="text"
                                        name="address"
                                        value="{{ old('address', ($dealer->address) ?? '') }}"
                                        class="form-control"
                                        placeholder="Số nhà, tên đường"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Vĩ độ (latitude)</label>
                                    <input
                                        type="text"
                                        name="latitude"
                                        value="{{ old('latitude', ($dealer->latitude) ?? '') }}"
                                        class="form-control"
                                        placeholder="21.0278"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Kinh độ (longitude)</label>
                                    <input
                                        type="text"
                                        name="longitude"
                                        value="{{ old('longitude', ($dealer->longitude) ?? '') }}"
                                        class="form-control"
                                        placeholder="105.8342"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <small class="text-muted">
                                    Lấy tọa độ: mở Google Maps, bấm chuột phải vào vị trí cửa hàng, dòng số
                                    đầu tiên hiện ra là <strong>vĩ độ, kinh độ</strong> — bấm vào để sao chép
                                    rồi dán vào hai ô trên. Ở Việt Nam vĩ độ nằm trong khoảng 8–24 và kinh độ
                                    102–110; nhập ngược hai ô sẽ bị báo lỗi.
                                    <strong>Bỏ trống thì điểm này không hiện trên bản đồ</strong>, nhưng vẫn
                                    nằm trong danh sách đại lý.
                                </small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-content">

                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left mb10">Loại điểm bán <span class="text-danger">(*)</span></label>
                                    <select name="type" class="form-control">
                                        @foreach($loai as $ma => $ten)
                                            <option value="{{ $ma }}" {{ old('type', ($dealer->type) ?? 'dealer') === $ma ? 'selected' : '' }}>{{ $ten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left mb10">Thứ tự</label>
                                    <input
                                        type="number"
                                        name="order"
                                        value="{{ old('order', ($dealer->order) ?? 0) }}"
                                        class="form-control"
                                        min="0"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left mb10">
                                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', ($dealer->is_featured) ?? 0) ? 'checked' : '' }}>
                                        Đại lý nổi bật
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left mb10">Trạng thái hiển thị</label>
                                    <select name="publish" class="form-control">
                                        <option value="2" {{ old('publish', ($dealer->publish) ?? 2) == 2 ? 'selected' : '' }}>Hiển thị</option>
                                        <option value="1" {{ old('publish', ($dealer->publish) ?? 2) == 1 ? 'selected' : '' }}>Ẩn</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @include('backend.dashboard.component.button')
    </div>
</form>

<script>
    // location.js doc ba bien nay de nap lai Quan/Huyen va Phuong/Xa khi mo
    // form sua - danh sach hai o do duoc lay bang ajax nen khong render san
    // duoc tu PHP.
    var province_id = '{{ old('province_code', ($dealer->province_code) ?? '') }}'
    var district_id = '{{ old('district_code', ($dealer->district_code) ?? '') }}'
    var ward_id = '{{ old('ward_code', ($dealer->ward_code) ?? '') }}'
</script>
