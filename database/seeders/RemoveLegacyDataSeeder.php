<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Xoa du lieu cua website truoc (truc), don nen cho WACO.
 *
 * WACO la web gioi thieu thuong hieu va thu don dang ky dai ly - KHONG ban hang
 * truc tuyen. Vi vay ca nhom don hang / khuyen mai / voucher / khach hang deu
 * khong dung den, xoa han chu khong giu lai cho roi.
 *
 * GIU LAI (ha tang va cau truc, se nap noi dung WACO o seeder sau):
 *   users, user_catalogues, permissions   - tai khoan quan tri
 *   languages, routers                    - da ngon ngu va dinh tuyen
 *   provinces, districts, wards           - dia gioi, dung cho khu vuc dai ly
 *   systems                               - cau hinh (se sua noi dung, khong xoa)
 *   menus, menu_catalogues                - se nap lai menu WACO
 *   post_catalogues, posts                - se nap lai tin tuc WACO
 *   product_catalogues, products          - se nap lai 6 danh muc WACO
 *
 * SAO LUU TRUOC KHI CHAY:
 *   docker compose exec db mysqldump -uroot -proot sql_waco > waco_backup.sql
 *
 * Chay: php artisan db:seed --class=RemoveLegacyDataSeeder --force
 * Chay lai nhieu lan khong sao.
 */
class RemoveLegacyDataSeeder extends Seeder
{
    /** Bang noi phai xoa TRUOC bang chinh. */
    private const PIVOT_TABLES = [
        'product_catalogue_product',
        'product_variant_attribute',
        'product_variant_language',
        'product_variants',
        'product_language',
        'attribute_catalogue_attribute',
        'attribute_language',
        'attribute_catalogue_language',
        'post_catalogue_post',
        'post_language',
        'post_catalogue_language',
        'product_catalogue_language',
        'order_product',
        'order_paymentable',
        'promotion_product_variant',
        'promotion_combos',
        'promotion_gifts',
        'promotion_rules',
        'voucher_order_conditions',
        'voucher_orders',
        'voucher_products',
        'voucher_shipping_conditions',
        'voucher_usages',
        'customer_point_history',
        'customer_password_resets',
    ];

    /** Bang chinh chua noi dung cua website truoc. */
    private const MAIN_TABLES = [
        'products',
        'product_catalogues',
        'attributes',
        'attribute_catalogues',
        'posts',
        'post_catalogues',
        'introduces',
        'slides',
        'reviews',
        'contacts',
        // Cac module rieng cua truc, WACO khong co
        'lecturers',
        'workshops',
        'departments',
        'distributions',
        'distribution_areas',
        'combo_products',
        'policies',
        // Nhom ban hang - WACO khong ban truc tuyen
        'orders',
        'promotions',
        'vouchers',
        'customers',
        'customer_catalogues',
    ];

    public function run(): void
    {
        $this->command->newLine();
        $this->command->info('=== Xoa du lieu cua website truoc ===');

        $truoc = $this->counts();
        if (array_sum($truoc) === 0) {
            $this->command->line('  Khong con gi de xoa.');
            $this->command->newLine();
            return;
        }

        $this->command->newLine();
        $this->command->line('  Truoc khi xoa:');
        foreach ($truoc as $bang => $n) {
            if ($n > 0) {
                $this->command->line(sprintf('    %-32s %6d', $bang, $n));
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::transaction(function () {
            foreach (array_merge(self::PIVOT_TABLES, self::MAIN_TABLES) as $bang) {
                if (DB::getSchemaBuilder()->hasTable($bang)) {
                    DB::table($bang)->delete();
                }
            }

            // Router tro toi noi dung vua xoa, de lai se thanh lien ket chet.
            DB::table('routers')->delete();
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $sau = $this->counts();
        $conLai = array_sum($sau);

        $this->command->newLine();
        if ($conLai === 0) {
            $this->command->info('  Tat ca ve 0.');
        } else {
            foreach ($sau as $bang => $n) {
                if ($n > 0) {
                    $this->command->warn(sprintf('    %-32s %6d  <- con sot', $bang, $n));
                }
            }
        }

        // Doi chieu phan GIU LAI de chac chan khong xoa nham.
        $this->command->newLine();
        $this->command->line('  Giu lai (phai khac 0):');
        foreach (['users', 'user_catalogues', 'permissions', 'languages',
                  'provinces', 'wards', 'systems', 'menus'] as $bang) {
            $n = DB::table($bang)->count();
            $this->command->line(sprintf('    %-32s %6d%s', $bang, $n, $n === 0 ? '   <- CANH BAO' : ''));
        }

        $this->command->newLine();
    }

    /** @return array<string,int> */
    private function counts(): array
    {
        $out = [];
        foreach (array_merge(self::MAIN_TABLES, self::PIVOT_TABLES) as $bang) {
            if (DB::getSchemaBuilder()->hasTable($bang)) {
                $out[$bang] = DB::table($bang)->count();
            }
        }
        $out['routers'] = DB::table('routers')->count();

        return $out;
    }
}
