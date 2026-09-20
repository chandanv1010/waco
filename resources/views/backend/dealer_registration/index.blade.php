@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])

@php $trangThaiDangLoc = request('status'); @endphp

<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <h5>{{ $config['seo']['index']['table'] }}</h5>
                </div>
            </div>
            <div class="ibox-content">

                <div class="row mb15">
                    @foreach($trangThai as $ma => $ten)
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('dealer.registration.index', ['status' => $ma]) }}"
                               class="btn btn-block btn-outline {{ $trangThaiDangLoc === $ma ? 'btn-primary' : 'btn-default' }}"
                               style="white-space:normal; margin-bottom:10px;">
                                {{ $ten }}
                                <br><strong>{{ $demTheoTrangThai[$ma] ?? 0 }} đơn</strong>
                            </a>
                        </div>
                    @endforeach
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <a href="{{ route('dealer.registration.index') }}"
                           class="btn btn-block btn-outline {{ $trangThaiDangLoc ? 'btn-default' : 'btn-primary' }}"
                           style="white-space:normal; margin-bottom:10px;">
                            Tất cả
                            <br><strong>{{ array_sum($demTheoTrangThai) }} đơn</strong>
                        </a>
                    </div>
                </div>

                <form action="{{ route('dealer.registration.index') }}">
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
                                    <select name="status" class="form-control setupSelect2 mr10">
                                        <option value="">[Tất cả trạng thái]</option>
                                        @foreach($trangThai as $ma => $ten)
                                            <option value="{{ $ma }}" {{ $trangThaiDangLoc === $ma ? 'selected' : '' }}>{{ $ten }}</option>
                                        @endforeach
                                    </select>
                                    @include('backend.dashboard.component.keyword')
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-striped table-bordered mt20">
                    <thead>
                        <tr>
                            <th style="width:60px;" class="text-center">#</th>
                            <th style="width:170px;">Họ và tên</th>
                            <th style="width:130px;">Điện thoại</th>
                            <th>Công ty / Khu vực</th>
                            <th>Ghi chú</th>
                            <th style="width:130px;" class="text-center">Trạng thái</th>
                            <th style="width:140px;">Ngày gửi</th>
                            <th class="text-center" style="width:120px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrations as $registration)
                            <tr>
                                <td class="text-center">{{ $registration->id }}</td>
                                <td><span class="text-success">{{ $registration->name }}</span></td>
                                <td>
                                    {{-- Dung the tel: de bam thang tu may tinh va dien thoai. --}}
                                    <a href="tel:{{ $registration->phone }}">{{ $registration->phone }}</a>
                                </td>
                                <td>
                                    {{ $registration->company ?: '—' }}
                                    @if(!empty($registration->business_area))
                                        <br><small class="text-muted">{{ $registration->business_area }}</small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ \Illuminate\Support\Str::limit($registration->note, 120) }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="label label-{{ $registration->mauTrangThai() }}">{{ $registration->tenTrangThai() }}</span>
                                </td>
                                <td>{{ $registration->created_at ? $registration->created_at->format('H:i d/m/Y') : '' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('dealer.registration.edit', $registration->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                                    <a href="{{ route('dealer.registration.delete', $registration->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach

                        @if(!$registrations->count())
                            <tr>
                                <td colspan="8" class="text-center text-muted" style="padding:30px;">
                                    Chưa có đơn đăng ký nào.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                {{ $registrations->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
