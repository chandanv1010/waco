@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])

<form action="{{ route('home.feature.destroy', $feature->id) }}" method="post" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>Bạn đang muốn xóa mục: <strong>{{ str_replace("\n", ' ', $feature->title) }}</strong></p>
                        <p>Mục này thuộc khối: <strong>{{ $feature->tenNhom() }}</strong></p>
                        <p class="text-danger">
                            Lưu ý: số mục trong mỗi khối đã được thiết kế cố định. Xóa bớt sẽ làm
                            lệch bố cục trang chủ. Nếu chỉ muốn tạm ẩn, hãy quay lại và tắt công tắc
                            ở cột Tình trạng thay vì xóa.
                        </p>
                        <p>Không thể khôi phục sau khi xóa.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label class="control-label text-left">Tiêu đề</label>
                                    <textarea class="form-control" rows="2" readonly>{{ $feature->title }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Khối hiển thị</label>
                                    <input type="text" value="{{ $feature->tenNhom() }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label class="control-label text-left">Con số</label>
                                    <input type="text" value="{{ $feature->value }}" class="form-control" readonly>
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
