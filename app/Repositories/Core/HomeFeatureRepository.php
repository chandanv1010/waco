<?php

namespace App\Repositories\Core;

use App\Repositories\BaseRepository;
use App\Models\HomeFeature;

class HomeFeatureRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        HomeFeature $model
    ) {
        $this->model = $model;
    }

    /**
     * Phan trang rieng cho man hinh nay.
     *
     * BaseRepository::pagination() chi nhan duoc MOT cap sap xep, ma danh sach
     * o day phai gom theo nhom truoc roi moi den thu tu trong nhom - dung dung
     * trinh tu cac khoi tren trang chu. Nen viet rieng thay vi lach bang bieu
     * thuc SQL tho.
     */
    public function phanTrang(array $where, ?int $publish, int $perPage)
    {
        return $this->model->newQuery()
            ->customWhere($where)
            ->publish($publish)
            ->orderBy('group')
            ->orderBy('order')
            ->paginate($perPage)
            ->withQueryString()
            ->withPath(env('APP_URL') . 'home-feature/index');
    }

    /** Dem so dong dang hien cua tung nhom, de canh bao khi thieu hoac thua muc. */
    public function demTheoNhom(): array
    {
        return $this->model->where('publish', 2)
            ->selectRaw('`group` AS nhom, COUNT(*) AS so_dong')
            ->groupBy('group')
            ->pluck('so_dong', 'nhom')
            ->toArray();
    }
}
