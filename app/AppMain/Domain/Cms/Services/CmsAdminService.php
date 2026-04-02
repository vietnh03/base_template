<?php

namespace App\AppMain\Domain\Cms\Services;

use App\Models\CmsSection;
use App\Models\CmsSectionTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CmsAdminService
{
    /**
     * Update or create a translation for a CMS section.
     *
     * @param string $sectionId
     * @param string $locale
     * @param array $content
     * @return CmsSectionTranslation
     * @throws ModelNotFoundException
     */
    public function updateSectionTranslation(string $sectionId, string $locale, array $content): CmsSectionTranslation
    {
        $section = CmsSection::findOrFail($sectionId);

        $translation = CmsSectionTranslation::updateOrCreate(
            [
                'cms_section_id' => $section->id,
                'locale' => $locale,
            ],
            [
                'content' => $content,
            ]
        );

        return $translation;
    }
}
