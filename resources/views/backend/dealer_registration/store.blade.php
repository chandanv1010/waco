@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['edit']['title']])
@include('backend.dashboard.component.formError')

<form action="{{ route('dealer.registration.update', $registration->id) }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-title"><h5>Thông tin khách gửi</h5></div>
                    <div class="ibox-content">

                        {{-- Cac o duoi day chi de doc: day la thong tin khach tu dien tren
                             website, sua lai trong admin se lam sai lech thu khach gui. --}}
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Họ và tên</label>
                                    <input type="text" value="{{ $registration->name }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Điện thoại</label>
                                    <input type="text" value="{{ $registration->phone }}" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Công ty</label>
                                    <input type="text" value="{{ $registration->company }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Khu vực kinh doanh</label>
                                    <input type="text" value="{{ $registration->business_area }}" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Gửi từ</label>
                                    <input type="text" value="{{ $registration->source ?: 'Không rõ' }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Thời điểm gửi</label>
                                    <input type="text" value="{{ $registration->created_at ? $registration->created_at->format('H:i d/m/Y') : '' }}" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left">Ghi chú xử lý</label>
                                    <textarea name="note" rows="6" class="form-control" placeholder="Ghi lại nội dung đã trao đổi với khách">{{ old('note', $registration->note) }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left mb10">Trạng thái xử lý <span class="text-danger">(*)</span></label>
                                    <select name="status" class="form-control">
                                        @foreach($trangThai as $ma => $ten)
                                            <option value="{{ $ma }}" {{ old('status', $registration->status) === $ma ? 'selected' : '' }}>{{ $ten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <a href="tel:{{ $registration->phone }}" class="btn btn-primary btn-block">
                                    <i class="fa fa-phone mr5"></i>Gọi {{ $registration->phone }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('backend.dashboard.component.button')
    </div>
</form>
