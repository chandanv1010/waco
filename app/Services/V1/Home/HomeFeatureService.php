<?php

namespace App\Services\V1\Home;

use App\Repositories\Core\HomeFeatureRepository;
use App\Services\V1\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Vi tri cua lop nay phai la App\Services\V1\Home: cong tac doi trang thai
 * (Ajax\DashboardController::changeStatus) tu ghep duong dan lop tu ten model -
 * "HomeFeature" -> tu dau la "Home" -> App\Services\V1\Home\HomeFeatureService.
 * Doi namespace la cong tac bat/tat hien thi trong danh sach ngung hoat dong.
 */
class HomeFeatureService extends BaseService
{
    protected $homeFeatureRepository;

    public function __construct(
        HomeFeatureRepository $homeFeatureRepository
    ) {
        $this->homeFeatureRepository = $homeFeatureRepository;
    }

    public function paginate($request)
    {
        $perPage = $request->integer('perpage') > 0 ? $request->integer('perpage') : 50;

        $where = [];

        // Bang nay khong co cot `name` nen khong dung duoc scope keyword mac
        // dinh - tu loc theo tieu de.
        $keyword = trim((string) $request->input('keyword'));
        if ($keyword !== '') {
            $where[] = ['title', 'LIKE', '%' . $keyword . '%'];
        }

        if ($request->input('group')) {
            $where[] = ['group', '=', $request->input('group')];
        }

        return $this->homeFeatureRepository->phanTrang(
            $where,
            $request->integer('publish') ?: null,
            $perPage
        );
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $this->homeFeatureRepository->create($this->duLieu($request));
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Them noi dung trang chu that bai: ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $this->homeFeatureRepository->update($id, $this->duLieu($request));
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cap nhat noi dung trang chu that bai: ' . $e->getMessage());
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->homeFeatureRepository->delete($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Xoa noi dung trang chu that bai: ' . $e->getMessage());
            return false;
        }
    }

    private function duLieu($request): array
    {
        return [
            'group' => $request->input('group'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'value' => $request->input('value'),
            'icon' => $request->input('icon'),
            'order' => $request->integer('order'),
            'publish' => $request->integer('publish') ?: 2,
        ];
    }
}
