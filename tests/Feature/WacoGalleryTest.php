<?php

namespace Tests\Feature;

use App\Http\ViewComposers\WacoComposer;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Hai viec di lien nhau:
 *   - ba huy hieu trong khoi gioi thieu co the gan mot bai viet, bam vao thi
 *     mo bai do;
 *   - trong bai viet, anh trong album hien thanh luoi 3 anh mot hang va bam
 *     vao thi phong to.
 */
class WacoGalleryTest extends TestCase
{
    private ?int $maBai = null;
    private ?int $maHuyHieu = null;
    private ?string $duongDanBai = null;
    private $albumCu = null;

    protected function setUp(): void
    {
        parent::setUp();

        $bai = DB::table('posts as p')
            ->join('post_language as pl', 'pl.post_id', '=', 'p.id')
            ->where('p.publish', 2)
            ->whereNull('p.deleted_at')
            ->select('p.id', 'p.album', 'pl.canonical')
            ->first();

        if (!$bai) {
            $this->markTestSkipped('Website chưa có bài viết nào đang hiển thị.');
        }

        $this->maBai = (int) $bai->id;
        $this->albumCu = $bai->album;
        $this->duongDanBai = '/' . $bai->canonical . '.html';

        $this->maHuyHieu = (int) DB::table('home_features')->where('group', 'about_badge')->value('id');

        $this->xoaBoNhoTamWaco();
    }

    /**
     * WacoComposer giu du lieu trong mot bien static de mot request khong truy
     * van lai hang chuc lan. Trong bo kiem tra thi ca lop chay chung MOT tien
     * trinh PHP, nen bien do song qua nhieu request - doi du lieu xong goi
     * trang van thay ban cu. Ngoai doi that moi request la mot tien trinh moi
     * nen khong gap.
     */
    private function xoaBoNhoTamWaco(): void
    {
        $thuocTinh = new \ReflectionProperty(WacoComposer::class, 'cache');
        $thuocTinh->setAccessible(true);
        $thuocTinh->setValue([]);
    }

    protected function tearDown(): void
    {
        // Tra lai nguyen trang: day la CSDL that cua may dev.
        if ($this->maBai) {
            DB::table('posts')->where('id', $this->maBai)->update(['album' => $this->albumCu]);
        }

        if ($this->maHuyHieu) {
            DB::table('home_features')->where('id', $this->maHuyHieu)->update(['post_id' => null]);
        }

        parent::tearDown();
    }

    /**
     * Doc HTML cua mot trang do RouterController dieu phoi.
     *
     * Router dang `echo` ket qua cua controller con thay vi `return`, nen than
     * phan hoi rong - noi dung di thang ra bo dem xuat cua PHP. Phai tu hung
     * lay, khong thi moi kiem tra noi dung tren cac trang nay deu thanh rong.
     */
    private function docTrang(string $duongDan): string
    {
        ob_start();
        $phanHoi = $this->get($duongDan);
        $echo = ob_get_clean();

        $phanHoi->assertOk();

        return $phanHoi->getContent() ?: $echo;
    }

    private function datAlbum(array $anh): void
    {
        DB::table('posts')->where('id', $this->maBai)->update(['album' => json_encode($anh)]);
    }

    public function test_bai_khong_co_album_thi_khong_in_luoi_anh(): void
    {
        DB::table('posts')->where('id', $this->maBai)->update(['album' => null]);

        $html = $this->docTrang($this->duongDanBai);

        $this->assertStringNotContainsString('waco-gallery', $html);
        $this->assertStringNotContainsString('data-waco-lightbox-hop', $html);
    }

    public function test_album_hien_thanh_luoi_anh_co_lightbox(): void
    {
        $this->datAlbum([
            '/uploads/waco/tin-1.png',
            '/uploads/waco/tin-2.png',
            '/uploads/waco/tin-3.png',
        ]);

        $html = $this->docTrang($this->duongDanBai);

        $this->assertSame(3, substr_count($html, 'waco-gallery__item'), 'Số ảnh trong lưới không đúng');
        $this->assertStringContainsString('/uploads/waco/tin-2.png', $html);

        // Khung phong to phai co mat, khong thi bam vao anh khong ra gi.
        $this->assertStringContainsString('data-waco-lightbox-hop', $html);
    }

    public function test_khung_phong_to_chi_in_mot_lan(): void
    {
        $this->datAlbum(['/uploads/waco/tin-1.png']);

        $html = $this->docTrang($this->duongDanBai);

        // @once lo viec nay. In hai lan thi co hai the cung data-*, JS bat
        // nham cai rong va bam vao anh khong thay gi.
        // Dem the that, khong dem ca chuoi trong doan JS ben duoi no.
        $this->assertSame(1, substr_count($html, '<div class="waco-lightbox" data-waco-lightbox-hop'));
    }

    public function test_album_rong_khong_lam_hong_trang(): void
    {
        // Quan tri xoa het anh thi cot con chuoi rong chu khong phai null.
        DB::table('posts')->where('id', $this->maBai)->update(['album' => '']);
        $this->assertStringNotContainsString('waco-gallery', $this->docTrang($this->duongDanBai));

        DB::table('posts')->where('id', $this->maBai)->update(['album' => '[]']);
        $this->assertStringNotContainsString('waco-gallery', $this->docTrang($this->duongDanBai));
    }

    public function test_huy_hieu_khong_gan_bai_thi_khong_phai_lien_ket(): void
    {
        if (!$this->maHuyHieu) {
            $this->markTestSkipped('Chưa có huy hiệu nào trong khối giới thiệu.');
        }

        DB::table('home_features')->where('id', $this->maHuyHieu)->update(['post_id' => null]);
        $this->xoaBoNhoTamWaco();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('waco-about__badges', $html);
        $this->assertStringNotContainsString('waco-badge--link', $html);
    }

    public function test_huy_hieu_gan_bai_thi_bam_duoc_sang_bai_do(): void
    {
        if (!$this->maHuyHieu) {
            $this->markTestSkipped('Chưa có huy hiệu nào trong khối giới thiệu.');
        }

        DB::table('home_features')->where('id', $this->maHuyHieu)->update(['post_id' => $this->maBai]);
        $this->xoaBoNhoTamWaco();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('waco-badge--link', $html);
        $this->assertStringContainsString(ltrim($this->duongDanBai, '/'), $html);
    }

    public function test_bai_bi_an_thi_huy_hieu_tro_lai_khong_bam_duoc(): void
    {
        if (!$this->maHuyHieu) {
            $this->markTestSkipped('Chưa có huy hiệu nào trong khối giới thiệu.');
        }

        // Gan mot bai khong ton tai: huy hieu phai tro ve the tinh chu khong
        // duoc in ra lien ket rong dan nguoi dung vao trang 404.
        DB::table('home_features')->where('id', $this->maHuyHieu)->update(['post_id' => 999999]);
        $this->xoaBoNhoTamWaco();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('waco-badge--link', $html);
    }
}
