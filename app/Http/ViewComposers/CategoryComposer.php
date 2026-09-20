<?php  
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Product\ProductCatalogueRepository;

class CategoryComposer
{

    protected $language;
    protected $productCatalogueRepository;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        $language
    ){
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->language = $language;
    }

    /**
     * Cache trong pham vi mot request.
     *
     * view()->composer dang dang ky cho 'frontend.*' nen chay lai voi MOI view
     * con, ke ca cac partial nho duoc include hang chuc lan trong mot trang.
     * Rieng composer nay con nap kem quan he 'products' nen la truy van nang
     * nhat trong nhom - khong cache thi moi partial lai keo ve toan bo san pham.
     *
     * MenuComposer trong cung thu muc nay da lam san theo cach nay.
     */
    protected static $cache = [];

    public function compose(View $view)
    {
        $language = $this->language;
        $key = 'category_' . $language;

        if (!isset(static::$cache[$key])) {
            $category = $this->productCatalogueRepository->all(
                [
                    'products',
                    'languages' => function($query) use ($language){
                        $query->where('language_id', $language);
                    }
                ]
            );
            static::$cache[$key] = recursive($category);
        }

        $view->with('category', static::$cache[$key]);
    }

   

}