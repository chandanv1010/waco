<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Nap noi dung WACO theo dung ban thiet ke Figma (frame TRANG CHU).
 *
 * Moi khoi trong thiet ke deu co nguon du lieu rieng de sau nay sua duoc trong
 * admin, khong ghi cung vao giao dien:
 *
 *   Menu dau trang / chan trang  -> menus + menu_language
 *   Hero - anh banner            -> slides (tu khoa main-slide)
 *   Hero (tieu de, mo ta)        -> introduces (tu khoa hero_*)
 *   Hero - 4 diem manh           -> home_features (group = hero_usp)
 *   Gioi thieu (2 doan van)      -> introduces (tu khoa about_*)
 *   Gioi thieu - 3 huy hieu      -> home_features (group = about_badge)
 *   He sinh thai san pham        -> product_catalogues, chon qua widget
 *                                   homepage-categories
 *   Vi sao WACO - 6 ly do        -> home_features (group = why_waco)
 *   Mang luoi - 4 con so         -> home_features (group = stat)
 *   Tin tuc                      -> posts + post_catalogues, chon qua
 *                                   widget homepage-news
 *   Thong tin lien he chan trang -> systems
 *
 * Chay: php artisan db:seed --class=WacoContentSeeder --force
 * Chay lai duoc nhieu lan: moi lan deu xoa va nap lai dung cac phan nay.
 */
class WacoContentSeeder extends Seeder
{
    private const LANG = 1;

    private int $userId = 1;

    /** Bo dem lft: Nestedsetbie sap xep theo lft nen phai chen san tang dan. */
    private int $lft = 1;

    public function run(): void
    {
        $this->userId = (int) (DB::table('users')->min('id') ?? 1);

        $this->command->newLine();
        $this->command->info('=== Nap noi dung WACO ===');

        DB::transaction(function () {
            $this->napHeThong();
            $this->napSlide();
            $this->napDanhMucSanPham();
            $this->napSanPham();
            $this->napTinTuc();
            $this->napTrangNoiDung();
            $this->napDoanVanBan();
            $this->napKhoiLapLai();
            $this->napMenu();
            $this->napWidget();
        });

        $this->command->newLine();
    }

    /** Thong tin cong ty - dung o chan trang va phan lien he. */
    private function napHeThong(): void
    {
        $config = [
            'homepage_company' => 'Công ty TNHH SXTM XNK Việt Anh',
            'homepage_brand' => 'WACO Việt Nam',
            'homepage_slogan' => 'Mang chuẩn sống Hàn Quốc đến gia đình Việt',
            'contact_hotline' => '1900 636 905',
            'contact_email' => 'info@wacotec.vn',
            'contact_address' => 'Tầng 5, Tòa nhà Việt Anh, Số 9, Ngõ 5 Láng Hạ, Đống Đa, Hà Nội, Việt Nam',
            'contact_website' => 'www.wacotec.vn',
            'seo_meta_title' => 'WACO Việt Nam - Nhà phân phối độc quyền WACO Korea',
            'seo_meta_description' => 'Công ty TNHH SXTM XNK Việt Anh là nhà phân phối độc quyền các dòng máy lọc nước và thiết bị chăm sóc sức khỏe thương hiệu WACO Korea tại Việt Nam.',
            'seo_meta_keyword' => 'máy lọc nước WACO, máy lọc nước ion kiềm, đại lý WACO',
            'homepage_copyright' => 'Bản quyền thuộc về XNK VIETANH - Website thương mại điện tử đã được Bộ Công Thương cấp phép',
        ];

        foreach ($config as $keyword => $content) {
            DB::table('systems')->updateOrInsert(
                ['keyword' => $keyword, 'language_id' => self::LANG],
                ['content' => $content, 'user_id' => $this->userId, 'updated_at' => now()]
            );
        }

        // Xoa duong dan anh thuong hieu cua website truoc. Neu de nguyen thi
        // trang WACO van hien logo, favicon va anh chia se cua truc - giao dien
        // co san fallback nen de trong an toan hon la de sai nhan hieu.
        $anhCu = [
            'homepage_logo', 'homepage_logo_mobile', 'homepage_favicon',
            'homepage_cover', 'seo_meta_images', 'seo_meta_images',
            'banner_home', 'homepage_about_image', 'homepage_map_image',
        ];
        DB::table('systems')
            ->whereIn('keyword', $anhCu)
            ->update(['content' => '', 'updated_at' => now()]);

        // Anh trich tu file thiet ke Figma, da luu vao public/uploads/waco.
        $anhWaco = [
            'homepage_logo' => '/uploads/waco/logo.png',
            'homepage_logo_mobile' => '/uploads/waco/logo.png',
            'homepage_favicon' => '/uploads/waco/logo.png',
            'seo_meta_images' => '/uploads/waco/hero.png',
            'seo_meta_images' => '/uploads/waco/hero.png',
            'homepage_about_image' => '/uploads/waco/gioi-thieu.png',
            'homepage_map_image' => '/uploads/waco/ban-do-dai-ly.png',
        ];
        foreach ($anhWaco as $keyword => $duongDan) {
            DB::table('systems')->updateOrInsert(
                ['keyword' => $keyword, 'language_id' => self::LANG],
                ['content' => $duongDan, 'user_id' => $this->userId, 'updated_at' => now()]
            );
        }

        $this->command->line(sprintf('  cau hinh he thong : %d muc, da xoa %d anh cu', count($config), count($anhCu)));
    }

