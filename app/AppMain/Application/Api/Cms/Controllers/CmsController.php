<?php

namespace App\AppMain\Application\Api\Cms\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Cms\Services\CmsService;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function __construct(
        protected CmsService $cmsService
    ) {
    }

    public function getPageData(string $slug, Request $request)
    {
        return $this->baseAction(function () use ($slug, $request) {
            $locale = $request->get('locale');
            $locale = is_string($locale) && in_array($locale, ['vi', 'en']) ? $locale : app()->getLocale();

            $data = $this->cmsService->getPageData($slug, $locale);

            if (!$data) {
                throw new \Exception('Page not found', 404);
            }

            return $data;
        }, 'Page data retrieved successfully');
    }
}
