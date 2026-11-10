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

        return redirect()->route('categories.index')->with('success', 'Xizmat turi muvaffaqiyatli qo\'shildi.');
    }

    public function edit(int $id): View|RedirectResponse
    {
        $category = $this->categoryService->getById($id);
        if (! $category) {
            return redirect()->route('categories.index')->with('error', 'Xizmat turi topilmadi.');
        }

        return view('categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(CategoryRequest $request, int $id): RedirectResponse
    {
        $updated = $this->categoryService->update($id, $request->validated());

        if (! $updated) {
            return back()->with('error', 'Xizmat turini yangilab bo\'lmadi.');
        }

        return redirect()->route('categories.index')->with('success', 'Xizmat turi muvaffaqiyatli yangilandi.');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $deleted = $this->categoryService->delete($id);

            if (! $deleted) {
                return back()->with('error', 'Xizmat turini o\'chirib bo\'lmadi.');
            }

            return redirect()->route('categories.index')->with('success', 'Xizmat turi o\'chirildi.');
        } catch (\Throwable) {
            return back()->with('error', 'Ushbu xizmat turiga bog\'langan zakazlar mavjud bo\'lgani uchun uni o\'chirib bo\'lmaydi.');
        }
    }
}
