<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\SlideEnum;
use App\Http\Controllers\FrontendController;
use App\Services\V1\Core\SlideService;
use App\Services\V1\Core\WidgetService;
use Illuminate\Support\Collection;

/**
 * Trang chu WACO.
 *
 * Nguyen tac: khong truy van thang bang ban ghi tu day.
 *  - Banner chinh  -> module Slide  (tu khoa main-slide)
 *  - Danh muc, tin -> module Widget (tu khoa homepage-categories / homepage-news)
 *  - Van ban tinh  -> module Gioi thieu (introduces) va bang home_features,
 *                     do WacoComposer cap cho moi view frontend
 * Nho vay quan tri doi noi dung / doi thu tu duoc ngay trong admin.
 */
class HomeController extends FrontendController
{
    protected $widgetService;
    protected $slideService;

    public function __construct(
        WidgetService $widgetService,
        SlideService $slideService
    ) {
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
        parent::__construct();
    }

    public function index()
    {
        $config = $this->config();
        $system = $this->system;

        // Banner chinh lay tu module Slide - quan tri them/bot/doi anh o day,
        // nhieu anh thi tu dong chay thanh slider.
        $slides = $this->slideService->getSlide([SlideEnum::MAIN], $this->language);

        // Hai khoi co lien ket toi ban ghi that (danh muc san pham, bai viet)
        // deu di qua Widget, khong tu dung query rieng.
        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'homepage-categories', 'object' => true],
            ['keyword' => 'homepage-news', 'object' => true],
        ], $this->language);

        // WidgetService tra ve theo cot `order` cua ban ghi. Tren trang chu thu
        // tu phai dung thu tu quan tri chon trong widget, nen sap lai theo
        // model_id.
        $categories = $this->theoThuTuWidget($widgets, 'homepage-categories');
        $news = $this->theoThuTuWidget($widgets, 'homepage-news');

        $seo = [
            'meta_title' => $system['seo_meta_title'] ?? '',
            'meta_keyword' => $system['seo_meta_keyword'] ?? '',
            'meta_description' => $system['seo_meta_description'] ?? '',
            'meta_image' => $system['seo_meta_images'] ?? '',
            'canonical' => config('app.url'),
        ];

        return view('frontend.homepage.home.index', compact(
            'config',
            'system',
            'seo',
            'slides',
            'widgets',
            'categories',
            'news'
        ));
    }

    /**
     * Lay danh sach ban ghi cua mot widget, giu dung thu tu quan tri da chon.
     *
     * model_id cua widget la mang id theo thu tu chon trong admin, con cau truy
     * van trong WidgetService sap theo cot `order`. Ham nay xep lai theo
     * model_id de keo tha trong admin la doi duoc thu tu ngoai trang chu.
     */
    private function theoThuTuWidget(array $widgets, string $keyword): Collection
    {
        $widget = $widgets[$keyword] ?? null;

        if (!$widget || empty($widget->object)) {
            return collect();
        }

        $thuTu = array_flip(array_map('intval', (array) $widget->model_id));

        return collect($widget->object)
            ->sortBy(fn($item) => $thuTu[(int) $item->id] ?? PHP_INT_MAX)
            ->values();
    }

    private function config(): array
    {
        return [
            'js' => [],
            'css' => [],
        ];
    }
}
