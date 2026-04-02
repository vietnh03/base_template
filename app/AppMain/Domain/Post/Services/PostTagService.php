<?php

namespace App\AppMain\Domain\Post\Services;

use App\AppMain\Domain\Post\DTOs\PostTagDTO;
use App\AppMain\Domain\Post\Repositories\PostTagRepository;

class PostTagService
{
    protected PostTagRepository $postTagRepository;

    public function __construct(PostTagRepository $postTagRepository)
    {
        $this->postTagRepository = $postTagRepository;
    }

    public function getTagsWithFilters(array $filters)
    {
        return $this->postTagRepository->getTagsWithFilters($filters);
    }

    public function findTag(string $id)
    {
        return $this->postTagRepository->findById($id);
    }

    public function createTag(PostTagDTO $dto)
    {
        $data = $dto->toArray();
        unset($data['translations']);

        return $this->postTagRepository->create($data, $dto->translations);
    }

    public function updateTag(string $id, PostTagDTO $dto)
    {
        $data = $dto->onlyFilled();
        unset($data['translations']);

        return $this->postTagRepository->update($id, $data, $dto->translations);
    }

    public function deleteTag(string $id): bool
    {
        return $this->postTagRepository->delete($id);
    }
}
