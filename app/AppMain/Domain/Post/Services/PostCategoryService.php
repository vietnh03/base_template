<?php

namespace App\AppMain\Domain\Post\Services;

use App\AppMain\Domain\Post\DTOs\PostCategoryDTO;
use App\AppMain\Domain\Post\Repositories\PostCategoryRepository;

class PostCategoryService
{
    protected PostCategoryRepository $postCategoryRepository;

    public function __construct(PostCategoryRepository $postCategoryRepository)
    {
        $this->postCategoryRepository = $postCategoryRepository;
    }

    public function getCategoriesWithFilters(array $filters)
    {
        return $this->postCategoryRepository->getCategoriesWithFilters($filters);
    }

    public function findCategory(string $id)
    {
        return $this->postCategoryRepository->findById($id);
    }

    public function createCategory(PostCategoryDTO $dto)
    {
        $data = $dto->toArray();
        unset($data['translations']);

        return $this->postCategoryRepository->create($data, $dto->translations);
    }

    public function updateCategory(string $id, PostCategoryDTO $dto)
    {
        $data = $dto->onlyFilled();
        unset($data['translations']);

        return $this->postCategoryRepository->update($id, $data, $dto->translations);
    }

    public function deleteCategory(string $id): bool
    {
        return $this->postCategoryRepository->delete($id);
    }
}
