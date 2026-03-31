<?php

namespace App\AppMain\Application\Admin\Catalog\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Catalog\DTOs\AttributeDTO;
use App\AppMain\Domain\Catalog\Services\AttributeService;
use App\AppMain\Application\Admin\Catalog\Requests\AttributeRequest;
use App\AppMain\Application\Admin\Catalog\Requests\AttributeFilter;
use App\AppMain\Application\Admin\Catalog\Responses\AttributeResponse;
use App\Models\Attribute;
use Illuminate\Http\Request;
class AttributeController extends Controller
{
    protected AttributeService $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    public function index(Request $request)
    {
        /* $this->authorize('viewAny', Attribute::class); */
        return $this->baseAction(function () use ($request) {
            $filter = AttributeFilter::fromRequest($request);
            $request->validate($filter->validate());
            $attributes = $this->attributeService->getAttributesWithFilters($filter->toArray());
            return AttributeResponse::paginated($attributes);
        }, 'Attributes retrieved successfully');
    }

    public function show(string $id)
    {
        $attribute = $this->attributeService->findAttribute($id);
        /* $this->authorize('view', $attribute); */
        return $this->baseAction(function () use ($attribute) {
            return AttributeResponse::single($attribute);
        }, 'Attribute retrieved successfully');
    }

    public function store(AttributeRequest $request)
    {
        /* $this->authorize('create', Attribute::class); */
        return $this->baseActionTransaction(function () use ($request) {
            $dto = new AttributeDTO($request->validated());
            $attribute = $this->attributeService->createAttribute($dto);
            return AttributeResponse::single($attribute);
        }, 'Attribute created successfully');
    }

    public function update(AttributeRequest $request, string $id)
    {
        $attribute = $this->attributeService->findAttribute($id);
        /* $this->authorize('update', $attribute); */

        return $this->baseActionTransaction(function () use ($request, $id) {
            $dto = new AttributeDTO($request->validated());
            $this->attributeService->updateAttribute($id, $dto);
            $updatedAttribute = $this->attributeService->findAttribute($id);
            return AttributeResponse::single($updatedAttribute);
        }, 'Attribute updated successfully');
    }

    public function destroy(string $id)
    {
        $attribute = $this->attributeService->findAttribute($id);
        /* $this->authorize('delete', $attribute); */

        return $this->baseActionTransaction(function () use ($id) {
            $this->attributeService->deleteAttribute($id);
            return ['message' => 'Attribute deleted successfully'];
        }, 'Attribute deleted successfully');
    }
}
