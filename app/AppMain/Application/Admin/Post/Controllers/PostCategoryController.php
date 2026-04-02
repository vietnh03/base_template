<?php

namespace App\AppMain\Application\Admin\Post\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Post\DTOs\PostCategoryDTO;
use App\AppMain\Domain\Post\Services\PostCategoryService;
use App\AppMain\Application\Admin\Post\Requests\PostCategoryRequest;
use App\AppMain\Application\Admin\Post\Requests\PostCategoryFilter;
use App\AppMain\Application\Admin\Post\Responses\PostCategoryResponse;
use Illuminate\Http\Request;

class PostCategoryController extends Controller
{
    protected PostCategoryService $postCategoryService;

    public function __construct(PostCategoryService $postCategoryService)
    {
        $this->postCategoryService = $postCategoryService;
    }

    public function index(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $filter = PostCategoryFilter::fromRequest($request);
            $request->validate($filter->validate());
            $categories = $this->postCategoryService->getCategoriesWithFilters($filter->toArray());
            return PostCategoryResponse::paginated($categories);
        }, 'Post categories retrieved successfully');
    }

    public function show(string $id)
    {
        return $this->baseAction(function () use ($id) {
            $category = $this->postCategoryService->findCategory($id);
            return PostCategoryResponse::single($category);
        }, 'Post category retrieved successfully');
    }

    public function store(PostCategoryRequest $request)
    {
        return $this->baseActionTransaction(function () use ($request) {
            $dto = PostCategoryDTO::fromRequest($request);
            $category = $this->postCategoryService->createCategory($dto);
            return PostCategoryResponse::single($category);
        }, 'Post category created successfully');
    }

    public function update(PostCategoryRequest $request, string $id)
    {
        return $this->baseActionTransaction(function () use ($request, $id) {
            $dto = PostCategoryDTO::fromRequest($request);
            $this->postCategoryService->updateCategory($id, $dto);
            $updatedCategory = $this->postCategoryService->findCategory($id);
            return PostCategoryResponse::single($updatedCategory);
        }, 'Post category updated successfully');
    }

    public function destroy(string $id)
    {
        return $this->baseActionTransaction(function () use ($id) {
            $this->postCategoryService->deleteCategory($id);
            return ['message' => 'Post category deleted successfully'];
        }, 'Post category deleted successfully');
    }
}
