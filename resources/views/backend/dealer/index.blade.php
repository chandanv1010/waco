@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])

<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <h5>{{ $config['seo']['index']['table'] }}</h5>
                    @include('backend.dashboard.component.toolbox', ['model' => $config['model']])
                </div>
            </div>
            <div class="ibox-content">

                <form action="{{ route('dealer.index') }}">
                    <div class="filter-wrapper">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <div class="perpage">
                                @php $perpage = request('perpage') ?: old('perpage'); @endphp
                                <select name="perpage" class="form-control input-sm perpage filter mr10">
                                    @for($i = 20; $i <= 200; $i += 20)
                                        <option {{ ($perpage == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }} bản ghi</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="action">
                                <div class="uk-flex uk-flex-middle">
                                    <select name="province_code" class="form-control setupSelect2 mr10">
                                        <option value="">[Tất cả tỉnh/thành]</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->code }}" {{ request('province_code') == $province->code ? 'selected' : '' }}>{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                    <select name="type" class="form-control setupSelect2 mr10">
                                        <option value="">[Tất cả loại]</option>
                                        @foreach($loai as $ma => $ten)
                                            <option value="{{ $ma }}" {{ request('type') === $ma ? 'selected' : '' }}>{{ $ten }}</option>
                                        @endforeach
                                    </select>
                                    @include('backend.dashboard.component.filterPublish')
                                    @include('backend.dashboard.component.keyword')
                                    <a href="{{ route('dealer.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>Thêm điểm bán</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-striped table-bordered mt20">
                    <thead>
                        <tr>
                            <th style="width:50px;">
                                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                            </th>
                            <th>Tên điểm bán</th>
                            <th style="width:140px;">Tỉnh/Thành</th>
                            <th style="width:130px;">Điện thoại</th>
                            <th style="width:140px;">Loại</th>
                            <th style="width:110px;" class="text-center">Tọa độ</th>
                            <th class="text-center" style="width:100px;">Tình trạng</th>
                            <th class="text-center" style="width:120px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($dealers) && is_object($dealers))
                            @foreach($dealers as $dealer)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $dealer->id }}" class="input-checkbox checkBoxItem">
                                    </td>
                                    <td>
                                        <span class="text-success">{{ $dealer->name }}</span>
                                        @if($dealer->is_featured)
                                            <span class="label label-warning ml5">Nổi bật</span>
                                        @endif
                                        @if(!empty($dealer->address))
                                            <br><small class="text-muted">{{ $dealer->address }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $dealer->province->name ?? '—' }}</td>
                                    <td>{{ $dealer->phone ?: '—' }}</td>
                                    <td>{{ $dealer->tenLoai() }}</td>
                                    <td class="text-center">
                                        {{-- Thieu toa do thi ghim khong ve duoc len ban do, phai thay ngay
                                             trong danh sach chu khong doi mo tung ban ghi ra kiem tra. --}}
                                        @if(!is_null($dealer->latitude) && !is_null($dealer->longitude))
                                            <i class="fa fa-check text-success"></i>
                                        @else
                                            <span class="label label-danger">Chưa có</span>
                                        @endif
                                    </td>
                                    <td class="text-center js-switch-{{ $dealer->id }}">
                                        <input type="checkbox" value="{{ $dealer->publish }}" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" {{ ($dealer->publish == 2) ? 'checked' : '' }} data-modelId="{{ $dealer->id }}" />
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('dealer.edit', $dealer->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                                        <a href="{{ route('dealer.delete', $dealer->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @if(!$dealers->count())
                            <tr>
                                <td colspan="8" class="text-center text-muted" style="padding:30px;">
                                    Chưa có điểm bán nào. Bấm <strong>Thêm điểm bán</strong> để nhập điểm đầu tiên.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                {{ $dealers->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
