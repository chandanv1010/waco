<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Doi "63 tinh thanh" thanh "34 tinh thanh" tren toan bo noi dung.
 *
 * Con so nay nam trong DU LIEU chu khong nam trong ma nguon, nen `git pull`
 * khong mang no sang may chu. Gom thanh seeder de chay mot lenh la xong, khong
 * phai go tay SQL hay mo tung man hinh quan tri.
 *
 * Chay lai nhieu lan khong sao: lan thu hai khong con gi khop de doi.
 *
 *   php artisan db:seed --class=WacoSoTinhThanhSeeder --force
 */
class WacoSoTinhThanhSeeder extends Seeder
{
    /** So tinh thanh cu -> moi, sau khi sap nhap don vi hanh chinh. */
    private const CU = '63';
    private const MOI = '34';

    /** Nhung cot van ban co the co cau "63 tinh thanh" do quan tri go vao. */
    private const COT_VAN_BAN = [
        ['introduces', 'content'],
        ['systems', 'content'],
        ['post_language', 'name'],
        ['post_language', 'description'],
        ['post_language', 'content'],
        ['post_language', 'meta_description'],
        ['product_language', 'description'],
        ['product_language', 'content'],
        ['product_language', 'meta_description'],
    ];

    public function run(): void
    {
        // 1. O "Con so" trong dai cam ket va bang so lieu mang luoi dai ly.
        //    Loc them theo tieu de: nhom `commit` con cac con so khac, lo co
        //    dong nao cung bang 63 thi khong duoc dong vao.
        $daDoi = DB::table('home_features')
            ->where('value', self::CU)
            ->whereIn('group', ['commit', 'stat'])
            ->where('title', 'LIKE', '%nh thành%')
            ->update(['value' => self::MOI]);

        // 2. Cac doan van ban viet thang "63 tinh thanh".
        foreach (self::COT_VAN_BAN as [$bang, $cot]) {
            $daDoi += $this->doiTrongCot($bang, $cot);
        }

        $this->command?->info("Đã đổi {$daDoi} chỗ từ " . self::CU . ' sang ' . self::MOI . ' tỉnh thành.');
    }

    private function doiTrongCot(string $bang, string $cot): int
    {
        // Bo qua bang/cot khong co that: website dung chung ma nguon voi vai
        // du an khac nhau, khong phai noi nao cung du cac bang nay.
        if (!Schema::hasTable($bang) || !Schema::hasColumn($bang, $cot)) {
            return 0;
        }

        $tim = self::CU . ' tỉnh thành';
        $thay = self::MOI . ' tỉnh thành';

        // DB::raw khong nhan tham so buoc, nhung hai chuoi nay la hang so cua
        // chinh lop nay chu khong phai du lieu nguoi dung nhap vao.
        return DB::table($bang)
            ->where($cot, 'LIKE', '%' . $tim . '%')
            ->update([$cot => DB::raw("REPLACE(`{$cot}`, '{$tim}', '{$thay}')")]);
    }
}
