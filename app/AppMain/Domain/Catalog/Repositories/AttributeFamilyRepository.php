<?php

namespace App\AppMain\Domain\Catalog\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\AttributeFamily;
use Illuminate\Support\Facades\DB;

class AttributeFamilyRepository extends BaseRepository
{
    public function getModel()
    {
        return AttributeFamily::class;
    }

    public function findById($id)
    {
        return $this->model->with('groups.attribute_group_mappings.attribute')->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $groups = $data['groups'] ?? [];
            unset($data['groups']);

            $family = $this->model->create($data);

            if (!empty($groups)) {
                $this->syncGroups($family, $groups);
            }

            return $family->load('groups.attribute_group_mappings.attribute');
        });
    }

    public function update($id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            // findById already throws ModelNotFoundException if not found
            $family = $this->findById($id);

            $groups = $data['groups'] ?? [];
            unset($data['groups']);

            $family->update($data);

            if (!empty($groups)) {
                $this->syncGroups($family, $groups);
            }

            return true;
        });
    }

    protected function syncGroups($family, array $groups)
    {
        $existingGroups = $family->groups()->get();
        $groupIds = collect($groups)->pluck('id')->filter()->toArray();

        // Delete removed groups
        $existingGroups->each(function ($group) use ($groupIds) {
            if (!in_array($group->id, $groupIds)) {
                $group->attribute_group_mappings()->delete();
                $group->delete();
            }
        });

        // Update or Create groups
        foreach ($groups as $groupData) {
            if (isset($groupData['id']) && $group = $existingGroups->find($groupData['id'])) {
                $group->update(collect($groupData)->only(['name', 'position'])->toArray());
            } else {
                $group = $family->groups()->create(collect($groupData)->only(['name', 'position'])->toArray());
            }

            if (isset($groupData['attributes'])) {
                $existingMappings = $group->attribute_group_mappings()->pluck('attribute_id')->toArray();
                $newAttributes = $groupData['attributes'];

                $toDelete = array_diff($existingMappings, $newAttributes);
                $toCreate = array_diff($newAttributes, $existingMappings);

                if (!empty($toDelete)) {
                    $group->attribute_group_mappings()->whereIn('attribute_id', $toDelete)->delete();
                }

                foreach ($toCreate as $attrId) {
                    $group->attribute_group_mappings()->create(['attribute_id' => $attrId]);
                }
            }
        }
    }

    public function getAttributeFamiliesWithFilters($filters = [])
    {
        $query = $this->model->newQuery();

        $this->applyLikeFilters($query, collect($filters)->only(['code', 'name'])->toArray());

        $this->applySortingFilter($query, $filters);

        return $this->applyPagination($query, $filters) ?? $query->paginate($filters['per_page'] ?? 15);
    }
}
