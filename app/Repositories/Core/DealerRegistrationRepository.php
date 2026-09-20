<?php

namespace App\Repositories\Core;

use App\Repositories\BaseRepository;
use App\Models\DealerRegistration;

class DealerRegistrationRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        DealerRegistration $model
    ) {
        $this->model = $model;
    }

    /**
     * Phan trang rieng vi o tim kiem phai do ca ten LAN so dien thoai.
     *
     * Scope keyword mac dinh chi do cot `name`, con scope customWhere chi noi
     * cac dieu kien bang AND - ghep hai cai do lai se thanh "ten chua X VA so
     * dien thoai chua X", khong bao gio ra ket qua.
     */
    public function phanTrang(?string $keyword, ?string $status, int $perPage)
    {
        $query = $this->model->newQuery();

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('phone', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('company', 'LIKE', '%' . $keyword . '%');
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query->orderBy('id', 'DESC')
            ->paginate($perPage)
            ->withQueryString()
            ->withPath(env('APP_URL') . 'dealer/registration/index');
    }

    /** Dem don theo tung trang thai, hien thanh cac the o dau trang danh sach. */
    public function demTheoTrangThai(): array
    {
        return $this->model->selectRaw('status, COUNT(*) AS so_don')
            ->groupBy('status')
            ->pluck('so_don', 'status')
            ->toArray();
    }
}
