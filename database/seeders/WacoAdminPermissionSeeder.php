<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Nap quyen cho ba module moi: Noi dung khoi trang chu, Diem ban, Don dang ky
 * dai ly - roi gan het cho nhom "Quan tri vien".
 *
 * Gate 'modules' chan theo cot canonical cua bang permissions. Thieu dong o day
 * la mo menu ra bi bao 403 du code da co day du.
 *
 * Chay lai nhieu lan khong sinh ban ghi trung.
 */
class WacoAdminPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $quyen = [
            'home.feature.index'   => 'Xem danh sách nội dung khối trang chủ',
            'home.feature.create'  => 'Thêm mục vào khối trang chủ',
            'home.feature.update'  => 'Cập nhật mục trong khối trang chủ',
            'home.feature.destroy' => 'Xóa mục khỏi khối trang chủ',

            'dealer.index'   => 'Xem danh sách điểm bán',
            'dealer.create'  => 'Thêm mới điểm bán',
            'dealer.update'  => 'Cập nhật điểm bán',
            'dealer.destroy' => 'Xóa điểm bán',

            'dealer.registration.index'   => 'Xem danh sách đơn đăng ký đại lý',
            'dealer.registration.update'  => 'Xử lý đơn đăng ký đại lý',
            'dealer.registration.destroy' => 'Xóa đơn đăng ký đại lý',
        ];

        $now = now();
        $ids = [];

        foreach ($quyen as $canonical => $ten) {
            $dong = DB::table('permissions')->where('canonical', $canonical)->first();

            if ($dong) {
                $ids[] = $dong->id;
                continue;
            }

            $ids[] = DB::table('permissions')->insertGetId([
                'name' => $ten,
                'canonical' => $canonical,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Gan cho nhom quan tri. Lay theo id = 1 neu co, khong thi lay nhom dau
        // tien - de seeder van chay duoc tren ban CSDL da bi doi ten nhom.
        $nhomQuanTri = DB::table('user_catalogues')->orderBy('id')->value('id');

        if (!$nhomQuanTri) {
            $this->command->warn('Chua co nhom nguoi dung nao - bo qua buoc gan quyen.');
            return;
        }

        $daCo = DB::table('user_catalogue_permission')
            ->where('user_catalogue_id', $nhomQuanTri)
            ->pluck('permission_id')
            ->toArray();

        $canThem = array_diff($ids, $daCo);

        if (count($canThem)) {
            DB::table('user_catalogue_permission')->insert(
                array_map(fn($id) => [
                    'user_catalogue_id' => $nhomQuanTri,
                    'permission_id' => $id,
                ], $canThem)
            );
        }

        $this->command->info(sprintf(
            'Quyen: %d dong, gan them %d quyen cho nhom nguoi dung #%d.',
            count($ids),
            count($canThem),
            $nhomQuanTri
        ));
    }
}
