<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

/**
 * Trang "Gioi thieu chung".
 *
 * Toan bo noi dung nam trong bang introduces va home_features (do WacoComposer
 * cap cho view), nen trang nay khong can truy van gi them.
 */
class AboutController extends FrontendController
{
    public function index()
    {
        $system = $this->system;

        $seo = [
            'meta_title' => 'Giới thiệu - ' . ($system['homepage_brand'] ?? 'WACO Việt Nam'),
            'meta_keyword' => $system['seo_meta_keyword'] ?? '',
            'meta_description' => $system['seo_meta_description'] ?? '',
            'meta_image' => $system['seo_meta_images'] ?? '',
            'canonical' => write_url('gioi-thieu'),
        ];

        return view('frontend.about.index', [
            'config' => ['js' => [], 'css' => []],
            'system' => $system,
            'seo' => $seo,
        ]);
    }
}
