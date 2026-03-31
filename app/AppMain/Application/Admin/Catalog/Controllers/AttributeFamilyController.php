<?php

namespace App\AppMain\Application\Admin\Catalog\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Catalog\DTOs\AttributeFamilyDTO;
use App\AppMain\Domain\Catalog\Services\AttributeFamilyService;
use App\AppMain\Application\Admin\Catalog\Requests\AttributeFamilyRequest;
use App\AppMain\Application\Admin\Catalog\Requests\AttributeFamilyFilter;
use App\AppMain\Application\Admin\Catalog\Responses\AttributeFamilyResponse;
use App\Models\AttributeFamily;
use Illuminate\Http\Request;
class AttributeFamilyController extends Controller
{
    protected AttributeFamilyService $attributeFamilyService;

    public function __construct(AttributeFamilyService $attributeFamilyService)
    {
        $this->attributeFamilyService = $attributeFamilyService;
    }

    public function index(Request $request)
    {
        /* $this->authorize('viewAny', AttributeFamily::class); */
        return $this->baseAction(function () use ($request) {
            $filter = AttributeFamilyFilter::fromRequest($request);
            $request->validate($filter->validate());
            $families = $this->attributeFamilyService->getAttributeFamiliesWithFilters($filter->toArray());
            return AttributeFamilyResponse::paginated($families);
        }, 'Attribute families retrieved successfully');
    }

    public function show(string $id)
    {
        $family = $this->attributeFamilyService->findAttributeFamily($id);
        /* $this->authorize('view', $family); */
        return $this->baseAction(function () use ($family) {
            return AttributeFamilyResponse::single($family);
        }, 'Attribute family retrieved successfully');
    }

    public function store(AttributeFamilyRequest $request)
    {
        /* $this->authorize('create', AttributeFamily::class); */
        return $this->baseActionTransaction(function () use ($request) {
            $dto = new AttributeFamilyDTO($request->validated());
            $family = $this->attributeFamilyService->createAttributeFamily($dto);
            return AttributeFamilyResponse::single($family);
        }, 'Attribute family created successfully');
    }

    public function update(AttributeFamilyRequest $request, string $id)
    {
        $family = $this->attributeFamilyService->findAttributeFamily($id);
        /* $this->authorize('update', $family); */

        return $this->baseActionTransaction(function () use ($request, $id) {
            $dto = new AttributeFamilyDTO($request->validated());
            $this->attributeFamilyService->updateAttributeFamily($id, $dto);
            $updatedFamily = $this->attributeFamilyService->findAttributeFamily($id);
            return AttributeFamilyResponse::single($updatedFamily);
        }, 'Attribute family updated successfully');
    }

    public function destroy(string $id)
    {
        $family = $this->attributeFamilyService->findAttributeFamily($id);
        /* $this->authorize('delete', $family); */

        return $this->baseActionTransaction(function () use ($id) {
            $this->attributeFamilyService->deleteAttributeFamily($id);
            return ['message' => 'Attribute family deleted successfully'];
        }, 'Attribute family deleted successfully');
    }
}
