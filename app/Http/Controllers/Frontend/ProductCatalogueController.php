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
            'nhanhDangXem' => $this->nhanhDangXem((int) $productCatalogue->id),
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
            'nhanhDangXem' => [],
            'danhMuc' => $this->danhSachDanhMuc(),
            'products' => $products,
        ]);
    }

    /**
     * Danh sach danh muc cho cot ben trai, da xep thanh cay cha - con.
     *
     * Truoc day tra ve mot danh sach PHANG gom moi cap roi chi sap theo cot
     * `order`. Cot do danh so rieng trong tung nhanh, nen danh muc con cua
     * nhanh nay chen vao giua cac danh muc cha cua nhanh khac - cot ben trai
     * nhin nhu bi xao tung len.
     */
    private function danhSachDanhMuc(): array
    {
        $tatCa = DB::table('product_catalogues as pc')
            ->join('product_catalogue_language as pcl', function ($join) {
                $join->on('pcl.product_catalogue_id', '=', 'pc.id')
                     ->where('pcl.language_id', '=', $this->language);
            })
            ->where('pc.publish', 2)
            ->whereNull('pc.deleted_at')
            ->orderBy('pc.order')
            ->orderBy('pc.id')
            ->get(['pc.id', 'pc.parent_id', 'pc.icon', 'pcl.name', 'pcl.canonical']);

        return $this->dungCayDanhMuc($tatCa);
    }

    /**
     * Gom danh sach phang thanh cay theo parent_id.
     *
     * Khong dua vao lft/rgt: hai cot do chi dung sau khi cay long nhau duoc
     * dung lai, ma viec do co the tre hoac sot. parent_id thi luon dung.
     */
    private function dungCayDanhMuc($tatCa): array
    {
        $coMat = $tatCa->keyBy('id');

        $conCua = [];
        foreach ($tatCa as $muc) {
            $cha = (int) $muc->parent_id;

            // Danh muc cha bi an hoac bi xoa thi con cua no thanh mo coi. Dua
            // len lam muc goc chu khong bo di - bo di la danh muc bien mat
            // khoi website ma khong ai biet vi sao.
            if ($cha !== 0 && !$coMat->has($cha)) {
                $cha = 0;
            }

            $conCua[$cha][] = $muc;
        }

        $dung = function (int $cha) use (&$dung, $conCua): array {
            $ra = [];
            foreach ($conCua[$cha] ?? [] as $muc) {
                $muc->children = $dung((int) $muc->id);
                $ra[] = $muc;
            }
            return $ra;
        };

        return $dung(0);
    }

    /**
     * Chuoi id tu goc xuong danh muc dang xem, de cot ben trai to dam ca nhanh
     * chu khong chi rieng muc cuoi.
     */
    private function nhanhDangXem(int $dangXem): array
    {
        if (!$dangXem) {
            return [];
        }

        $nhanh = [];
        $id = $dangXem;

        // Chan 10 vong: du lieu hong (A la cha cua B, B la cha cua A) se lam
        // vong lap chay mai.
        for ($i = 0; $i < 10 && $id; $i++) {
            $nhanh[] = $id;
            $id = (int) DB::table('product_catalogues')->where('id', $id)->value('parent_id');
        }

        return $nhanh;
    }
}
