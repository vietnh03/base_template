<?php

namespace App\AppMain\Application\Admin\Catalog\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Catalog\DTOs\TagDTO;
use App\AppMain\Domain\Catalog\Services\TagService;
use App\AppMain\Application\Admin\Catalog\Requests\TagRequest;
use App\AppMain\Application\Admin\Catalog\Requests\TagFilter;
use App\AppMain\Application\Admin\Catalog\Responses\TagResponse;
use Illuminate\Http\Request;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\Tag;

class TagController extends Controller
{
    protected TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index(Request $request)
    {
        /* $this->authorize('viewAny', Tag::class); */
        return $this->baseAction(function () use ($request) {
            $filter = TagFilter::fromRequest($request);
            $request->validate($filter->validate());
            $tags = $this->tagService->getTagsWithFilters($filter->toArray());
            return TagResponse::paginated($tags);
        }, 'Tags retrieved successfully');
    }

    public function show(string $id)
    {
        $tag = $this->tagService->findTag($id);
        if (!$tag) {
            throw new NotFoundHttpException('Tag not found');
        }
        /* $this->authorize('view', $tag); */
        return $this->baseAction(function () use ($tag) {
            return TagResponse::single($tag);
        }, 'Tag retrieved successfully');
    }

    public function store(TagRequest $request)
    {
        /* $this->authorize('create', Tag::class); */
        return $this->baseActionTransaction(function () use ($request) {
            $dto = TagDTO::fromRequest($request);
            $tag = $this->tagService->createTag($dto);
            return TagResponse::single($tag);
        }, 'Tag created successfully');
    }

    public function update(TagRequest $request, string $id)
    {
        $tag = $this->tagService->findTag($id);
        if (!$tag) {
            throw new NotFoundHttpException('Tag not found');
        }
        /* $this->authorize('update', $tag); */

        return $this->baseActionTransaction(function () use ($request, $id) {
            $dto = TagDTO::fromRequest($request);
            $this->tagService->updateTag($id, $dto);
            $updatedTag = $this->tagService->findTag($id);
            return TagResponse::single($updatedTag);
        }, 'Tag updated successfully');
    }

    public function destroy(string $id)
    {
        $tag = $this->tagService->findTag($id);
        if (!$tag) {
            throw new NotFoundHttpException('Tag not found');
        }
        /* $this->authorize('delete', $tag); */

        return $this->baseActionTransaction(function () use ($id) {
            $this->tagService->deleteTag($id);
            return ['message' => 'Tag deleted successfully'];
        }, 'Tag deleted successfully');
    }
}
