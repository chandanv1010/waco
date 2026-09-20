<?php

namespace App\Services\V1\Dealer;

use App\Models\DealerRegistration;
use App\Repositories\Core\DealerRegistrationRepository;
use App\Services\V1\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DealerRegistrationService extends BaseService
{
    protected $dealerRegistrationRepository;

    public function __construct(
        DealerRegistrationRepository $dealerRegistrationRepository
    ) {
        $this->dealerRegistrationRepository = $dealerRegistrationRepository;
    }

    public function paginate($request)
    {
        $perPage = $request->integer('perpage') > 0 ? $request->integer('perpage') : 20;

        $keyword = trim((string) $request->input('keyword'));

        $trangThai = $request->input('status');
        if (!array_key_exists((string) $trangThai, DealerRegistration::TRANG_THAI)) {
            $trangThai = null;
        }

        return $this->dealerRegistrationRepository->phanTrang(
            $keyword !== '' ? $keyword : null,
            $trangThai,
            $perPage
        );
    }

    /** Doi trang thai va ghi chu cua mot don. */
    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $this->dealerRegistrationRepository->update($id, [
                'status' => $request->input('status'),
                'note' => $request->input('note'),
            ]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cap nhat don dang ky dai ly that bai: ' . $e->getMessage());
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->dealerRegistrationRepository->delete($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Xoa don dang ky dai ly that bai: ' . $e->getMessage());
            return false;
        }
    }

    public function demTheoTrangThai(): array
    {
        return $this->dealerRegistrationRepository->demTheoTrangThai();
    }
}
