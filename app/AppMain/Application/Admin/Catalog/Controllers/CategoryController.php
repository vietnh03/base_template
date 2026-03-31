<?php

namespace App\AppMain\Application\Admin\Catalog\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Catalog\DTOs\CategoryDTO;
use App\AppMain\Domain\Catalog\Services\CategoryService;
use App\AppMain\Application\Admin\Catalog\Requests\CategoryRequest;
use App\AppMain\Application\Admin\Catalog\Requests\CategoryFilter;
use App\AppMain\Application\Admin\Catalog\Responses\CategoryResponse;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);
        return $this->baseAction(function () use ($request) {
            $filter = CategoryFilter::fromRequest($request);
            $request->validate($filter->validate());
            $categories = $this->categoryService->getCategoriesWithFilters($filter->toArray());
            return CategoryResponse::paginated($categories);
        }, 'Categories retrieved successfully');
    }

    public function show(string $id)
    {
        $category = $this->categoryService->findCategory($id);
        $this->authorize('view', $category);
        return $this->baseAction(function () use ($category) {
            return CategoryResponse::single($category);
        }, 'Category retrieved successfully');
    }

    public function store(CategoryRequest $request)
    {
        $this->authorize('create', Category::class);
        return $this->baseActionTransaction(function () use ($request) {
            $dto = CategoryDTO::fromRequest($request);
            $category = $this->categoryService->createCategory($dto);
            return CategoryResponse::single($category);
        }, 'Category created successfully');
    }

    public function update(CategoryRequest $request, string $id)
    {
        $category = $this->categoryService->findCategory($id);
        $this->authorize('update', $category);

        return $this->baseActionTransaction(function () use ($request, $id) {
            $dto = CategoryDTO::fromRequest($request);
            $this->categoryService->updateCategory($id, $dto);
            $updatedCategory = $this->categoryService->findCategory($id);
            return CategoryResponse::single($updatedCategory);
        }, 'Category updated successfully');
    }

    public function destroy(string $id)
    {
        $category = $this->categoryService->findCategory($id);
        $this->authorize('delete', $category);

        return $this->baseActionTransaction(function () use ($id) {
            $this->categoryService->deleteCategory($id);
            return ['message' => 'Category deleted successfully'];
        }, 'Category deleted successfully');
    }
}
