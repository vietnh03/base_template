<?php

namespace App\AppMain\Domain\Catalog\Services;

use App\AppMain\Domain\Catalog\DTOs\CategoryDTO;
use App\AppMain\Domain\Catalog\Repositories\CategoryRepository;

class CategoryService
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategoriesWithFilters(array $filters)
    {
        return $this->categoryRepository->getCategoriesWithFilters($filters);
    }

    public function findCategory(string $id)
    {
        return $this->categoryRepository->findById($id);
    }

    public function createCategory(CategoryDTO $dto)
    {
        $data = $dto->toArray();
        unset($data['translations']);

        return $this->categoryRepository->create($data, $dto->translations);
    }

    public function updateCategory(string $id, CategoryDTO $dto)
    {
        $data = $dto->onlyFilled();
        unset($data['translations']);

        return $this->categoryRepository->update($id, $data, $dto->translations);
    }

    public function deleteCategory(string $id): bool
    {
        return $this->categoryRepository->delete($id);
    }
}
