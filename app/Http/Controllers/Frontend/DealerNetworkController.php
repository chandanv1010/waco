<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

/**
 * Trang "He thong dai ly".
 *
 * Hien gio trang chi gioi thieu mang luoi va nhan don dang ky. Khi bang dealers
 * co du lieu that thi bo sung danh sach diem ban theo tinh o day.
 */
class DealerNetworkController extends FrontendController
{
    public function index()
    {
        $system = $this->system;

        $seo = [
            'meta_title' => 'Hệ thống đại lý - ' . ($system['homepage_brand'] ?? 'WACO Việt Nam'),
            'meta_keyword' => 'đại lý WACO, hệ thống phân phối WACO',
            'meta_description' => 'Hệ thống đại lý WACO phủ sóng 63 tỉnh thành với hơn 1.000 điểm bán hàng trên toàn quốc.',
            'meta_image' => $system['homepage_map_image'] ?? '',
            'canonical' => write_url('he-thong-dai-ly'),
        ];

        return view('frontend.dealer.index', [
            'config' => ['js' => [], 'css' => []],
            'system' => $system,
            'seo' => $seo,
        ]);
    }
}
