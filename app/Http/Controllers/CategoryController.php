<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
    ) {}

    public function index(): View
    {
        $categories = $this->categoryService->paginate(15);

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $this->categoryService->create($request->validated());

        return redirect()->route('categories.index')->with('success', __('common.category_created'));
    }

    public function edit(int $id): View|RedirectResponse
    {
        $category = $this->categoryService->getById($id);
        if (! $category) {
            return redirect()->route('categories.index')->with('error', __('common.category_not_found'));
        }

        return view('categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(CategoryRequest $request, int $id): RedirectResponse
    {
        $updated = $this->categoryService->update($id, $request->validated());

        if (! $updated) {
            return back()->with('error', __('common.category_cannot_update'));
        }

        return redirect()->route('categories.index')->with('success', __('common.category_updated'));
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $deleted = $this->categoryService->delete($id);

            if (! $deleted) {
                return back()->with('error', __('common.category_cannot_delete'));
            }

            return redirect()->route('categories.index')->with('success', __('common.category_deleted'));
        } catch (\Throwable) {
            return back()->with('error', __('common.category_cannot_delete_has_orders'));
        }
    }
}
