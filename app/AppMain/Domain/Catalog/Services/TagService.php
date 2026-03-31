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
        $data = $dto->toArray();
        unset($data['translations']);
        return $this->tagRepository->create($data, $dto->translations);
    }

    public function updateTag(string $id, TagDTO $dto)
    {
        $data = $dto->onlyFilled();
        unset($data['translations']);
        return $this->tagRepository->update($id, $data, $dto->translations);
    }

    public function deleteTag(string $id): bool
    {
        return $this->tagRepository->delete($id);
    }
}
