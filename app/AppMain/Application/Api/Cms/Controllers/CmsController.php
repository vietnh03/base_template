<?php

namespace App\AppMain\Application\Api\Cms\Controllers;

use App\Http\Controllers\Controller;
use App\AppMain\Domain\Cms\Services\CmsService;
use Illuminate\Http\JsonResponse;

class CmsController extends Controller
{
    public function __construct(
        protected CmsService $cmsService
    ) {
    }

    public function getPageData(string $slug): JsonResponse
    {
        $locale = request()->get('locale');
        $locale = is_string($locale) && in_array($locale, ['vi', 'en']) ? $locale : app()->getLocale();

        $data = $this->cmsService->getPageData($slug, $locale);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Page not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Page data retrieved successfully',
            'data' => $data
        ]);
    }
}
