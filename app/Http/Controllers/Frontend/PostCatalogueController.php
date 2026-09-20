<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Post\PostCatalogueRepository;
use App\Services\V1\Post\PostService;

/**
 * Trang danh sach bai viet (Tin tuc - Su kien).
 *
 * Controller nay duoc goi tu RouterController theo canonical trong bang routers,
 * khong khai bao route rieng.
 */
class PostCatalogueController extends FrontendController
{
    protected $postCatalogueRepository;
    protected $postService;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostService $postService
    ) {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postService = $postService;
        parent::__construct();
    }

    public function index($id, $request, $page = 1)
    {
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($id, $this->language);
        $breadcrumb = $this->postCatalogueRepository->breadcrumb($postCatalogue, $this->language);

        $posts = $this->postService->paginate(
            $request,
            $this->language,
            $postCatalogue,
            $page,
            ['path' => $postCatalogue->canonical],
            ['posts.released_at', 'desc']
        );

        // Cac chuyen muc cap 1 lam dai loc o dau danh sach (thiet ke goi la
        // "Tin tuc | Su kien | Chia se kinh nghiem"). Quan tri them chuyen muc
        // trong module Bai viet la tu moc them tab.
        $tabs = $this->postCatalogueRepository->findByCondition(
            [
                ['publish', '=', 2],
                ['parent_id', '=', 0],
            ],
            true,
            ['languages' => fn($q) => $q->where('language_id', $this->language)],
            ['order', 'asc']
        );

        // Bai noi bat dat o dau trang: lay bai moi nhat cua chuyen muc, roi bo
        // no ra khoi luoi ben duoi de khong hien hai lan.
        $featured = $posts->first();

        return view('frontend.post.catalogue.index', [
            'config' => ['js' => [], 'css' => []],
            'system' => $this->system,
            'seo' => seo($postCatalogue, $page),
            'breadcrumb' => $breadcrumb,
            'postCatalogue' => $postCatalogue,
            'posts' => $posts,
            'tabs' => $tabs,
            'featured' => $page === 1 ? $featured : null,
        ]);
    }

    /** RouterController goi ham nay cho duong dan /{canonical}/trang-{page}. */
    public function page($id, $request, $page = 1)
    {
        return $this->index($id, $request, $page);
    }
}
