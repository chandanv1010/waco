<?php

namespace App\Services\V1\Dealer;

use App\Repositories\Core\DealerRepository;
use App\Services\V1\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Namespace phai la App\Services\V1\Dealer de cong tac doi trang thai tim thay
 * lop nay: no ghep duong dan tu ten model "Dealer" -> tu dau "Dealer".
 */
class DealerService extends BaseService
{
    protected $dealerRepository;

    public function __construct(
        DealerRepository $dealerRepository
    ) {
        $this->dealerRepository = $dealerRepository;
    }

    public function paginate($request)
    {
        $perPage = $request->integer('perpage') > 0 ? $request->integer('perpage') : 20;

        $where = [];

        if ($request->input('province_code')) {
            $where[] = ['province_code', '=', $request->input('province_code')];
        }

        if ($request->input('type')) {
            $where[] = ['type', '=', $request->input('type')];
        }

        $condition = [
            'keyword' => addslashes((string) $request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => $where,
        ];

        return $this->dealerRepository->pagination(
            ['id', 'name', 'address', 'phone', 'province_code', 'type', 'is_featured', 'publish', 'order'],
            $condition,
            $perPage,
            ['path' => 'dealer/index'],
            ['order', 'ASC'],
            [],
            ['province']
        );
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $this->dealerRepository->create($this->duLieu($request));
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Them diem ban that bai: ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $this->dealerRepository->update($id, $this->duLieu($request));
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cap nhat diem ban that bai: ' . $e->getMessage());
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->dealerRepository->delete($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Xoa diem ban that bai: ' . $e->getMessage());
            return false;
        }
    }

    private function duLieu($request): array
    {
        // O toa do de trong thi luu null chu khong luu 0 - toa do 0,0 la mot
        // diem ngoai khoi chau Phi, ghim se bay ra khoi ban do Viet Nam.
        $lat = trim((string) $request->input('latitude'));
        $lng = trim((string) $request->input('longitude'));

        return [
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'province_code' => $request->input('province_code') ?: null,
            'district_code' => $request->input('district_code') ?: null,
            'ward_code' => $request->input('ward_code') ?: null,
            'latitude' => $lat === '' ? null : $lat,
            'longitude' => $lng === '' ? null : $lng,
            'type' => $request->input('type') ?: 'dealer',
            'is_featured' => $request->boolean('is_featured') ? 1 : 0,
            'order' => $request->integer('order'),
            'publish' => $request->integer('publish') ?: 2,
        ];
    }
}
