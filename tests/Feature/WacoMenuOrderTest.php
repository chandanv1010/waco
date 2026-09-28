<?php

namespace Tests\Feature;

use App\Http\ViewComposers\MenuComposer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Keo tha doi vi tri menu trong quan tri thi menu ngoai website phai doi theo.
 *
 * Truoc day khong doi: man hinh keo tha ghi `order` GIAM dan (muc dau tien
 * duoc so lon nhat) trong khi form nhap tay va view frontend deu hieu la TANG
 * dan, nen keo xong menu ngoai trang hien nguoc lai.
 */
class WacoMenuOrderTest extends TestCase
{
    private int $maDanhMuc;
    private array $thuTuGoc = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->maDanhMuc = (int) DB::table('menu_catalogues')->where('keyword', 'main-menu')->value('id');

        if (!$this->maDanhMuc) {
            $this->markTestSkipped('Website chưa có menu chính.');
        }

        $this->thuTuGoc = DB::table('menus')
            ->where('menu_catalogue_id', $this->maDanhMuc)
            ->pluck('order', 'id')
            ->toArray();
    }

    /**
     * MenuComposer dung mot bien static de mot request khong dung lai truy van
     * menu hang chuc lan. Ca bo kiem tra chay chung MOT tien trinh PHP nen bien
     * do song qua nhieu request: bai kiem tra nao goi trang chu truoc se giu
     * lai thu tu cu, keo tha xong goi lai van thay ban cu. Chay that thi moi
     * request la mot tien trinh rieng nen khong gap.
     */
    private function xoaBoNhoTamMenu(): void
    {
        $thuocTinh = new \ReflectionProperty(MenuComposer::class, 'menuData');
        $thuocTinh->setAccessible(true);
        $thuocTinh->setValue([]);
    }

    protected function tearDown(): void
    {
        // Tra lai thu tu cu: day la CSDL that cua may dev, khong phai co so du
        // lieu dung mot lan.
        foreach ($this->thuTuGoc as $id => $thuTu) {
            DB::table('menus')->where('id', $id)->update(['order' => $thuTu]);
        }

        parent::tearDown();
    }

    /** Cac muc menu cap 1, xep theo thu tu dang luu. */
    private function menuCap1(): array
    {
        return DB::table('menus as m')
            ->join('menu_language as ml', 'ml.menu_id', '=', 'm.id')
            ->where('m.menu_catalogue_id', $this->maDanhMuc)
            ->where('m.parent_id', 0)
            ->orderBy('m.order')
            ->pluck('ml.name', 'm.id')
            ->toArray();
    }

    private function quanTri(): User
    {
        $user = User::whereHas('user_catalogues', fn ($q) => $q->where('name', 'LIKE', '%uản trị%'))->first()
            ?? User::query()->orderBy('id')->first();

        if (!$user) {
            $this->markTestSkipped('Không có tài khoản quản trị nào.');
        }

        return $user;
    }

    public function test_keo_tha_xong_thi_menu_ngoai_trang_doi_theo(): void
    {
        $banDau = $this->menuCap1();
        $this->assertGreaterThanOrEqual(3, count($banDau), 'Menu chính cần ít nhất 3 mục để kiểm tra');

        // Dao nguoc thu tu, giong nhu nguoi dung keo muc cuoi len dau.
        $daoNguoc = array_reverse(array_keys($banDau), true);
        $json = array_map(fn ($id) => ['id' => (string) $id], $daoNguoc);

        $this->actingAs($this->quanTri())
            ->post(route('ajax.menu.drag'), [
                'json' => json_encode(array_values($json)),
                'menu_catalogue_id' => $this->maDanhMuc,
            ])
            ->assertSuccessful();

        $sauKhiKeo = $this->menuCap1();

        $this->assertSame(
            array_reverse(array_values($banDau)),
            array_values($sauKhiKeo),
            'Thứ tự lưu trong CSDL không khớp với thứ tự vừa kéo'
        );

        $this->xoaBoNhoTamMenu();

        // Diem mau chot: doc HTML that cua trang chu, khong chi tin vao cot
        // `order`. Frontend co the sap xep kieu khac ma van "dung" trong CSDL.
        $html = $this->get('/')->assertOk()->getContent();

        $viTri = [];
        foreach ($sauKhiKeo as $ten) {
            $viTri[$ten] = strpos($html, '>' . $ten . '<');
        }

        $this->assertNotContains(false, $viTri, 'Có mục menu không xuất hiện ngoài trang chủ');

        $mongDoi = array_values($viTri);
        $daSap = $mongDoi;
        sort($daSap);

        $this->assertSame($daSap, $mongDoi, 'Menu ngoài trang chủ không theo đúng thứ tự vừa kéo');
    }

    public function test_danh_sach_trong_quan_tri_xep_cung_chieu_voi_website(): void
    {
        $html = $this->actingAs($this->quanTri())
            ->get(route('menu.edit', ['id' => $this->maDanhMuc]))
            ->assertOk()
            ->getContent();

        // Bam theo data-id cua tung the <li> trong khung keo tha. Tim theo ten
        // menu se dinh ca thanh ben trai va duong dan dieu huong - ten menu
        // xuat hien nhieu cho tren cung mot trang.
        preg_match_all("/<li class='dd-item' data-id='(\d+)'/", $html, $khop);

        $capMot = array_keys($this->menuCap1());

        $this->assertNotEmpty($khop[1], 'Không đọc được danh sách kéo thả trong quản trị');

        $trongKhung = array_values(array_intersect(array_map('intval', $khop[1]), $capMot));

        $this->assertSame(
            $capMot,
            $trongKhung,
            'Danh sách trong quản trị hiện ngược với thứ tự ngoài website'
        );
    }
}
