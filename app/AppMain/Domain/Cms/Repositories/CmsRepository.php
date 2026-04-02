<?php

namespace App\AppMain\Domain\Cms\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\CmsPage;

class CmsRepository extends BaseRepository
{
    public function getModel()
    {
        return CmsPage::class;
    }

    public function findBySlug(string $slug, string $locale)
    {
        return $this->model->newQuery()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'translations' => function ($query) use ($locale) {
                    $query->where('locale', $locale);
                },
                'sections' => function ($query) use ($locale) {
                    $query->where('is_active', true)
                        ->with([
                            'translations' => function ($q) use ($locale) {
                                $q->where('locale', $locale);
                            }
                        ]);
                }
            ])
            ->first();
    }
}
