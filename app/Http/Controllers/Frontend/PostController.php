<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Models\Post;
use App\Repositories\Post\PostCatalogueRepository;
use App\Repositories\Post\PostRepository;
use Illuminate\Support\Facades\DB;

/**
 * Trang chi tiet bai viet.
 *
 * Controller nay duoc goi tu RouterController theo canonical trong bang routers.
 */
class PostController extends FrontendController
{
    protected $postCatalogueRepository;
    protected $postRepository;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostRepository $postRepository
    ) {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postRepository = $postRepository;
        parent::__construct();
    }

    public function index($id, $request)
    {
        $post = $this->postRepository->getPostById($id, $this->language, config('apps.general.defaultPublish'));

        if (is_null($post)) {
            abort(404);
        }

        // Tang luot xem bang mot cau UPDATE thay vi doc roi ghi: hai nguoi cung
        // mo mot bai thi khong de mat luot.
        Post::where('id', $id)->increment('viewed');

        $postCatalogue = $this->postCatalogueRepository
            ->getPostCatalogueById($post->post_catalogue_id, $this->language);

        $breadcrumb = is_null($postCatalogue)
            ? []
            : $this->postCatalogueRepository->breadcrumb($postCatalogue, $this->language);

        // Bai cung chuyen muc, bo chinh bai dang xem.
        $related = DB::table('posts as p')
            ->join('post_language as pl', function ($join) {
                $join->on('pl.post_id', '=', 'p.id')
                     ->where('pl.language_id', '=', $this->language);
            })
            ->where('p.publish', 2)
            ->whereNull('p.deleted_at')
            ->where('p.post_catalogue_id', $post->post_catalogue_id)
            ->where('p.id', '!=', $post->id)
            ->orderByDesc('p.released_at')
            ->limit(3)
            ->get(['p.id', 'p.image', 'p.released_at', 'pl.name', 'pl.canonical', 'pl.description']);

        return view('frontend.post.post.index', [
            'config' => ['js' => [], 'css' => []],
            'system' => $this->system,
            'seo' => seo($post),
            'post' => $post,
            'postCatalogue' => $postCatalogue,
            'breadcrumb' => $breadcrumb,
            'related' => $related,
        ]);
    }
}
