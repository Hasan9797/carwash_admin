<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\UserRequest;
use App\Services\RegionService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected RegionService $regionService,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->input('search'),
            'role' => $request->input('role'),
            'region_id' => $request->input('region_id'),
        ];

        $users = $this->userService->paginate($filters, 15);
        $regions = $this->regionService->listActive();

        return view('users.index', [
            'users' => $users,
            'regions' => $regions,
            'roles' => UserRole::cases(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $regions = $this->regionService->listActive();

        return view('users.create', [
            'regions' => $regions,
            'roles' => UserRole::cases(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return redirect()->route('users.index')->with('success', __('common.user_created'));
    }

    public function edit(int $id): View|RedirectResponse
    {
        $user = $this->userService->getById($id);
        if (! $user) {
            return redirect()->route('users.index')->with('error', __('common.user_not_found'));
        }

        $regions = $this->regionService->listActive();

        return view('users.edit', [
            'user' => $user,
            'regions' => $regions,
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(UserRequest $request, int $id): RedirectResponse
    {
        $updated = $this->userService->update($id, $request->validated());

        if (! $updated) {
            return back()->with('error', __('common.user_cannot_update'));
        }

        return redirect()->route('users.index')->with('success', __('common.user_updated'));
    }

    public function destroy(int $id): RedirectResponse
    {
        if (Auth::id() === $id) {
            return back()->with('error', __('common.user_cannot_delete_self'));
        }

        try {
            $deleted = $this->userService->delete($id);

            if (! $deleted) {
                return back()->with('error', __('common.user_cannot_delete'));
            }

            return redirect()->route('users.index')->with('success', __('common.user_deleted'));
        } catch (\Throwable) {
            return back()->with('error', __('common.user_cannot_delete_has_orders'));
        }
    }
}
