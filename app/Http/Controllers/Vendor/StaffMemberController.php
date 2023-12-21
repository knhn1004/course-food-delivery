<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\RoleName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\StoreStaffMemberRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use App\Notifications\RestaurantStaffInvitation;

class StaffMemberController extends Controller
{
    public function index(): Response
    {
        $this->authorize('user.viewAny');

        return Inertia::render('Vendor/Staff/Show', [
            'staff' => auth()->user()->restaurant->staff,
        ]);
    }

    public function store(StoreStaffMemberRequest $request): RedirectResponse
    {
        $restaurant = $request->user()->restaurant;
        $attributes = $request->validated();

        $member = DB::transaction(function () use ($attributes, $restaurant) {
            $user = $restaurant->staff()->create([
                'name'     => $attributes['name'],
                'email'    => $attributes['email'],
                'password' => '',
            ]);

            $user->roles()->sync(Role::where('name', RoleName::STAFF->value)->first());
            return $user;
        });

        try {
            $member->notify(new RestaurantStaffInvitation($restaurant->name));
        } catch (\Exception $e) {
            return back()->withStatus('error: Failed to send invitation email');
        }

        return back();
    }

    public function destroy($staffMemberId)
    {
        $this->authorize('user.delete');

        $restaurant = auth()->user()->restaurant;
        $member     = $restaurant->staff()->findOrFail($staffMemberId);

        $member->roles()->sync([]);
        $member->delete();

        return back();
    }
}
