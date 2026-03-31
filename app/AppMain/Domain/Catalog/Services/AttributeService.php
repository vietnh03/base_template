<?php

namespace App\AppMain\Domain\Catalog\Services;

use App\AppMain\Domain\Catalog\DTOs\AttributeDTO;
use App\AppMain\Domain\Catalog\Repositories\AttributeRepository;

class AttributeService
{
    protected AttributeRepository $attributeRepository;

    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    public function getAttributesWithFilters(array $filters)
    {
        return $this->attributeRepository->getAttributesWithFilters($filters);
    }

    public function findAttribute(string $id)
    {
        return $this->attributeRepository->findById($id);
    }

    public function createAttribute(AttributeDTO $dto)
    {
        $data = $dto->onlyFilled();
        $options = $data['options'] ?? [];
        unset($data['options']);

        return $this->attributeRepository->create($data, $options);
    }

    public function updateAttribute(string $id, AttributeDTO $dto)
    {
        $data = $dto->onlyFilled();
        $options = $data['options'] ?? [];
        unset($data['options']);

        return $this->attributeRepository->update($id, $data, $options);
    }

    public function deleteAttribute(string $id)
    {
        return $this->attributeRepository->delete($id);
    }
}