    /**
     * Banner chinh cua trang chu.
     *
     * Dat trong module Slide chu khong phai mot o anh trong phan cau hinh he
     * thong: quan tri vao Slide > main-slide la them/bot/doi thu tu anh duoc,
     * tu anh thu hai tro len trang chu tu chuyen thanh slider.
     */
    private function napSlide(): void
    {
        $anh = [
            [
                'image' => '/uploads/waco/hero.png',
                'name' => 'WACO Korea - Mang chuan song Han Quoc den gia dinh Viet',
                'description' => '',
                'canonical' => '',
                'alt' => 'May loc nuoc va thiet bi cham soc suc khoe WACO Korea',
                'window' => '',
            ],
        ];

        DB::table('slides')->updateOrInsert(
            ['keyword' => 'main-slide'],
            [
                'name' => 'Banner trang chu',
                'item' => json_encode([self::LANG => $anh], JSON_UNESCAPED_UNICODE),
                'setting' => json_encode([]),
                'short_code' => '[slide keyword="main-slide"]',
                'publish' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->line(sprintf('  banner trang chu  : %d anh (module Slide)', count($anh)));
    }

    /** 6 danh muc trong khoi "He sinh thai san pham WACO". */
    private function napDanhMucSanPham(): void
    {
        // Cot thu 4 la icon, cot thu 5 la anh san pham - ca hai deu la file
        // trich tu ban thiet ke Figma, quan tri thay duoc trong admin.
        $danhMuc = [
            ['Máy lọc nước', 'may-loc-nuoc', 'Dòng máy lọc nước RO loại bỏ tạp chất, vi khuẩn và kim loại nặng trong nguồn nước sinh hoạt, cho nước tinh khiết dùng trực tiếp mà không cần đun sôi.', '/uploads/waco/icons/cat-may-loc-nuoc.png', '/uploads/waco/dm-may-loc-nuoc.png'],
            ['Máy lọc nước ion kiềm', 'may-loc-nuoc-ion-kiem', 'Dòng máy lọc nước ion kiềm tạo ra nguồn nước giàu Hydrogen, nhiều khoáng chất có lợi cho sức khỏe, giúp cân bằng pH và hỗ trợ phòng ngừa các bệnh mãn tính.', '/uploads/waco/icons/cat-ion-kiem.png', '/uploads/waco/dm-ion-kiem.png'],
            ['Hệ thống xử lý nước', 'he-thong-xu-ly-nuoc', 'Giải pháp xử lý nước tổng thể cho gia đình, văn phòng và công trình công nghiệp: lọc thô đầu nguồn, làm mềm nước và khử mùi, bảo vệ toàn bộ thiết bị trong nhà.', '/uploads/waco/icons/cat-he-thong-xu-ly.png', '/uploads/waco/dm-he-thong-xu-ly.png'],
            ['Lọc nước nóng lạnh', 'loc-nuoc-nong-lanh', 'Máy lọc nước tích hợp nóng lạnh cho nước ở nhiệt độ mong muốn ngay lập tức, tiện cho pha sữa, pha trà và uống thuốc, kèm khóa an toàn cho trẻ nhỏ.', '/uploads/waco/icons/cat-nong-lanh.png', '/uploads/waco/dm-nong-lanh.png'],
            ['Nắp bồn cầu thông minh', 'nap-bon-cau-thong-minh', 'Thiết bị vệ sinh thông minh với chức năng rửa tự động, sấy khô và sưởi ấm bệ ngồi, nâng chuẩn sống cho phòng tắm của gia đình Việt.', '/uploads/waco/icons/cat-bon-cau.png', '/uploads/waco/dm-bon-cau.png'],
            ['Máy lọc không khí', 'may-loc-khong-khi', 'Máy lọc không khí với màng lọc HEPA loại bỏ bụi mịn PM2.5, phấn hoa và các tác nhân gây dị ứng, trả lại bầu không khí trong lành cho không gian sống.', '/uploads/waco/icons/cat-khong-khi.png', '/uploads/waco/dm-khong-khi.png'],
        ];

        DB::table('product_catalogue_language')->delete();
        DB::table('product_catalogues')->delete();
        // Xoa router cu cua module nay, neu khong chay seeder lan hai se trung
        // canonical va bao loi khoa duy nhat.
        $this->xoaRouter('ProductCatalogueController');

        foreach ($danhMuc as $i => [$ten, $slug, $moTa, $icon, $anh]) {
            $id = DB::table('product_catalogues')->insertGetId([
                'parent_id' => 0,
                'lft' => $i * 2 + 1,
                'rgt' => $i * 2 + 2,
                'level' => 1,
                'icon' => $icon,
                'image' => $anh,
                'publish' => 2,
                'follow' => 2,
                'order' => $i,
                'user_id' => $this->userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('product_catalogue_language')->insert([
                'product_catalogue_id' => $id,
                'language_id' => self::LANG,
                'name' => $ten,
                'description' => $moTa,
                'canonical' => $slug,
                'meta_title' => $ten . ' - WACO Việt Nam',
                'meta_description' => $moTa,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('routers')->insert([
                'canonical' => $slug,
                'module_id' => $id,
                'controllers' => 'App\\Http\\Controllers\\Frontend\\ProductCatalogueController',
                'language_id' => self::LANG,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->line(sprintf('  danh muc san pham : %d', count($danhMuc)));
    }

    /**
     * San pham mau cho tung danh muc.
     *
     * Anh san pham tam dung chinh anh cua danh muc - bo anh khach gui sang chua
     * co anh rieng cho tung ma may. Quan tri thay anh trong module San pham.
     */
    private function napSanPham(): void
    {
        DB::table('product_catalogue_product')->delete();
        DB::table('product_language')->delete();
        DB::table('products')->delete();
        $this->xoaRouter('ProductController');

        // [slug danh muc => [[ma may, ten, mo ta ngan], ...]]
        $theoDanhMuc = [
            'may-loc-nuoc' => [
                ['HW-RO900', 'Máy lọc nước RO HW-RO900', "9 cấp lọc RO cao cấp
Công suất 15 lít/giờ
Phù hợp gia đình 4-6 người"],
                ['HW-RO700', 'Máy lọc nước RO HW-RO700', "Thiết kế tủ đứng gọn gàng
Lõi lọc nhập khẩu Hàn Quốc
Thay lõi nhanh, không cần thợ"],
                ['HW-RO500', 'Máy lọc nước RO HW-RO500', "Lắp gầm tủ bếp, tiết kiệm chỗ
7 cấp lọc tiêu chuẩn
Vòi rút gọn tiện dùng"],
            ],
            'may-loc-nuoc-ion-kiem' => [
                ['HW-LP500', 'Máy lọc nước ion kiềm HW-LP500', "7 tấm điện cực Titanium
9 loại nước pH 2.5 – 10.5
Màn hình cảm ứng thông minh"],
                ['HW-LP700', 'Máy lọc nước ion kiềm HW-LP700', "9 tấm điện cực Titanium
Tự động vệ sinh điện cực
Màn hình cảm ứng thông minh"],
                ['HW-LP300', 'Máy lọc nước ion kiềm HW-LP300', "5 tấm điện cực Titanium
Nhỏ gọn cho gia đình 2-4 người
7 loại nước pH 3.5 – 10.0"],
            ],
            'he-thong-xu-ly-nuoc' => [
                ['HW-SYS10', 'Hệ thống lọc tổng HW-SYS10', "Xử lý nước đầu nguồn
Công suất 1.000 lít/giờ
Cho biệt thự, khách sạn"],
                ['HW-SYS30', 'Hệ thống lọc công nghiệp HW-SYS30', "Giải pháp cho nhà máy
Vật liệu lọc nhập khẩu
Bảo trì định kỳ tận nơi"],
            ],
            'loc-nuoc-nong-lanh' => [
                ['HW-HC200', 'Máy lọc nước nóng lạnh HW-HC200', "Nóng 90°C, lạnh 5°C tức thì
Khóa an toàn trẻ em
Tiết kiệm điện ban đêm"],
                ['HW-HC400', 'Máy lọc nước nóng lạnh HW-HC400', "Ba chế độ nhiệt tùy chọn
Vòi rút gọn tiện dùng
Phù hợp văn phòng"],
            ],
            'nap-bon-cau-thong-minh' => [
                ['HW-BD100', 'Nắp bồn cầu thông minh HW-BD100', "Rửa tự động, sấy khô
Sưởi ấm bệ ngồi
Điều khiển cảm ứng"],
                ['HW-BD300', 'Nắp bồn cầu thông minh HW-BD300', "Khử mùi tự động
Đèn LED ban đêm
Remote không dây"],
            ],
            'may-loc-khong-khi' => [
                ['HW-AP50', 'Máy lọc không khí HW-AP50', "Màng lọc HEPA H13
Khử bụi mịn PM2.5
Phù hợp phòng 50m²"],
                ['HW-AP30', 'Máy lọc không khí HW-AP30', "Cảm biến chất lượng không khí
Chế độ ngủ đêm yên tĩnh
Phù hợp phòng 30m²"],
            ],
        ];

        $danhMuc = DB::table('product_catalogues as pc')
            ->join('product_catalogue_language as pcl', function ($join) {
                $join->on('pcl.product_catalogue_id', '=', 'pc.id')
                     ->where('pcl.language_id', '=', self::LANG);
            })
            ->get(['pc.id', 'pc.image', 'pcl.canonical'])
            ->keyBy('canonical');

        $tong = 0;

        foreach ($theoDanhMuc as $slugDanhMuc => $danhSach) {
            $cat = $danhMuc[$slugDanhMuc] ?? null;
            if (!$cat) {
                continue;
            }

            foreach ($danhSach as $i => [$ma, $ten, $moTa]) {
                $slug = \Illuminate\Support\Str::slug($ma);

                $id = DB::table('products')->insertGetId([
                    'product_catalogue_id' => $cat->id,
                    'image' => $cat->image,
                    // Anh phu cho dai anh doc o trang chi tiet. Day la anh tam
                    // (chua co anh chup rieng tung may), quan tri thay trong
                    // module San pham.
                    'album' => json_encode([
                        '/uploads/waco/tin-2.png',
                        '/uploads/waco/tin-1.png',
                        '/uploads/waco/gioi-thieu.png',
                    ]),
                    // Quan tri dan link YouTube o day; giao dien tu tach ma video
                    // ra de nhung. De trong thi dai anh khong co muc video.
                    'iframe' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
                    'specification' => $this->thongSoKyThuat($ma, $ten),
                    'code' => $ma,
                    'made_in' => 'Hàn Quốc',
                    'price' => 0,
                    'stock' => 0,
                    'warranty' => 24,
                    'check' => 0,
                    'total_lesson' => 0,
                    'duration' => '',
                    'publish' => 2,
                    'follow' => 2,
                    // Cot `order` cua module san pham sap giam dan, so lon hien truoc.
                    'order' => count($danhSach) - $i,
                    'user_id' => $this->userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('product_language')->insert([
                    'product_id' => $id,
                    'language_id' => self::LANG,
                    'url' => $slug,
                    'name' => $ten,
                    // Mo ta ngan luu moi thong so mot dong (ky tu xuong dong that):
                    // the san pham tach ra thanh danh sach gach dau dong dung nhu
                    // thiet ke, con admin chi can go moi dong mot y.
                    'description' => $moTa,
                    'content' => '<ul><li>' . implode('</li><li>', array_map('e', explode("
", $moTa))) . '</li></ul>'
                        . '<p>Sản phẩm nhập khẩu nguyên chiếc từ Hàn Quốc, đầy đủ CO, CQ và được bảo hành chính hãng trên toàn quốc.</p>',
                    'canonical' => $slug,
                    'meta_title' => $ten . ' - WACO Việt Nam',
                    'meta_description' => str_replace("
", ', ', $moTa),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('product_catalogue_product')->insert([
                    'product_id' => $id,
                    'product_catalogue_id' => $cat->id,
                ]);

                DB::table('routers')->insert([
                    'canonical' => $slug,
                    'module_id' => $id,
                    'controllers' => 'App\Http\Controllers\Frontend\ProductController',
                    'language_id' => self::LANG,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $tong++;
            }
        }

        $this->command->line(sprintf('  san pham          : %d', $tong));
    }

    /**
     * Thong so ky thuat mau cho mot san pham.
     *
     * Moi dong mot thong so dang "Ten: Gia tri" - dung dinh dang ma o nhap trong
     * admin huong dan, giao dien tu dung thanh bang hai cot.
     */
    private function thongSoKyThuat(string $ma, string $ten): string
    {
        return implode("
", [
            'Mã sản phẩm: ' . $ma,
            'Tên đầy đủ: ' . $ten,
            'Thương hiệu: WACO Korea',
            'Xuất xứ: Hàn Quốc',
            'Bảo hành: 24 tháng',
            'Điện áp: 220V - 50Hz',
            'Tiêu chuẩn: Nhập khẩu nguyên chiếc, đầy đủ CO, CQ',
        ]);
    }

    /** Khoi "Tin tuc - Su kien" tren trang chu. */
    private function napTinTuc(): void
    {
        DB::table('post_catalogue_language')->delete();
        DB::table('post_catalogue_post')->delete();
        DB::table('post_language')->delete();
        DB::table('post_catalogues')->delete();
        DB::table('posts')->delete();
        $this->xoaRouter('PostCatalogueController');
        $this->xoaRouter('PostController');

        $catId = DB::table('post_catalogues')->insertGetId([
            'parent_id' => 0, 'lft' => 1, 'rgt' => 2, 'level' => 1,
            'publish' => 2, 'follow' => 2, 'order' => 0,
            'user_id' => $this->userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('post_catalogue_language')->insert([
            'post_catalogue_id' => $catId, 'language_id' => self::LANG,
            'name' => 'Tin tức - Sự kiện', 'canonical' => 'tin-tuc',
            'description' => 'Tin tức và sự kiện mới nhất từ WACO Việt Nam.',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('routers')->insert([
            'canonical' => 'tin-tuc', 'module_id' => $catId,
            'controllers' => 'App\\Http\\Controllers\\Frontend\\PostCatalogueController',
            'language_id' => self::LANG, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // Cot thu 4 la mo ta ngan - hien ngay duoi tieu de o the tin trang chu
        // va o trang danh sach tin, nen khong de trong.
        $baiViet = [
            [
                'Nước sạch ảnh hưởng thế nào đến sức khỏe người tiêu dùng',
                'nuoc-sach-anh-huong-the-nao-den-suc-khoe',
                '/uploads/waco/tin-1.png',
                'Nguồn nước sinh hoạt hằng ngày có thể chứa kim loại nặng, vi khuẩn và tạp chất ảnh hưởng lâu dài tới sức khỏe cả gia đình.',
            ],
            [
                'Vì sao nên chọn máy lọc nước ion kiềm cho gia đình',
                'vi-sao-nen-chon-may-loc-nuoc-ion-kiem',
                '/uploads/waco/tin-2.png',
                'Nước ion kiềm giàu Hydrogen giúp trung hòa axit dư, hỗ trợ tiêu hóa và bổ sung khoáng chất tự nhiên cho cơ thể.',
            ],
            [
                'Hướng dẫn lựa chọn máy lọc nước phù hợp với nguồn nước',
                'huong-dan-chon-may-loc-nuoc-phu-hop',
                '/uploads/waco/tin-3.png',
                'Nước máy, nước giếng khoan hay nước mưa đều cần công nghệ lọc khác nhau. Cùng WACO xác định đúng thiết bị cho gia đình bạn.',
            ],
            [
                'Công nghệ lọc nước Hàn Quốc - xu hướng chăm sóc sức khỏe hiện đại',
                'cong-nghe-loc-nuoc-han-quoc',
                '/uploads/waco/tin-4.png',
                'Thiết bị lọc nước Hàn Quốc được ưa chuộng nhờ tiêu chuẩn kiểm định nghiêm ngặt, thiết kế gọn và chi phí vận hành thấp.',
            ],
        ];

        foreach ($baiViet as $i => [$ten, $slug, $anh, $moTa]) {
            $id = DB::table('posts')->insertGetId([
                'post_catalogue_id' => $catId,
                'image' => $anh,
                'publish' => 2, 'follow' => 2, 'order' => $i,
                'user_id' => $this->userId,
                'released_at' => now()->subDays($i * 3),
                'created_at' => now()->subDays($i * 3), 'updated_at' => now(),
            ]);

            DB::table('post_language')->insert([
                'post_id' => $id, 'language_id' => self::LANG,
                'name' => $ten, 'canonical' => $slug,
                'description' => $moTa,
                'content' => '<p>' . e($moTa) . '</p>',
                'meta_title' => $ten,
                'meta_description' => $moTa,
                'created_at' => now(), 'updated_at' => now(),
            ]);

            DB::table('post_catalogue_post')->insert([
                'post_id' => $id, 'post_catalogue_id' => $catId,
            ]);

            DB::table('routers')->insert([
                'canonical' => $slug, 'module_id' => $id,
                'controllers' => 'App\\Http\\Controllers\\Frontend\\PostController',
                'language_id' => self::LANG, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $this->command->line(sprintf('  tin tuc           : 1 danh muc, %d bai', count($baiViet)));
    }

    /** Cac doan van ban dai tren trang chu. */
    private function napDoanVanBan(): void
    {
        $noiDung = [
            'hero_title_line1' => 'NHÀ PHÂN PHỐI',
            'hero_title_line2' => 'ĐỘC QUYỀN <span>WACO KOREA</span>',
            'hero_title_line3' => 'TẠI VIỆT NAM',
            'hero_description' => 'Mang chuẩn sống Hàn Quốc đến gia đình Việt',
            'hero_cta_label' => 'TRỞ THÀNH ĐẠI LÝ',
            'hero_cta2_label' => 'XEM HỒ SƠ NĂNG LỰC',
            // Nhan nut o dau trang (ca ban desktop lan menu man hinh hep).
            'header_cta_label' => 'TRỞ THÀNH ĐẠI LÝ',

            // Thiet ke: nhan nho mau xanh o tren, tieu de lon hai dong, roi moi
            // den dong in dam gioi thieu phap nhan.
            'about_label' => 'ĐỘC QUYỀN & UY TÍN',
            'about_heading' => 'CAM KẾT CHÍNH HÃNG<br>TỪ WACO KOREA',
            'about_subheading' => 'VIỆT ANH – ĐƠN VỊ PHÂN PHỐI ĐỘC QUYỀN THƯƠNG HIỆU WACO',
            'about_content' => '<p>Công ty TNHH SXTM XNK Việt Anh là đơn vị phân phối độc quyền các dòng sản phẩm máy lọc nước và thiết bị chăm sóc sức khỏe thương hiệu WACO tại thị trường Việt Nam.</p>'
                . '<p>Với gần 20 năm kinh nghiệm trong lĩnh vực nước sạch và công nghệ xử lý nước, chúng tôi mang đến cho người tiêu dùng Việt Nam những sản phẩm được sản xuất theo tiêu chuẩn Hàn Quốc, đáp ứng nhu cầu sử dụng từ hộ gia đình, văn phòng đến các công trình thương mại và công nghiệp.</p>',

            'ecosystem_heading' => 'HỆ SINH THÁI SẢN PHẨM WACO',
            'ecosystem_description' => 'Giải pháp toàn diện cho nguồn nước sạch và cuộc sống khoẻ mạnh',

            'why_heading' => 'VÌ SAO WACO ĐƯỢC TIN DÙNG TẠI HƠN 70 QUỐC GIA?',

            'network_label' => 'MẠNG LƯỚI PHÂN PHỐI',
            'network_heading' => 'WACO TOÀN QUỐC',
            'network_cta_label' => 'XEM BẢN ĐỒ ĐẠI LÝ',

            'dealer_form_heading' => 'TRỞ THÀNH<br>ĐẠI LÝ PHÂN PHỐI WACO',
            'dealer_form_description' => 'WACO Việt Nam đang mở rộng hệ thống phân phối trên toàn quốc và tìm kiếm các đối tác chiến lược cùng phát triển thị trường.',

            'news_heading' => 'TIN TỨC - SỰ KIỆN',
            'news_page_description' => 'Cập nhật tin tức, sự kiện và kiến thức về nước sạch từ WACO Việt Nam',

            // Trang Gioi thieu - khoi "Tam nhin - Su menh"
            'vision_label' => 'TẦM NHÌN - SỨ MỆNH',
            'vision_heading' => 'GIÁ TRỊ CỐT LÕI DOANH NGHIỆP',
            'vision_content' => '<p><strong>Tầm nhìn:</strong> Trở thành nhà phân phối thiết bị lọc nước và chăm sóc sức khỏe hàng đầu Việt Nam, đưa chuẩn sống Hàn Quốc đến mọi gia đình Việt.</p>'
                . '<p><strong>Sứ mệnh:</strong> Mang tới nguồn nước sạch, an toàn và giàu khoáng chất cho người tiêu dùng Việt Nam thông qua công nghệ lọc nước tiên tiến của WACO Korea.</p>'
                . '<p><strong>Giá trị cốt lõi:</strong> Chính hãng - Minh bạch - Đồng hành. Chúng tôi cam kết sản phẩm nhập khẩu nguyên chiếc, đầy đủ CO, CQ và chính sách bảo hành rõ ràng trên toàn quốc.</p>',

            // Trang He thong dai ly
            'dealer_page_heading' => '1.000 ĐIỂM BÁN HÀNG TRÊN TOÀN QUỐC',
            'dealer_page_content' => '<p>Hệ thống đại lý WACO đã phủ sóng 63 tỉnh thành với hơn 1.000 điểm bán hàng và trung tâm kỹ thuật, sẵn sàng tư vấn, lắp đặt và bảo hành cho khách hàng ở mọi khu vực.</p>'
                . '<p>Quý khách vui lòng liên hệ hotline hoặc gửi thông tin theo mẫu bên dưới để được kết nối tới đại lý gần nhất.</p>',

            // Trang Lien he
            'contact_heading' => 'THÔNG TIN LIÊN HỆ',
            'contact_description' => 'Để lại thông tin, đội ngũ WACO sẽ liên hệ lại với bạn trong thời gian sớm nhất.',

            // Trang San pham - o tu van ben cot trai
            'product_sidebar_heading' => 'Bạn cần tư vấn sản phẩm phù hợp?',
            'product_sidebar_description' => 'Đội ngũ chuyên gia của WACO sẵn sàng hỗ trợ bạn 24/7.',
            // Moi dong mot loi ich - the hien thanh danh sach co dau tich.
            'product_sidebar_benefits' => "Tư vấn giải pháp phù hợp
Nhận báo giá nhanh
Hỗ trợ kỹ thuật tận tâm",
            'product_sidebar_cta' => 'NHẬN TƯ VẤN NGAY',

            'footer_description' => 'Nhà phân phối độc quyền thương hiệu nước sạch WACO tại Việt Nam. Mang công nghệ nước Hàn Quốc đến mọi gia đình Việt',
        ];

        DB::table('introduces')->whereIn('keyword', array_keys($noiDung))->delete();

        foreach ($noiDung as $keyword => $content) {
            DB::table('introduces')->insert([
                'language_id' => self::LANG,
                'user_id' => $this->userId,
                'keyword' => $keyword,
                'content' => $content,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->line(sprintf('  doan van ban      : %d muc', count($noiDung)));
    }

    /**
     * Cac trang noi dung tinh duoc menu tro toi (chinh sach, dich vu, tuyen dung...).
     *
     * Dung module Bai viet chu khong ghi cung thanh blade: quan tri sua noi dung
     * ngay trong admin, khong phai nho toi lap trinh. Noi dung o day chi la khung
     * de link khong gay, khach hang se thay bang noi dung that.
     */
    private function napTrangNoiDung(): void
    {
        $catId = DB::table('post_catalogues')->insertGetId([
            'parent_id' => 0, 'lft' => 3, 'rgt' => 4, 'level' => 1,
            'publish' => 2, 'follow' => 2, 'order' => 1,
            'user_id' => $this->userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('post_catalogue_language')->insert([
            'post_catalogue_id' => $catId, 'language_id' => self::LANG,
            'name' => 'Dịch vụ và hỗ trợ', 'canonical' => 'dich-vu-ho-tro',
            'description' => 'Chính sách, dịch vụ và hướng dẫn dành cho khách hàng và đại lý WACO.',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('routers')->insert([
            'canonical' => 'dich-vu-ho-tro', 'module_id' => $catId,
            'controllers' => 'App\Http\Controllers\Frontend\PostCatalogueController',
            'language_id' => self::LANG, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // [canonical, tieu de, mo ta ngan]
        $trang = [
            ['dai-ly-chinh-hang', 'Đại lý chính hãng', 'Danh sách đại lý chính hãng WACO và cách nhận biết hàng chính hãng.'],
            ['ho-tro-bao-hanh', 'Hỗ trợ và bảo hành', 'Quy trình tiếp nhận bảo hành, thời gian xử lý và các mốc bảo hành theo dòng sản phẩm.'],
            ['linh-kien', 'Linh kiện thay thế', 'Lõi lọc, màng RO và các linh kiện chính hãng WACO cùng chu kỳ thay thế khuyến nghị.'],
            ['brochure-san-pham', 'Brochure sản phẩm', 'Tài liệu giới thiệu các dòng sản phẩm WACO dành cho đại lý và khách hàng dự án.'],
            ['tam-nhin-su-menh', 'Tầm nhìn - Sứ mệnh', 'Tầm nhìn, sứ mệnh và giá trị cốt lõi của WACO Việt Nam.'],
            ['tuyen-dung', 'Tuyển dụng', 'Cơ hội nghề nghiệp tại WACO Việt Nam.'],
            ['chinh-sach-dai-ly', 'Chính sách đại lý', 'Điều kiện hợp tác, mức chiết khấu và quyền lợi dành cho đại lý WACO.'],
            ['chinh-sach-bao-hanh', 'Chính sách bảo hành', 'Điều kiện bảo hành, trường hợp được và không được bảo hành.'],
            ['chinh-sach-doi-tra', 'Chính sách đổi trả', 'Thời hạn, điều kiện và thủ tục đổi trả sản phẩm.'],
            ['huong-dan-mua-hang', 'Hướng dẫn mua hàng', 'Các bước đặt hàng, thanh toán và nhận hàng.'],
        ];

        foreach ($trang as $i => [$slug, $ten, $moTa]) {
            $id = DB::table('posts')->insertGetId([
                'post_catalogue_id' => $catId,
                'image' => '',
                'publish' => 2, 'follow' => 2, 'order' => count($trang) - $i,
                'user_id' => $this->userId,
                'released_at' => now(),
                'created_at' => now(), 'updated_at' => now(),
            ]);

            DB::table('post_language')->insert([
                'post_id' => $id, 'language_id' => self::LANG,
                'name' => $ten, 'canonical' => $slug,
                'description' => $moTa,
                'content' => '<p>' . e($moTa) . '</p><p>Nội dung đang được cập nhật.</p>',
                'meta_title' => $ten . ' - WACO Việt Nam',
                'meta_description' => $moTa,
                'created_at' => now(), 'updated_at' => now(),
            ]);

            DB::table('post_catalogue_post')->insert([
                'post_id' => $id, 'post_catalogue_id' => $catId,
            ]);

            DB::table('routers')->insert([
                'canonical' => $slug, 'module_id' => $id,
                'controllers' => 'App\Http\Controllers\Frontend\PostController',
                'language_id' => self::LANG, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $this->command->line(sprintf('  trang noi dung    : 1 chuyen muc, %d trang', count($trang)));
    }

    /** Cac khoi "icon + tieu de + mo ta" lap lai tren trang chu. */
    private function napKhoiLapLai(): void
    {
        $khoi = [
            // 4 diem manh duoi hero.
            // Tieu de xuong dong theo dung thiet ke (hai tu moi dong) - luu ky
            // tu xuong dong that trong CSDL roi view dung nl2br, de admin sua
            // cho ngat dong ma khong phai dong vao ma nguon.
            ['hero_usp', "Công nghệ
Hàn Quốc", null, '/uploads/waco/icons/usp-cong-nghe.png'],
            ['hero_usp', "Nhập khẩu
nguyên chiếc", null, '/uploads/waco/icons/usp-nhap-khau.png'],
            ['hero_usp', "Dịch vụ
chuyên nghiệp", null, '/uploads/waco/icons/usp-dich-vu.png'],
            ['hero_usp', "Bảo hành
toàn quốc", null, '/uploads/waco/icons/usp-bao-hanh.png'],

            // 3 huy hieu khoi gioi thieu
            ['about_badge', "Giấy chứng nhận
độc quyền", null, '/uploads/waco/icons/badge-chung-nhan.png'],
            ['about_badge', "CO, CQ
đầy đủ", null, '/uploads/waco/icons/badge-co-cq.png'],
            ['about_badge', "Hỗ trợ trực tiếp
từ hãng", null, '/uploads/waco/icons/badge-ho-tro.png'],

            // 6 ly do "Vi sao WACO"
            ['why_waco', 'THƯƠNG HIỆU HÀN QUỐC UY TÍN', 'Công nghệ tiên tiến, chất lượng quốc tế', '/uploads/waco/icons/why-thuong-hieu.png'],
            ['why_waco', 'SẢN PHẨM CHÍNH HÃNG', 'Nhập khẩu nguyên chiếc, CO, CQ đầy đủ', '/uploads/waco/icons/why-chinh-hang.png'],
            ['why_waco', 'CHÍNH SÁCH CẠNH TRANH', 'Chiết khấu hấp dẫn, lợi nhuận tối ưu', '/uploads/waco/icons/why-chinh-sach.png'],
            ['why_waco', 'HỖ TRỢ TOÀN DIỆN', 'Marketing, đào tạo, kỹ thuật chuyên sâu', '/uploads/waco/icons/why-ho-tro.png'],
            ['why_waco', 'DỊCH VỤ CHUYÊN NGHIỆP', 'Bảo hành toàn quốc, hỗ trợ nhanh chóng', '/uploads/waco/icons/why-dich-vu.png'],
            ['why_waco', 'ĐỒNG HÀNH PHÁT TRIỂN', 'Chiến lược dài hạn, cùng đại lý phát triển', '/uploads/waco/icons/why-dong-hanh.png'],
        ];

        DB::table('home_features')->delete();

        $order = [];
        foreach ($khoi as [$group, $title, $desc, $icon]) {
            $order[$group] = ($order[$group] ?? 0) + 1;
            DB::table('home_features')->insert([
                'group' => $group, 'title' => $title, 'description' => $desc,
                'icon' => $icon, 'order' => $order[$group], 'publish' => 2,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Dai cam ket nam de len day hero: 5 chi so, icon trang tren nen navy.
        $camKet = [
            ['20+', 'NĂM KINH NGHIỆM', '/uploads/waco/icons/ck-kinh-nghiem.png'],
            ['100+', 'QUỐC GIA XUẤT KHẨU', '/uploads/waco/icons/ck-quoc-gia.png'],
            ['1000+', 'ĐẠI LÝ TOÀN QUỐC', '/uploads/waco/icons/ck-dai-ly.png'],
            ['63', 'TỈNH THÀNH PHỦ SÓNG', '/uploads/waco/icons/ck-tinh-thanh.png'],
            ['2', 'THƯƠNG HIỆU CHỦ LỰC', '/uploads/waco/icons/ck-thuong-hieu.png'],
        ];
        foreach ($camKet as $i => [$value, $title, $icon]) {
            DB::table('home_features')->insert([
                'group' => 'commit', 'value' => $value, 'title' => $title,
                'icon' => $icon, 'order' => $i + 1, 'publish' => 2,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // 4 con so mang luoi: `value` la so hien to, `title` la nhan ben duoi.
        $soLieu = [
            ['100+', 'Đại lý toàn quốc'],
            ['63', 'Tỉnh thành phủ sóng'],
            ['1000+', 'Điểm bán hàng'],
            ['20+', 'Năm kinh nghiệm'],
        ];
        foreach ($soLieu as $i => [$value, $title]) {
            DB::table('home_features')->insert([
                'group' => 'stat', 'value' => $value, 'title' => $title,
                'order' => $i + 1, 'publish' => 2,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $this->command->line(sprintf('  khoi lap lai      : %d muc', count($khoi) + count($soLieu) + count($camKet)));
    }

    /** Menu dau trang va 4 cot chan trang. */
    private function napMenu(): void
    {
        $mainId = $this->menuCatalogueId('main-menu', 'Menu chính');
        $footerId = $this->menuCatalogueId('footer-menu', 'Menu chân trang');

        $cu = DB::table('menus')->whereIn('menu_catalogue_id', [$mainId, $footerId])->pluck('id');
        DB::table('menu_language')->whereIn('menu_id', $cu)->delete();
        DB::table('menus')->whereIn('id', $cu)->delete();

        $this->lft = 1;

        // [ten, duong dan, [muc con]]
        $main = [
            ['Trang chủ', '', []],
            ['Giới thiệu', 'gioi-thieu', []],
            ['Sản phẩm', 'san-pham', []],
            ['Dịch vụ và hỗ trợ', 'dich-vu-ho-tro', [
                ['Đại lý chính hãng', 'dai-ly-chinh-hang'],
                ['Hỗ trợ và bảo hành', 'ho-tro-bao-hanh'],
                ['Linh kiện', 'linh-kien'],
                ['Brochure sản phẩm', 'brochure-san-pham'],
            ]],
            ['Tin tức', 'tin-tuc', []],
            ['Liên hệ', 'lien-he', []],
        ];

        $soCap1 = $soCap2 = 0;
        foreach ($main as $i => [$ten, $url, $con]) {
            $parentId = $this->chenMenu($mainId, 0, 1, $i, $ten, $url);
            $soCap1++;
            foreach ($con as $j => [$cTen, $cUrl]) {
                $this->chenMenu($mainId, $parentId, 2, $j, $cTen, $cUrl);
                $soCap2++;
            }
        }

        // Chan trang: 3 cot lien ket (cot lien he lay tu bang systems).
        $footer = [
            ['Về chúng tôi', '', [
                ['Giới thiệu', 'gioi-thieu'],
                ['Tầm nhìn - Sứ mệnh', 'tam-nhin-su-menh'],
                ['Hệ thống đại lý', 'he-thong-dai-ly'],
                ['Tuyển dụng', 'tuyen-dung'],
                ['Liên hệ', 'lien-he'],
            ]],
            ['Sản phẩm', '', [
                ['Máy lọc nước', 'may-loc-nuoc'],
                ['Máy lọc nước ion kiềm', 'may-loc-nuoc-ion-kiem'],
                ['Hệ thống xử lý nước', 'he-thong-xu-ly-nuoc'],
                ['Lọc nước nóng lạnh', 'loc-nuoc-nong-lanh'],
                ['Nắp bồn cầu thông minh', 'nap-bon-cau-thong-minh'],
                ['Máy lọc không khí', 'may-loc-khong-khi'],
            ]],
            ['Chính sách', '', [
                ['Chính sách đại lý', 'chinh-sach-dai-ly'],
                ['Chính sách bảo hành', 'chinh-sach-bao-hanh'],
                ['Chính sách đổi trả', 'chinh-sach-doi-tra'],
                ['Hướng dẫn mua hàng', 'huong-dan-mua-hang'],
            ]],
        ];

        $soFooter = 0;
        foreach ($footer as $i => [$ten, $url, $con]) {
            $parentId = $this->chenMenu($footerId, 0, 1, $i, $ten, $url);
            $soFooter++;
            foreach ($con as $j => [$cTen, $cUrl]) {
                $this->chenMenu($footerId, $parentId, 2, $j, $cTen, $cUrl);
                $soFooter++;
            }
        }

        $this->command->line(sprintf('  menu chinh        : %d muc cap 1, %d muc cap 2', $soCap1, $soCap2));
        $this->command->line(sprintf('  menu chan trang   : %d muc', $soFooter));
    }

    /** Xoa router tro toi mot controller frontend. */
    private function xoaRouter(string $controller): void
    {
        DB::table('routers')
            ->where('controllers', 'App\\Http\\Controllers\\Frontend\\' . $controller)
            ->delete();
    }

    private function menuCatalogueId(string $keyword, string $name): int
    {
        $id = DB::table('menu_catalogues')->where('keyword', $keyword)->value('id');
        if ($id === null) {
            $id = DB::table('menu_catalogues')->insertGetId([
                'name' => $name, 'keyword' => $keyword, 'publish' => 2,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        return (int) $id;
    }

    private function chenMenu(int $catId, int $parentId, int $level, int $order, string $ten, string $url): int
    {
        $id = DB::table('menus')->insertGetId([
            'parent_id' => $parentId,
            'menu_catalogue_id' => $catId,
            'lft' => $this->lft++,
            'rgt' => 0,
            'level' => $level,
            'publish' => 2,
            'order' => $order,
            'user_id' => $this->userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_language')->insert([
            'menu_id' => $id, 'language_id' => self::LANG,
            'name' => $ten, 'canonical' => $url,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return (int) $id;
    }

    /**
     * Widget chon ban ghi cho khoi danh muc va khoi tin tuc.
     *
     * Trang chu khong tu truy van bang product_catalogues / posts. Muon doi
     * danh muc nao len trang chu, hay bai viet nao duoc hien, quan tri sua
     * widget tuong ung - thu tu id trong widget cung la thu tu hien ra.
     */
    private function napWidget(): void
    {
        // Widget cua website cu tro toi nhung ban ghi da bi xoa, de lai chi lam
        // roi danh sach trong admin.
        $giuLai = ['homepage-categories', 'homepage-news'];
        $soCu = DB::table('widgets')->whereNotIn('keyword', $giuLai)->delete();

        $catIds = DB::table('product_catalogues')->orderBy('order')->pluck('id')->all();
        $postIds = DB::table('posts')->orderByDesc('released_at')->limit(4)->pluck('id')->all();

        foreach ([
            ['homepage-categories', 'Hệ sinh thái sản phẩm', 'ProductCatalogue', $catIds],
            ['homepage-news', 'Tin tức - Sự kiện', 'Post', $postIds],
        ] as [$keyword, $name, $model, $ids]) {
            DB::table('widgets')->updateOrInsert(
                ['keyword' => $keyword],
                [
                    'name' => $name,
                    'model' => $model,
                    'model_id' => json_encode($ids),
                    'publish' => 2,
                    'deleted_at' => null,
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->line(sprintf('  widget            : 2 khoi, da xoa %d widget cu', $soCu));
    }
}
