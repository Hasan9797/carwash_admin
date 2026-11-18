<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RegionRequest;
use App\Services\RegionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function __construct(
        protected RegionService $regionService,
    ) {}

    public function index(): View
    {
        $regions = $this->regionService->paginate(15);

        return view('regions.index', [
            'regions' => $regions,
        ]);
    }

    public function create(): View
    {
        return view('regions.create');
    }

    public function store(RegionRequest $request): RedirectResponse
    {
        $this->regionService->create($request->validated());

        return redirect()->route('regions.index')->with('success', __('common.region_created'));
    }

    public function edit(int $id): View|RedirectResponse
    {
        $region = $this->regionService->getById($id);
        if (! $region) {
            return redirect()->route('regions.index')->with('error', __('common.region_not_found'));
        }

        return view('regions.edit', [
            'region' => $region,
        ]);
    }

    public function update(RegionRequest $request, int $id): RedirectResponse
    {
        $updated = $this->regionService->update($id, $request->validated());

        if (! $updated) {
            return back()->with('error', __('common.region_cannot_update'));
        }

        return redirect()->route('regions.index')->with('success', __('common.region_updated'));
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $deleted = $this->regionService->delete($id);

            if (! $deleted) {
                return back()->with('error', __('common.region_cannot_delete'));
            }

            return redirect()->route('regions.index')->with('success', __('common.region_deleted'));
        } catch (\Throwable) {
            return back()->with('error', __('common.region_cannot_delete_has_relations'));
        }
    }
}
