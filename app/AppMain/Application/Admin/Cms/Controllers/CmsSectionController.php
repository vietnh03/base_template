<?php

namespace App\AppMain\Application\Admin\Cms\Controllers;

use App\Http\Controllers\Controller;
use App\AppMain\Application\Admin\Cms\Requests\UpdateCmsSectionRequest;
use App\AppMain\Domain\Cms\Services\CmsAdminService;
use Illuminate\Http\JsonResponse;

class CmsSectionController extends Controller
{
    public function __construct(
        protected CmsAdminService $cmsAdminService
    ) {
    }

    /**
     * Update the translation content of a CMS Section.
     */
    public function update(UpdateCmsSectionRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

        try {
            $translation = $this->cmsAdminService->updateSectionTranslation(
                $id,
                $validated['locale'],
                $validated['content']
            );

            return response()->json([
                'status' => 'success',
                'message' => 'CMS Section translation updated successfully',
                'data' => $translation
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'CMS Section not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the CMS Section',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
