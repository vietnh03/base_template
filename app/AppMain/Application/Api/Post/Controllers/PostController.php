<?php

namespace App\AppMain\Application\Api\Post\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Post\Services\PostService;
use App\AppMain\Domain\Post\Services\PostCategoryService;
use App\AppMain\Application\Api\Post\Responses\PostResponse;
use App\AppMain\Application\Api\Post\Responses\PostCategoryResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService,
        protected PostCategoryService $postCategoryService
    ) {
    }

    public function index(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $filters = $request->only(['category_id', 'tag_id', 'sort_by', 'sort_direction', 'per_page']);
            $filters['status'] = true; // Only show active posts

            $posts = $this->postService->getPostsWithFilters($filters);
            return PostResponse::paginated($posts);
        }, 'Posts retrieved successfully');
    }

    public function show(string $id)
    {
        return $this->baseAction(function () use ($id) {
            $post = $this->postService->findPost($id);

            if (!$post->status) {
                throw new \Exception('Post not found or inactive', 404);
            }

            return PostResponse::single($post);
        }, 'Post retrieved successfully');
    }

    public function categories(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $filters = $request->only(['parent_id', 'sort_by', 'sort_direction', 'per_page']);
            $filters['status'] = true;

            $categories = $this->postCategoryService->getCategoriesWithFilters($filters);
            return PostCategoryResponse::paginated($categories);
        }, 'Categories retrieved successfully');
    }
}
