<?php

namespace App\AppMain\Domain\Catalog\Services;

use App\AppMain\Domain\Catalog\DTOs\TagDTO;
use App\AppMain\Domain\Catalog\Repositories\TagRepository;

class TagService
{
    protected TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getTagsWithFilters(array $filters)
    {
        return $this->tagRepository->getTagsWithFilters($filters);
    }

    public function findTag(string $id)
    {
        return $this->tagRepository->findById($id);
    }

    public function createTag(TagDTO $dto)
    {
        return $this->tagRepository->create($dto->onlyFilled());
    }

    public function updateTag(string $id, TagDTO $dto)
    {
        return $this->tagRepository->update($id, $dto->onlyFilled());
    }

    public function deleteTag(string $id): bool
    {
        return $this->tagRepository->delete($id);
    }
}
