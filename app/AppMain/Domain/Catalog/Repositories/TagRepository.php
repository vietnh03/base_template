namespace App\AppMain\Domain\Catalog\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Tag;

class TagRepository extends BaseRepository
{
public function getModel()
{
return Tag::class;
}

public function getTagsWithFilters($filters = [])
{
$query = $this->model->newQuery();

$this->applyLikeFilters($query, collect($filters)->only(['name', 'slug'])->toArray());

$this->applySortingFilter($query, $filters);

return $this->applyPagination($query, $filters) ?? $query->paginate($filters['per_page'] ?? 15);
}
}