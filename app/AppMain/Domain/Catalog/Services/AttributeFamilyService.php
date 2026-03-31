<?php

namespace App\AppMain\Domain\Catalog\Services;

use App\AppMain\Domain\Catalog\DTOs\AttributeFamilyDTO;
use App\AppMain\Domain\Catalog\Repositories\AttributeFamilyRepository;

class AttributeFamilyService
{
    protected AttributeFamilyRepository $attributeFamilyRepository;

    public function __construct(AttributeFamilyRepository $attributeFamilyRepository)
    {
        $this->attributeFamilyRepository = $attributeFamilyRepository;
    }

    public function getAttributeFamiliesWithFilters(array $filters)
    {
        return $this->attributeFamilyRepository->getAttributeFamiliesWithFilters($filters);
    }

    public function findAttributeFamily(string $id)
    {
        return $this->attributeFamilyRepository->findById($id);
    }

    public function createAttributeFamily(AttributeFamilyDTO $dto)
    {
        return $this->attributeFamilyRepository->create($dto->onlyFilled());
    }

    public function updateAttributeFamily(string $id, AttributeFamilyDTO $dto)
    {
        return $this->attributeFamilyRepository->update($id, $dto->onlyFilled());
    }

    public function deleteAttributeFamily(string $id)
    {
        return $this->attributeFamilyRepository->delete($id);
    }
}
