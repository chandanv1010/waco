<?php  
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Core\SystemRepository;

class SystemComposer
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
    protected $systemRepository;

    public function __construct(
        SystemRepository $systemRepository,
        $language
    ){
        $this->systemRepository = $systemRepository;
        $this->language = $language;
    }

    public function compose(View $view)
    {
        $key = 'system_' . $this->language;
        if (!isset(static::$cache[$key])) {
            $system = $this->systemRepository->findByCondition(
                [
                    ['language_id', '=', $this->language]
                ],
                TRUE
            );
            static::$cache[$key] = convert_array($system, 'keyword', 'content');
        }

        $view->with('system', static::$cache[$key]);
    }
}