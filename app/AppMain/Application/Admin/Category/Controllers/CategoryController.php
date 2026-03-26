<?php

namespace App\AppMain\Application\Admin\Category\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::with('translations')->get();
        return response()->json($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $category = Category::create($request->only(['parent_id', 'position', 'status']));

        if ($request->has('translations')) {
            foreach ($request->translations as $locale => $data) {
                $category->translations()->create(array_merge($data, ['locale' => $locale]));
            }
        }

        return response()->json($category->load('translations'), 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = Category::with('translations')->find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->update($request->only(['parent_id', 'position', 'status']));

        if ($request->has('translations')) {
            // Simple update/create for translations
            foreach ($request->translations as $locale => $data) {
                $category->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $data
                );
            }
        }

        return response()->json($category->load('translations'));
    }

    public function destroy(int $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();
        return response()->json(null, 204);
    }
}
