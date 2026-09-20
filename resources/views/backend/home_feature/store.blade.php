@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method'] === 'create' ? 'create' : 'edit']['title']])
@include('backend.dashboard.component.formError')

@php
    $url = ($config['method'] == 'create')
        ? route('home.feature.store')
        : route('home.feature.update', $feature->id);
@endphp

<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-content">

                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left">Khối hiển thị <span class="text-danger">(*)</span></label>
                                    <select name="group" class="form-control">
                                        @foreach($nhom as $ma => $ten)
                                            <option value="{{ $ma }}" {{ old('group', ($feature->group) ?? '') === $ma ? 'selected' : '' }}>{{ $ten }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Chọn đúng khối thì mục mới hiện ra ở vị trí mong muốn trên trang chủ.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left">Tiêu đề <span class="text-danger">(*)</span></label>
                                    {{-- Dung textarea chu khong phai input: nhieu tieu de can ngat dong
                                         dung cho ("Cong nghe" xuong dong "Han Quoc"), va cho ngat dong
                                         do luu bang ky tu xuong dong that trong co so du lieu. --}}
                                    <textarea name="title" rows="2" class="form-control" placeholder="Nhấn Enter để xuống dòng nếu muốn tách chữ">{{ old('title', ($feature->title) ?? '') }}</textarea>
                                    <small class="text-muted">Nhấn Enter để xuống dòng. Chỗ xuống dòng sẽ hiện đúng như vậy ngoài trang.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left">Mô tả ngắn</label>
                                    <input
                                        type="text"
                                        name="description"
                                        value="{{ old('description', ($feature->description) ?? '') }}"
                                        class="form-control"
                                        autocomplete="off"
                                    >
                                    <small class="text-muted">Dòng chữ nhỏ dưới tiêu đề. Khối "Vì sao chọn WACO" dùng ô này, các khối khác có thể để trống.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Con số</label>
                                    <input
                                        type="text"
                                        name="value"
                                        value="{{ old('value', ($feature->value) ?? '') }}"
                                        class="form-control"
                                        placeholder="Ví dụ: 63, 1.000+, 24/7"
                                        autocomplete="off"
                                    >
                                    <small class="text-muted">Chỉ dùng cho khối "Dải cam kết" và "Số liệu mạng lưới". Các khối khác bỏ trống.</small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Thứ tự</label>
                                    <input
                                        type="number"
                                        name="order"
                                        value="{{ old('order', ($feature->order) ?? 0) }}"
                                        class="form-control"
                                        min="0"
                                    >
                                    <small class="text-muted">Số nhỏ đứng trước. Các mục trong cùng một khối xếp theo số này.</small>
                                </div>
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
                                    <label class="control-label text-left mb10">Icon</label>
                                    <span
                                        class="image img-cover image-target"
                                        style="height:160px; padding:20px; text-align:center; border:1px dashed #b8b2b2; display:flex; align-items:center; justify-content:center; cursor:pointer;"
                                    >
                                        <img src="{{ old('icon', ($feature->icon) ?? '') ?: 'backend/img/image.svg' }}" alt="" style="width:90px;height:90px;object-fit:contain;">
                                    </span>
                                    <input type="hidden" name="icon" value="{{ old('icon', ($feature->icon) ?? '') }}">
                                    <small class="text-muted">Bấm vào ô trên để chọn ảnh. Nên dùng ảnh PNG nền trong suốt.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left mb10">Trạng thái hiển thị</label>
                                    <select name="publish" class="form-control">
                                        <option value="2" {{ old('publish', ($feature->publish) ?? 2) == 2 ? 'selected' : '' }}>Hiển thị</option>
                                        <option value="1" {{ old('publish', ($feature->publish) ?? 2) == 1 ? 'selected' : '' }}>Ẩn</option>
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
