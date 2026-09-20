<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Product\ProductCatalogueRepository;
use App\Repositories\Product\ProductRepository;
use App\Services\V1\Core\WidgetService;
use Illuminate\Support\Collection;

/**
 * Trang chi tiet san pham.
 *
 * Controller nay duoc goi tu RouterController theo canonical trong bang routers.
 * Trang khong ban hang truc tiep: hai nut keu goi deu mo popup de khach de lai
 * thong tin, khong dieu huong di dau.
 */
class ProductController extends FrontendController
{
    protected $productRepository;
    protected $productCatalogueRepository;
    protected $widgetService;

    public function __construct(
        ProductRepository $productRepository,
        ProductCatalogueRepository $productCatalogueRepository,
        WidgetService $widgetService
    ) {
        $this->productRepository = $productRepository;
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->widgetService = $widgetService;
        parent::__construct();
    }

    public function index($id, $request)
    {
        $product = $this->productRepository->getProductById($id, $this->language, config('apps.general.defaultPublish'));

        if (is_null($product)) {
            abort(404);
        }

        $productCatalogue = $this->productCatalogueRepository
            ->getProductCatalogueById($product->product_catalogue_id, $this->language);

        // Anh phu: cot album luu chuoi JSON. Du lieu do quan tri nhap nen phai
        // chap nhan ca truong hop JSON hong ma khong lam vo trang.
        $album = [];
        if (!empty($product->album)) {
            $decoded = json_decode($product->album, true);
            $album = is_array($decoded) ? array_filter($decoded) : [];
        }

        return view('frontend.product.product.index', [
            'config' => ['js' => [], 'css' => []],
            'system' => $this->system,
            'seo' => seo($product),
            'product' => $product,
            'productCatalogue' => $productCatalogue,
            'album' => $album,
            // Khoi "He sinh thai san pham WACO" o cuoi trang - cung widget voi
            // trang chu, khong truy van rieng.
            'categories' => $this->danhMucQuaWidget(),
        ]);
    }

    /**
     * 6 danh muc cho khoi he sinh thai, lay qua widget homepage-categories va
     * giu dung thu tu quan tri chon trong widget.
     */
    private function danhMucQuaWidget(): Collection
    {
        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'homepage-categories', 'object' => true],
        ], $this->language);

        $widget = $widgets['homepage-categories'] ?? null;

        if (!$widget || empty($widget->object)) {
            return collect();
        }

        $thuTu = array_flip(array_map('intval', (array) $widget->model_id));

        return collect($widget->object)
            ->sortBy(fn($item) => $thuTu[(int) $item->id] ?? PHP_INT_MAX)
            ->values();
    }
}
