<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Product\ProductCatalogueRepository;
use App\Services\V1\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Trang danh sach san pham.
 *
 *   - index() : mot danh muc cu the, goi tu RouterController theo canonical
 *   - all()   : trang /san-pham.html gom tat ca san pham (muc "San pham" tren menu)
 *
 * Ca hai dung chung mot giao dien, chi khac o cho co loc theo danh muc hay khong.
 */
class ProductCatalogueController extends FrontendController
{
    protected $productCatalogueRepository;
    protected $productService;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductService $productService
    ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productService = $productService;
        parent::__construct();
    }

    public function index($id, $request, $page = 1)
    {
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);

        if (is_null($productCatalogue)) {
            abort(404);
        }

        $products = $this->productService->paginate(
            $request,
            $this->language,
            $productCatalogue,
            $page,
            ['path' => $productCatalogue->canonical]
        );

        return view('frontend.product.catalogue.index', [
            'config' => ['js' => [], 'css' => []],
            'system' => $this->system,
            'seo' => seo($productCatalogue, $page),
            'tieuDe' => $productCatalogue->name,
            'moTa' => $productCatalogue->description,
            'danhMucHienTai' => (int) $productCatalogue->id,
            'danhMuc' => $this->danhSachDanhMuc(),
            'products' => $products,
        ]);
    }

    /** Trang "San pham" gom tat ca danh muc - khai bao route rieng trong web.php. */
    public function all(Request $request, $page = 1)
    {
        $products = $this->productService->paginate(
            $request,
            $this->language,
            null,
            $page,
            ['path' => 'san-pham']
        );

        $system = $this->system;

        return view('frontend.product.catalogue.index', [
            'config' => ['js' => [], 'css' => []],
            'system' => $system,
            'seo' => [
                'meta_title' => 'Sản phẩm - ' . ($system['homepage_brand'] ?? 'WACO Việt Nam'),
                'meta_keyword' => $system['seo_meta_keyword'] ?? '',
                'meta_description' => $system['seo_meta_description'] ?? '',
                'meta_image' => $system['seo_meta_images'] ?? '',
                'canonical' => write_url('san-pham'),
            ],
            'tieuDe' => 'Hệ sinh thái sản phẩm',
            // De rong: view se lui ve doan gioi thieu chung cua khoi he sinh thai
            // (introduces.ecosystem_description) do WacoComposer cap.
            'moTa' => '',
            'danhMucHienTai' => 0,
            'danhMuc' => $this->danhSachDanhMuc(),
            'products' => $products,
        ]);
    }

    /** Danh sach danh muc cho cot ben trai. */
    private function danhSachDanhMuc()
    {
        return DB::table('product_catalogues as pc')
            ->join('product_catalogue_language as pcl', function ($join) {
                $join->on('pcl.product_catalogue_id', '=', 'pc.id')
                     ->where('pcl.language_id', '=', $this->language);
            })
            ->where('pc.publish', 2)
            ->whereNull('pc.deleted_at')
            ->orderBy('pc.order')
            ->get(['pc.id', 'pc.icon', 'pcl.name', 'pcl.canonical']);
    }
}
