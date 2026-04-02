<?php

namespace App\AppMain\Application\Admin\Post\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Post\DTOs\PostTagDTO;
use App\AppMain\Domain\Post\Services\PostTagService;
use App\AppMain\Application\Admin\Post\Requests\PostTagRequest;
use App\AppMain\Application\Admin\Post\Requests\PostTagFilter;
use App\AppMain\Application\Admin\Post\Responses\PostTagResponse;
use Illuminate\Http\Request;

class PostTagController extends Controller
{
    protected PostTagService $postTagService;

    public function __construct(PostTagService $postTagService)
    {
        $this->postTagService = $postTagService;
    }

    public function index(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $filter = PostTagFilter::fromRequest($request);
            $request->validate($filter->validate());
            $tags = $this->postTagService->getTagsWithFilters($filter->toArray());
            return PostTagResponse::paginated($tags);
        }, 'Post tags retrieved successfully');
    }

    public function show(string $id)
    {
        return $this->baseAction(function () use ($id) {
            $tag = $this->postTagService->findTag($id);
            return PostTagResponse::single($tag);
        }, 'Post tag retrieved successfully');
    }

    public function store(PostTagRequest $request)
    {
        return $this->baseActionTransaction(function () use ($request) {
            $dto = PostTagDTO::fromRequest($request);
            $tag = $this->postTagService->createTag($dto);
            return PostTagResponse::single($tag);
        }, 'Post tag created successfully');
    }

    public function update(PostTagRequest $request, string $id)
    {
        return $this->baseActionTransaction(function () use ($request, $id) {
            $dto = PostTagDTO::fromRequest($request);
            $this->postTagService->updateTag($id, $dto);
            $updatedTag = $this->postTagService->findTag($id);
            return PostTagResponse::single($updatedTag);
        }, 'Post tag updated successfully');
    }

    public function destroy(string $id)
    {
        return $this->baseActionTransaction(function () use ($id) {
            $this->postTagService->deleteTag($id);
            return ['message' => 'Post tag deleted successfully'];
        }, 'Post tag deleted successfully');
    }
}
