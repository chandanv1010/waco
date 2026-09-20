@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])

@php
    // So muc ma thiet ke danh cho tung khoi. Nhieu hon hay it hon deu lam vo
    // bo cuc trang chu (vi du khoi "Vi sao chon WACO" xep 3 cot x 2 hang), nen
    // canh bao ngay tai day thay vi de phat hien khi xem trang.
    $soChuan = [
        'hero_usp' => 4,
        'commit' => 5,
        'about_badge' => 3,
        'why_waco' => 6,
        'stat' => 4,
    ];
    $nhomDangLoc = request('group');
@endphp

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

                <div class="alert alert-info">
                    Mỗi dòng ở đây là một ô nhỏ trên trang chủ. Cột <strong>Khối hiển thị</strong>
                    cho biết nó nằm ở đâu. Số mục trong mỗi khối đã được thiết kế cố định —
                    thêm hoặc bớt sẽ làm lệch bố cục.
                </div>

                <div class="row mb15">
                    @foreach($nhom as $ma => $ten)
                        @php
                            $dang = $demTheoNhom[$ma] ?? 0;
                            $chuan = $soChuan[$ma] ?? null;
                            $lech = !is_null($chuan) && $dang != $chuan;
                        @endphp
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('home.feature.index', ['group' => $ma]) }}"
                               class="btn btn-block btn-outline {{ $nhomDangLoc === $ma ? 'btn-primary' : 'btn-default' }}"
                               style="white-space:normal; margin-bottom:10px;">
                                {{ $ten }}
                                <br>
                                <strong class="{{ $lech ? 'text-danger' : 'text-success' }}">
                                    {{ $dang }}{{ $chuan ? ' / ' . $chuan : '' }} mục
                                </strong>
                            </a>
                        </div>
                    @endforeach
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <a href="{{ route('home.feature.index') }}"
                           class="btn btn-block btn-outline {{ $nhomDangLoc ? 'btn-default' : 'btn-primary' }}"
                           style="white-space:normal; margin-bottom:10px;">
                            Tất cả
                            <br><strong>&nbsp;</strong>
                        </a>
                    </div>
                </div>

                <form action="{{ route('home.feature.index') }}">
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
                                    <select name="group" class="form-control setupSelect2 mr10">
                                        <option value="">[Tất cả các khối]</option>
                                        @foreach($nhom as $ma => $ten)
                                            <option value="{{ $ma }}" {{ $nhomDangLoc === $ma ? 'selected' : '' }}>{{ $ten }}</option>
                                        @endforeach
                                    </select>
                                    @include('backend.dashboard.component.filterPublish')
                                    @include('backend.dashboard.component.keyword')
                                    <a href="{{ route('home.feature.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>Thêm mục</a>
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
                            <th style="width:210px;">Khối hiển thị</th>
                            <th style="width:60px;" class="text-center">Icon</th>
                            <th>Tiêu đề</th>
                            <th style="width:110px;" class="text-center">Con số</th>
                            <th style="width:70px;" class="text-center">Thứ tự</th>
                            <th class="text-center" style="width:100px;">Tình trạng</th>
                            <th class="text-center" style="width:120px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($features) && is_object($features))
                            @foreach($features as $feature)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $feature->id }}" class="input-checkbox checkBoxItem">
                                    </td>
                                    <td>{{ $feature->tenNhom() }}</td>
                                    <td class="text-center">
                                        @if(!empty($feature->icon))
                                            <img src="{{ $feature->icon }}" alt="" style="max-height:28px; max-width:40px;">
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Tieu de luu xuong dong that (vi du "Cong nghe\nHan Quoc") de
                                             ngat dong dung cho tren trang chu, nen phai giu nguyen o day. --}}
                                        <span class="text-success">{!! nl2br(e($feature->title)) !!}</span>
                                        @if(!empty($feature->description))
                                            <br><small class="text-muted">{{ $feature->description }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $feature->value ?: '—' }}</td>
                                    <td class="text-center">{{ $feature->order }}</td>
                                    <td class="text-center js-switch-{{ $feature->id }}">
                                        <input type="checkbox" value="{{ $feature->publish }}" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" {{ ($feature->publish == 2) ? 'checked' : '' }} data-modelId="{{ $feature->id }}" />
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('home.feature.edit', $feature->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                                        <a href="{{ route('home.feature.delete', $feature->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                {{ $features->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
