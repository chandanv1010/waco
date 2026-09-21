<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Product\ProductCatalogueRepository;
use App\Repositories\Product\ProductRepository;
use App\Services\V1\Core\WidgetService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
            'lienQuan' => $this->sanPhamLienQuan($product),
            // Khoi "He sinh thai san pham WACO" o cuoi trang - cung widget voi
            // trang chu, khong truy van rieng.
            'categories' => $this->danhMucQuaWidget(),
        ]);
    }

    /**
     * 4 san pham khac cung danh muc, bo chinh san pham dang xem.
     *
     * Tra ve dung hinh dang ma the san pham can: ten, canonical va mo ta nam
     * thang tren dong (join bang ngon ngu) chu khong qua quan he ->languages -
     * neu khong moi the lai them mot truy van.
     */
    private function sanPhamLienQuan($product): Collection
    {
        if (empty($product->product_catalogue_id)) {
            return collect();
        }

        return DB::table('products as p')
            ->join('product_language as pl', function ($join) {
                $join->on('pl.product_id', '=', 'p.id')
                     ->where('pl.language_id', '=', $this->language);
            })
            ->where('p.product_catalogue_id', $product->product_catalogue_id)
            ->where('p.id', '!=', $product->id)
            ->where('p.publish', 2)
            ->whereNull('p.deleted_at')
            ->orderBy('p.order')
            ->orderByDesc('p.id')
            ->limit(4)
            ->get(['p.id', 'p.code', 'p.image', 'pl.name', 'pl.canonical', 'pl.description']);
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
