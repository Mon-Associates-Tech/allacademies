<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdministratorCollection;
use App\Http\Resources\AdministratorResource;
use App\Http\Resources\GroupBookSubscriptionResource;
use App\Models\Administrator;
use App\Models\Book;
use App\Models\GroupBookSubscription;
use App\Models\StudentGroup;
use Illuminate\Http\Request;

class AdministratorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Administrator::class, 'administrator');
    }

    public function index()
    {
        return new AdministratorCollection(Administrator::with('user')->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $administrator = Administrator::create($validated);

        return new AdministratorResource($administrator->load('user'));
    }

    public function show(Administrator $administrator)
    {
        return new AdministratorResource($administrator->load('user'));
    }

    public function update(Request $request, Administrator $administrator)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
        ]);

        $administrator->update($validated);

        return new AdministratorResource($administrator->load('user'));
    }

    public function destroy(Administrator $administrator)
    {
        $administrator->delete();

        return response()->noContent();
    }

    // Additional methods specific to administrators

    public function subscribeGroupToBook(Request $request, Administrator $administrator)
    {
        $validated = $request->validate([
            'student_group_id' => 'required|exists:student_groups,id',
            'book_id' => 'required|exists:books,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $studentGroup = StudentGroup::findOrFail($validated['student_group_id']);
        $this->authorize('groupSubscribe', $book);

        if (! $book->has_softcopy) {
            return response()->json(['message' => 'This book does not have a softcopy available for subscription'], 422);
        }

        $subscription = GroupBookSubscription::create([
            'student_group_id' => $studentGroup->id,
            'book_id' => $book->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'active',
            'subscribed_by_type' => 'App\Models\Administrator',
            'subscribed_by_id' => $administrator->id,
        ]);

        return new GroupBookSubscriptionResource($subscription->load('studentGroup', 'book'));
    }

    public function getGroupSubscriptions(Administrator $administrator)
    {
        $subscriptions = GroupBookSubscription::where('subscribed_by_type', 'App\Models\Administrator')
            ->where('subscribed_by_id', $administrator->id)
            ->with('studentGroup', 'book')
            ->paginate();

        return GroupBookSubscriptionResource::collection($subscriptions);
    }
}
