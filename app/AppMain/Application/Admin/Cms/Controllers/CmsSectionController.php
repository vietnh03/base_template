<?php

namespace App\AppMain\Application\Admin\Cms\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Application\Admin\Cms\Requests\UpdateCmsSectionRequest;
use App\AppMain\Domain\Cms\Services\CmsAdminService;

class CmsSectionController extends Controller
{
    public function __construct(
        protected CmsAdminService $cmsAdminService
    ) {
    }

    /**
     * Update the translation content of a CMS Section.
     */
    public function update(UpdateCmsSectionRequest $request, string $id)
    {
        return $this->baseAction(function () use ($id, $request) {
            $validated = $request->validated();

            return $this->cmsAdminService->updateSectionTranslation(
                $id,
                $validated['locale'],
                $validated['content']
            );
        }, 'CMS Section translation updated successfully');
    }
}
