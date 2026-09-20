<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

/**
 * Diem ban / dai ly de ve ghim len ban do o trang "He thong dai ly".
 *
 * Ba cot dia gioi luu theo `code` cua provinces/districts/wards chu khong phai
 * so nguyen - do khoa chinh cua ba bang do la varchar.
 */
class Dealer extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $table = 'dealers';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'province_code',
        'district_code',
        'ward_code',
        'latitude',
        'longitude',
        'type',
        'is_featured',
        'publish',
        'order',
    ];

    /** Phan loai diem ban, quyet dinh mau ghim tren ban do. */
    public const LOAI = [
        'dealer'      => 'Đại lý',
        'distributor' => 'Nhà phân phối',
        'service'     => 'Trung tâm bảo hành',
        'showroom'    => 'Showroom',
    ];

    public function tenLoai(): string
    {
        return self::LOAI[$this->type] ?? $this->type;
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_code', 'code');
    }
}
