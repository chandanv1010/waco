<?php  
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Core\LanguageRepository;

class LanguageComposer
{

    /**
     * Cache trong pham vi mot request.
     *
     * view()->composer dang dang ky cho 'frontend.*' nen chay lai voi MOI view
     * con, ke ca cac partial nho duoc include hang chuc lan trong mot trang.
     * Khong cache thi cung mot truy van chay lai dung bay nhieu lan - do chinh
     * la cho lam trang chu sinh ra 175 truy van thua.
     *
     * MenuComposer trong cung thu muc nay da lam san theo cach nay.
     */
    protected static $cache = [];

    protected $language;
    protected $languageRepository;

    public function __construct(
        LanguageRepository $languageRepository,
        $language
    ){
        $this->languageRepository = $languageRepository;
    }

    public function compose(View $view)
    {
        if (!isset(static::$cache['languages'])) {
            static::$cache['languages'] = $this->languageRepository->findByCondition(...$this->agrument());
        }

        $view->with('languages', static::$cache['languages']);
    }

    private function agrument(){
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => [],
            'orderBy' => ['current', 'desc']
        ];
    }

}