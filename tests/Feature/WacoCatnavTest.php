<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Cot danh muc ben trai trang san pham.
 *
 * Truoc day view in mot danh sach PHANG gom moi cap roi chi sap theo cot
 * `order`. Cot do danh so rieng trong tung nhanh nen danh muc con cua nhanh
 * nay chen vao giua cac danh muc cha cua nhanh khac - cot ben trai nhin nhu
 * bi xao tung len.
 */
class WacoCatnavTest extends TestCase
{
    private array $daTao = [];

    protected function tearDown(): void
    {
        if ($this->daTao) {
            DB::table('product_catalogue_language')->whereIn('product_catalogue_id', $this->daTao)->delete();
            DB::table('product_catalogues')->whereIn('id', $this->daTao)->delete();
        }

        parent::tearDown();
    }

    private function taoDanhMuc(int $cha, string $ten, int $thuTu, int $hienThi = 2): int
    {
        $id = DB::table('product_catalogues')->insertGetId([
            'parent_id' => $cha,
            'lft' => 0,
            'rgt' => 0,
            'level' => 1,
            'publish' => $hienThi,
            'order' => $thuTu,
            'user_id' => DB::table('users')->min('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_catalogue_language')->insert([
            'product_catalogue_id' => $id,
            'language_id' => config('app.language_id') ?: 1,
            'name' => $ten,
            'canonical' => 'kiem-thu-' . $id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->daTao[] = $id;

        return $id;
    }

    private function viTri(string $html, string $ten): int
    {
        $viTri = strpos($html, '<span>' . $ten . '</span>');
        $this->assertNotFalse($viTri, "Không thấy danh mục \"{$ten}\" ngoài trang");

        return $viTri;
    }

    public function test_danh_muc_con_nam_duoi_dung_danh_muc_cha(): void
    {
        // Dat `order` trung nhau giua cac nhanh - dung kieu danh so that cua
        // quan tri, va cung la truong hop lam danh sach phang bi xao tung.
        $chaMot = $this->taoDanhMuc(0, 'Kiểm thử nhánh A', 90);
        $conA1 = $this->taoDanhMuc($chaMot, 'Kiểm thử A con 1', 0);
        $this->taoDanhMuc($chaMot, 'Kiểm thử A con 2', 1);
        $this->taoDanhMuc($conA1, 'Kiểm thử A cháu', 0);

        $chaHai = $this->taoDanhMuc(0, 'Kiểm thử nhánh B', 91);
        $this->taoDanhMuc($chaHai, 'Kiểm thử B con 1', 0);

        $html = $this->get('/san-pham.html')->assertOk()->getContent();

        // Ca nhanh A phai nam gon truoc nhanh B, khong duoc xen ke.
        $this->assertLessThan($this->viTri($html, 'Kiểm thử A con 1'), $this->viTri($html, 'Kiểm thử nhánh A'));
        $this->assertLessThan($this->viTri($html, 'Kiểm thử A cháu'), $this->viTri($html, 'Kiểm thử A con 1'));
        $this->assertLessThan($this->viTri($html, 'Kiểm thử A con 2'), $this->viTri($html, 'Kiểm thử A cháu'));
        $this->assertLessThan($this->viTri($html, 'Kiểm thử nhánh B'), $this->viTri($html, 'Kiểm thử A con 2'));
        $this->assertLessThan($this->viTri($html, 'Kiểm thử B con 1'), $this->viTri($html, 'Kiểm thử nhánh B'));

        // Va phai co du ba muc do sau.
        $this->assertStringContainsString('waco-catnav__muc--cap2', $html);
        $this->assertStringContainsString('waco-catnav__muc--cap3', $html);
    }

    public function test_danh_muc_cha_bi_an_thi_con_van_hien_ra(): void
    {
        // Bo di la danh muc bien mat khoi website ma khong ai hieu vi sao.
        $chaAn = $this->taoDanhMuc(0, 'Kiểm thử cha đã ẩn', 92, 1);
        $this->taoDanhMuc($chaAn, 'Kiểm thử con mồ côi', 0);

        $html = $this->get('/san-pham.html')->assertOk()->getContent();

        $this->assertStringNotContainsString('Kiểm thử cha đã ẩn', $html);
        $this->assertStringContainsString('Kiểm thử con mồ côi', $html);
    }

    public function test_cha_cua_danh_muc_dang_xem_duoc_danh_dau(): void
    {
        $cha = $this->taoDanhMuc(0, 'Kiểm thử nhánh C', 93);
        $con = $this->taoDanhMuc($cha, 'Kiểm thử C con', 0);

        $duongDan = 'kiem-thu-' . $con;

        DB::table('routers')->insert([
            'canonical' => $duongDan,
            'module_id' => $con,
            'controllers' => 'App\Http\Controllers\Frontend\ProductCatalogueController',
            'language_id' => config('app.language_id') ?: 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // RouterController dang `echo` ket qua thay vi `return`, nen than phan
        // hoi rong - phai tu hung lay bo dem xuat cua PHP.
        ob_start();
        $phanHoi = $this->get('/' . $duongDan . '.html');
        $echo = ob_get_clean();
        $phanHoi->assertOk();
        $html = $phanHoi->getContent() ?: $echo;

        // Xoa theo `canonical` chu KHONG theo `module_id`: cot do dem rieng
        // cho tung module, nen id 118 vua la mot danh muc vua la mot bai viet.
        // Xoa theo module_id la ban nham duong dan cua bai viet trung so.
        DB::table('routers')->where('canonical', $duongDan)->delete();

        // Muc dang xem sang len, ca nhanh cha to dam - khong thi nguoi dung
        // khong biet minh dang o dau trong cay danh muc.
        $this->assertMatchesRegularExpression(
            '/is-active[^>]*>\s*<span>Kiểm thử C con<\/span>/u',
            $html,
            'Danh mục đang xem không được đánh dấu'
        );
        $this->assertStringContainsString('is-nhanh', $html, 'Danh mục cha không được đánh dấu');
    }
}
