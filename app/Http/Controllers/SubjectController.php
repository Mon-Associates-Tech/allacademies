<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\SubjectCollection;
use App\Http\Resources\TopicResource;

class SubjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Subject::class, 'subject');
    }

    public function index()
    {
        return new SubjectCollection(Subject::withCount('topics')->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects',
            'description' => 'nullable|string',
        ]);

        $subject = Subject::create($validated);

        return new SubjectResource($subject);
    }

    public function show(Subject $subject)
    {
        return new SubjectResource($subject->load('topics'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:subjects,name,' . $subject->id,
            'description' => 'nullable|string',
        ]);

        $subject->update($validated);

        return new SubjectResource($subject);
    }

    public function destroy(Subject $subject)
    {
        // Check if subject has topics
        if ($subject->topics()->exists()) {
            return response()->json(['message' => 'Cannot delete subject that has topics assigned to it'], 422);
        }
        
        $subject->delete();

        return response()->noContent();
    }
    
    public function getTopics(Subject $subject)
    {
        $topics = $subject->topics()->paginate();
        return TopicResource::collection($topics);
    }
}