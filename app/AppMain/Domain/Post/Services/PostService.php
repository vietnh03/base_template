<?php

namespace App\AppMain\Domain\Post\Services;

use App\AppMain\Domain\Post\DTOs\PostDTO;
use App\AppMain\Domain\Post\Repositories\PostRepository;

class PostService
{
    protected PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getPostsWithFilters(array $filters)
    {
        return $this->postRepository->getPostsWithFilters($filters);
    }

    public function findPost(string $id)
    {
        return $this->postRepository->findById($id);
    }

    public function createPost(PostDTO $dto)
    {
        $data = $dto->toArray();
        unset($data['translations'], $data['categories'], $data['tags']);

        return $this->postRepository->create($data, $dto->translations, $dto->categories, $dto->tags);
    }

    public function updatePost(string $id, PostDTO $dto)
    {
        $data = $dto->onlyFilled();
        unset($data['translations'], $data['categories'], $data['tags']);

        return $this->postRepository->update($id, $data, $dto->translations, $dto->categories, $dto->tags);
    }

    public function deletePost(string $id): bool
    {
        return $this->postRepository->delete($id);
    }
}
