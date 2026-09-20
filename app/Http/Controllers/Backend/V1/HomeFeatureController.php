<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeFeature\StoreHomeFeatureRequest;
use App\Models\HomeFeature;
use App\Repositories\Core\HomeFeatureRepository;
use App\Services\V1\Home\HomeFeatureService;
use Illuminate\Http\Request;

/**
 * Man hinh sua cac khoi "icon + tieu de" tren trang chu va trang gioi thieu.
 *
 * Truoc day nhung dong nay chi sua duoc bang cach vao thang co so du lieu.
 */
class HomeFeatureController extends Controller
{
    protected $homeFeatureService;
    protected $homeFeatureRepository;

    public function __construct(
        HomeFeatureService $homeFeatureService,
        HomeFeatureRepository $homeFeatureRepository
    ) {
        $this->homeFeatureService = $homeFeatureService;
        $this->homeFeatureRepository = $homeFeatureRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'home.feature.index');

        $features = $this->homeFeatureService->paginate($request);
        $config = $this->config();
        $config['seo'] = __('messages.homeFeature');
        $template = 'backend.home_feature.index';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'features'
        ) + [
            'nhom' => HomeFeature::NHOM,
            'demTheoNhom' => $this->homeFeatureRepository->demTheoNhom(),
        ]);
    }

    public function create()
    {
        $this->authorize('modules', 'home.feature.create');

        $config = $this->config();
        $config['seo'] = __('messages.homeFeature');
        $config['method'] = 'create';
        $template = 'backend.home_feature.store';

        return view('backend.dashboard.layout', compact('template', 'config') + [
            'nhom' => HomeFeature::NHOM,
        ]);
    }

    public function store(StoreHomeFeatureRequest $request)
    {
        if ($this->homeFeatureService->create($request)) {
            return redirect()->route('home.feature.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('home.feature.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'home.feature.update');

        $feature = $this->homeFeatureRepository->findById($id);
        $config = $this->config();
        $config['seo'] = __('messages.homeFeature');
        $config['method'] = 'edit';
        $template = 'backend.home_feature.store';

        return view('backend.dashboard.layout', compact('template', 'config', 'feature') + [
            'nhom' => HomeFeature::NHOM,
        ]);
    }

    public function update($id, StoreHomeFeatureRequest $request)
    {
        if ($this->homeFeatureService->update($id, $request)) {
            return redirect()->route('home.feature.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('home.feature.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'home.feature.destroy');

        $feature = $this->homeFeatureRepository->findById($id);
        $config = $this->config();
        $config['seo'] = __('messages.homeFeature');
        $template = 'backend.home_feature.delete';

        return view('backend.dashboard.layout', compact('template', 'config', 'feature'));
    }

    public function destroy($id)
    {
        if ($this->homeFeatureService->destroy($id)) {
            return redirect()->route('home.feature.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('home.feature.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function config()
    {
        return [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'backend/library/finder.js',
            ],
            'model' => 'HomeFeature',
        ];
    }
}
