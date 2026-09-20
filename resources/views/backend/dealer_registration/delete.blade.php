@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])

<form action="{{ route('dealer.registration.destroy', $registration->id) }}" method="post" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>Bạn đang muốn xóa đơn đăng ký của: <strong>{{ $registration->name }}</strong> — {{ $registration->phone }}</p>
                        <p class="text-danger">
                            Đây là thông tin khách hàng tự gửi. Nếu chỉ muốn dọn danh sách, hãy quay
                            lại và đổi trạng thái thành <strong>Không phù hợp</strong> thay vì xóa —
                            như vậy vẫn còn dữ liệu để đối chiếu về sau.
                        </p>
                        <p>Không thể khôi phục sau khi xóa.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
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
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left">Công ty</label>
                                    <input type="text" value="{{ $registration->company }}" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right mb15">
            <button class="btn btn-danger" type="submit" name="send" value="send">Xóa dữ liệu</button>
        </div>
    </div>
</form>
