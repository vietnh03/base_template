use App\AppMain\Core\BaseModel;

class CategoryTranslation extends BaseModel
{
public $timestamps = false;

protected $fillable = [
'category_id',
'locale',
'name',
'slug',
'description',
'url_key',
'meta_title',
'meta_keywords',
'meta_description',
];
}