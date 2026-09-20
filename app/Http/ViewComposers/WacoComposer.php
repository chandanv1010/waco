<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Cap du lieu noi dung dung chung cho moi trang frontend WACO.
 *
 *   $intro    - cac doan van ban theo tu khoa (bang introduces)
 *   $features - cac khoi "icon + tieu de" lap lai, gom theo group (home_features)
 *
 * Nhieu khoi trong thiet ke lap lai o nhieu trang (dai cam ket, "Vi sao WACO",
 * mang luoi phan phoi). Dat o day thi moi trang deu co san, khong phai controller
 * nao cung tu truy van lai.
 */
class WacoComposer
{
    /**
     * Cache trong pham vi mot request.
     *
     * view()->composer dang ky cho 'frontend.*' nen chay lai voi MOI view con,
     * ke ca partial duoc include hang chuc lan trong mot trang. Khong cache thi
     * cung mot truy van chay lai dung bay nhieu lan.
     */
    protected static $cache = [];

    protected $language;

    public function __construct($language)
    {
        $this->language = $language;
    }

    public function compose(View $view)
    {
        $key = 'waco_' . $this->language;

        if (!isset(static::$cache[$key])) {
            static::$cache[$key] = [
                'intro' => DB::table('introduces')
                    ->where('language_id', $this->language)
                    ->pluck('content', 'keyword')
                    ->all(),

                'features' => DB::table('home_features')
                    ->where('publish', 2)
                    ->orderBy('group')
                    ->orderBy('order')
                    ->get()
                    ->groupBy('group'),
            ];
        }

        $view->with(static::$cache[$key]);
    }
}
