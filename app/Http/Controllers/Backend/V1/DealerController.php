<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dealer\StoreDealerRequest;
use App\Models\Dealer;
use App\Models\Province;
use App\Repositories\Core\DealerRepository;
use App\Services\V1\Dealer\DealerService;
use Illuminate\Http\Request;

/**
 * Man hinh quan ly diem ban / dai ly - nguon du lieu cho ban do o trang
 * "He thong dai ly".
 */
class DealerController extends Controller
{
    protected $dealerService;
    protected $dealerRepository;

    public function __construct(
        DealerService $dealerService,
        DealerRepository $dealerRepository
    ) {
        $this->dealerService = $dealerService;
        $this->dealerRepository = $dealerRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'dealer.index');

        $dealers = $this->dealerService->paginate($request);
        $config = $this->config();
        $config['seo'] = __('messages.dealer');
        $template = 'backend.dealer.index';

        return view('backend.dashboard.layout', compact('template', 'config', 'dealers') + [
            'provinces' => $this->danhSachTinh(),
            'loai' => Dealer::LOAI,
        ]);
    }

    public function create()
    {
        $this->authorize('modules', 'dealer.create');

        $config = $this->config();
        $config['seo'] = __('messages.dealer');
        $config['method'] = 'create';
        $template = 'backend.dealer.store';

        return view('backend.dashboard.layout', compact('template', 'config') + [
            'provinces' => $this->danhSachTinh(),
            'loai' => Dealer::LOAI,
        ]);
    }

    public function store(StoreDealerRequest $request)
    {
        if ($this->dealerService->create($request)) {
            return redirect()->route('dealer.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('dealer.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'dealer.update');

        $dealer = $this->dealerRepository->findById($id);
        $config = $this->config();
        $config['seo'] = __('messages.dealer');
        $config['method'] = 'edit';
        $template = 'backend.dealer.store';

        return view('backend.dashboard.layout', compact('template', 'config', 'dealer') + [
            'provinces' => $this->danhSachTinh(),
            'loai' => Dealer::LOAI,
        ]);
    }

    public function update($id, StoreDealerRequest $request)
    {
        if ($this->dealerService->update($id, $request)) {
            return redirect()->route('dealer.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('dealer.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'dealer.destroy');

        $dealer = $this->dealerRepository->findById($id);
        $config = $this->config();
        $config['seo'] = __('messages.dealer');
        $template = 'backend.dealer.delete';

        return view('backend.dashboard.layout', compact('template', 'config', 'dealer'));
    }

    public function destroy($id)
    {
        if ($this->dealerService->destroy($id)) {
            return redirect()->route('dealer.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('dealer.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function danhSachTinh()
    {
        return Province::select('code', 'name')->orderBy('name')->get();
    }

    private function config()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/css/plugins/switchery/switchery.css',
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
            ],
            'model' => 'Dealer',
        ];
    }
}
