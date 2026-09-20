<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

/**
 * Don dang ky lam dai ly, gui tu form o cuoi trang chu va trang "He thong dai ly".
 *
 * Khach khong tao ban ghi nay trong admin - chi doc, doi trang thai va ghi chu.
 */
class DealerRegistration extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $table = 'dealer_registrations';

    protected $fillable = [
        'name',
        'phone',
        'company',
        'business_area',
        'note',
        'status',
        'source',
    ];

    /** Trang thai xu ly don. */
    public const TRANG_THAI = [
        'new'       => 'Mới gửi',
        'contacted' => 'Đã liên hệ',
        'working'   => 'Đang trao đổi',
        'done'      => 'Đã chốt',
        'cancelled' => 'Không phù hợp',
    ];

    /** Mau nhan trang thai trong bang danh sach. */
    public const MAU_TRANG_THAI = [
        'new'       => 'danger',
        'contacted' => 'warning',
        'working'   => 'primary',
        'done'      => 'success',
        'cancelled' => 'default',
    ];

    public function tenTrangThai(): string
    {
        return self::TRANG_THAI[$this->status] ?? $this->status;
    }

    public function mauTrangThai(): string
    {
        return self::MAU_TRANG_THAI[$this->status] ?? 'default';
    }
}
