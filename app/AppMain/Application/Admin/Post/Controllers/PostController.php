<?php

namespace App\AppMain\Application\Admin\Post\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Post\DTOs\PostDTO;
use App\AppMain\Domain\Post\Services\PostService;
use App\AppMain\Application\Admin\Post\Requests\PostRequest;
use App\AppMain\Application\Admin\Post\Requests\PostFilter;
use App\AppMain\Application\Admin\Post\Responses\PostResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $filter = PostFilter::fromRequest($request);
            $request->validate($filter->validate());
            $posts = $this->postService->getPostsWithFilters($filter->toArray());
            return PostResponse::paginated($posts);
        }, 'Posts retrieved successfully');
    }

    public function show(string $id)
    {
        return $this->baseAction(function () use ($id) {
            $post = $this->postService->findPost($id);
            return PostResponse::single($post);
        }, 'Post retrieved successfully');
    }

    public function store(PostRequest $request)
    {
        return $this->baseActionTransaction(function () use ($request) {
            $dto = PostDTO::fromRequest($request);
            $post = $this->postService->createPost($dto);
            return PostResponse::single($post);
        }, 'Post created successfully');
    }

    public function update(PostRequest $request, string $id)
    {
        return $this->baseActionTransaction(function () use ($request, $id) {
            $dto = PostDTO::fromRequest($request);
            $this->postService->updatePost($id, $dto);
            $updatedPost = $this->postService->findPost($id);
            return PostResponse::single($updatedPost);
        }, 'Post updated successfully');
    }

    public function destroy(string $id)
    {
        return $this->baseActionTransaction(function () use ($id) {
            $this->postService->deletePost($id);
            return ['message' => 'Post deleted successfully'];
        }, 'Post deleted successfully');
    }
}
