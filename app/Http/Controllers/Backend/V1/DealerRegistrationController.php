<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dealer\UpdateDealerRegistrationRequest;
use App\Models\DealerRegistration;
use App\Repositories\Core\DealerRegistrationRepository;
use App\Services\V1\Dealer\DealerRegistrationService;
use Illuminate\Http\Request;

/**
 * Don dang ky lam dai ly do khach gui tu form ngoai website.
 *
 * Khong co man hinh "them moi": ban ghi chi sinh ra tu form. Trong admin chi
 * doc, doi trang thai xu ly, ghi chu va xoa.
 */
class DealerRegistrationController extends Controller
{
    protected $dealerRegistrationService;
    protected $dealerRegistrationRepository;

    public function __construct(
        DealerRegistrationService $dealerRegistrationService,
        DealerRegistrationRepository $dealerRegistrationRepository
    ) {
        $this->dealerRegistrationService = $dealerRegistrationService;
        $this->dealerRegistrationRepository = $dealerRegistrationRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'dealer.registration.index');

        $registrations = $this->dealerRegistrationService->paginate($request);
        $config = $this->config();
        $config['seo'] = __('messages.dealerRegistration');
        $template = 'backend.dealer_registration.index';

        return view('backend.dashboard.layout', compact('template', 'config', 'registrations') + [
            'trangThai' => DealerRegistration::TRANG_THAI,
            'demTheoTrangThai' => $this->dealerRegistrationService->demTheoTrangThai(),
        ]);
    }

    public function edit($id)
    {
        $this->authorize('modules', 'dealer.registration.update');

        $registration = $this->dealerRegistrationRepository->findById($id);
        $config = $this->config();
        $config['seo'] = __('messages.dealerRegistration');
        $config['method'] = 'edit';
        $template = 'backend.dealer_registration.store';

        return view('backend.dashboard.layout', compact('template', 'config', 'registration') + [
            'trangThai' => DealerRegistration::TRANG_THAI,
        ]);
    }

    public function update($id, UpdateDealerRegistrationRequest $request)
    {
        if ($this->dealerRegistrationService->update($id, $request)) {
            return redirect()->route('dealer.registration.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('dealer.registration.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'dealer.registration.destroy');

        $registration = $this->dealerRegistrationRepository->findById($id);
        $config = $this->config();
        $config['seo'] = __('messages.dealerRegistration');
        $template = 'backend.dealer_registration.delete';

        return view('backend.dashboard.layout', compact('template', 'config', 'registration'));
    }

    public function destroy($id)
    {
        if ($this->dealerRegistrationService->destroy($id)) {
            return redirect()->route('dealer.registration.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('dealer.registration.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function config()
    {
        return [
            'css' => [],
            'js' => [],
            'model' => 'DealerRegistration',
        ];
    }
}
