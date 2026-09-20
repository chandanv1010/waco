<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;

/**
 * Cac khoi "icon + tieu de" lap lai tren trang chu va trang gioi thieu.
 *
 * Moi dong thuoc mot nhom (cot `group`), tuong ung mot khoi trong thiet ke:
 *   hero_usp     4 diem manh ngay duoi banner
 *   commit       5 con so tren dai cam ket
 *   about_badge  3 huy hieu trong khoi gioi thieu
 *   why_waco     6 ly do chon WACO
 *   stat         4 o so lieu mang luoi dai ly
 *
 * View doc qua WacoComposer nen khong can quan he Eloquent nao.
 */
class HomeFeature extends Model
{
    use HasFactory, QueryScopes;

    protected $table = 'home_features';

    protected $fillable = [
        'group',
        'title',
        'description',
        'value',
        'icon',
        'order',
        'publish',
    ];

    /** Ten hien thi cua tung nhom, dung chung cho o chon va cot trong bang. */
    public const NHOM = [
        'hero_usp'    => 'Điểm mạnh dưới banner (4 mục)',
        'commit'      => 'Dải cam kết (5 mục)',
        'about_badge' => 'Huy hiệu khối giới thiệu (3 mục)',
        'why_waco'    => 'Vì sao chọn WACO (6 mục)',
        'stat'        => 'Số liệu mạng lưới đại lý (4 mục)',
    ];

    public function tenNhom(): string
    {
        return self::NHOM[$this->group] ?? $this->group;
    }
}
