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
        $actor = Auth::user();
        $filters = [
            'search' => $request->input('search'),
            'role' => $request->input('role'),
            'region_id' => $request->input('region_id'),
        ];

        if ($actor?->isOperator()) {
            $filters['exclude_admin'] = true;
        }

        $users = $this->userService->paginate($filters, 15);
        $regions = $this->regionService->listActive();
        $roles = $actor?->isOperator()
            ? [UserRole::OPERATOR, UserRole::WASHER]
            : UserRole::cases();

        return view('users.index', [
            'users' => $users,
            'regions' => $regions,
            'roles' => $roles,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $actor = Auth::user();
        $roles = $actor?->isOperator()
            ? [UserRole::OPERATOR, UserRole::WASHER]
            : UserRole::cases();
        $regions = $this->regionService->listActive();

        return view('users.create', [
            'regions' => $regions,
            'roles' => $roles,
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

        $this->authorizeAdminUserAccess($user);

        $actor = Auth::user();
        $roles = $actor?->isOperator()
            ? [UserRole::OPERATOR, UserRole::WASHER]
            : UserRole::cases();
        $regions = $this->regionService->listActive();

        return view('users.edit', [
            'user' => $user,
            'regions' => $regions,
            'roles' => $roles,
        ]);
    }

    public function update(UserRequest $request, int $id): RedirectResponse
    {
        $user = $this->userService->getById($id);
        if (! $user) {
            return redirect()->route('users.index')->with('error', __('common.user_not_found'));
        }

        $this->authorizeAdminUserAccess($user);

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

        $user = $this->userService->getById($id);
        if (! $user) {
            return redirect()->route('users.index')->with('error', __('common.user_not_found'));
        }

        $this->authorizeAdminUserAccess($user);

        try {
            $deleted = $this->userService->delete($id);

            if (! $deleted) {
                return back()->with('error', __('common.user_cannot_delete'));
            }

            return redirect()->route('users.index')->with('success', __('common.user_deleted'));
        } catch (\Throwable) {
            return back()->with('error', __('common.user_cannot_delete'));
        }
    }

    protected function authorizeAdminUserAccess(\App\Models\User $targetUser): void
    {
        $actor = Auth::user();
        if ($actor?->isOperator() && $targetUser->isAdmin()) {
            abort(403, "Operator administrator hisobini ko'rish yoki o'zgartirish huquqiga ega emas.");
        }
    }
}
