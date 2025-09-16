<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReadingProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookProgressController extends Controller
{
    /**
     * Update the reading progress for a book
     */
    public function updateProgress(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'current_page' => 'required|integer|min:1',
            'total_pages' => 'required|integer|min:1',
            'chapter_id' => 'nullable|integer',
            'notes' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $book = Book::findOrFail($request->book_id);

        try {
            $progressPercentage = round(($request->current_page / $request->total_pages) * 100, 2);

            $progress = BookReadingProgress::updateOrCreate(
                [
                    'book_id' => $request->book_id,
                    'user_id' => $user->id
                ],
                [
                    'current_page' => $request->current_page,
                    'total_pages' => $request->total_pages,
                    'progress_percentage' => $progressPercentage,
                    'chapter_id' => $request->chapter_id,
                    'notes' => $request->notes,
                    'last_read_at' => now()
                ]
            );

            // Log the reading activity
            if (class_exists(\Spatie\Activitylog\ActivityLogger::class)) {
                activity()
                    ->performedOn($book)
                    ->causedBy($user)
                    ->withProperties([
                        'action' => 'reading_progress_updated',
                        'current_page' => $request->current_page,
                        'total_pages' => $request->total_pages,
                        'progress_percentage' => $progressPercentage,
                        'chapter_id' => $request->chapter_id
                    ])
                    ->log('Reading progress updated');
            }

            return response()->json([
                'success' => true,
                'progress' => $progress,
                'progress_percentage' => $progressPercentage,
                'message' => 'Reading progress updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update reading progress', [
                'user_id' => $user->id,
                'book_id' => $request->book_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update reading progress'
            ], 500);
        }
    }

    /**
     * Get the reading progress for a specific book
     */
    public function getProgress(Book $book)
    {
        $user = Auth::user();

        $progress = BookReadingProgress::where('book_id', $book->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$progress) {
            return response()->json([
                'progress' => null,
                'progress_percentage' => 0,
                'message' => 'No reading progress found'
            ]);
        }

        $progressPercentage = $progress->total_pages > 0
            ? round(($progress->current_page / $progress->total_pages) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'progress' => [
                'id' => $progress->id,
                'book_id' => $progress->book_id,
                'user_id' => $progress->user_id,
                'current_page' => $progress->current_page,
                'total_pages' => $progress->total_pages,
                'progress_percentage' => $progressPercentage,
                'chapter_id' => $progress->chapter_id,
                'notes' => $progress->notes,
                'last_read_at' => $progress->last_read_at?->toISOString(),
                'created_at' => $progress->created_at?->toISOString(),
                'updated_at' => $progress->updated_at?->toISOString()
            ],
            'progress_percentage' => $progressPercentage
        ]);
    }

    /**
     * Get all reading progress for the authenticated user
     */
    public function getUserProgress(Request $request)
    {
        $user = Auth::user();

        $query = BookReadingProgress::where('user_id', $user->id)
            ->with(['book:id,title,author,cover_image,total_pages'])
            ->orderBy('last_read_at', 'desc');

        // Filter by status if provided
        if ($request->has('status')) {
            switch ($request->status) {
                case 'reading':
                    $query->where('progress_percentage', '<', 100);
                    break;
                case 'completed':
                    $query->where('progress_percentage', '>=', 100);
                    break;
                case 'started':
                    $query->where('current_page', '>', 1);
                    break;
            }
        }

        $progress = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $progress
        ]);
    }

    /**
     * Mark a book as completed
     */
    public function markCompleted(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        $user = Auth::user();
        $book = Book::findOrFail($request->book_id);

        try {
            $progress = BookReadingProgress::updateOrCreate(
                [
                    'book_id' => $request->book_id,
                    'user_id' => $user->id
                ],
                [
                    'current_page' => $book->total_pages ?? 1,
                    'total_pages' => $book->total_pages ?? 1,
                    'progress_percentage' => 100,
                    'last_read_at' => now(),
                    'completed_at' => now()
                ]
            );

            // Log completion activity
            if (class_exists(\Spatie\Activitylog\ActivityLogger::class)) {
                activity()
                    ->performedOn($book)
                    ->causedBy($user)
                    ->withProperties([
                        'action' => 'book_completed',
                        'progress_percentage' => 100
                    ])
                    ->log('Book completed');
            }

            return response()->json([
                'success' => true,
                'progress' => $progress,
                'message' => 'Book marked as completed'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark book as completed', [
                'user_id' => $user->id,
                'book_id' => $request->book_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark book as completed'
            ], 500);
        }
    }

    /**
     * Delete reading progress for a book
     */
    public function deleteProgress(Book $book)
    {
        $user = Auth::user();

        try {
            $progress = BookReadingProgress::where('book_id', $book->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$progress) {
                return response()->json([
                    'success' => false,
                    'message' => 'No reading progress found to delete'
                ], 404);
            }

            $progress->delete();

            return response()->json([
                'success' => true,
                'message' => 'Reading progress deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete reading progress', [
                'user_id' => $user->id,
                'book_id' => $book->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete reading progress'
            ], 500);
        }
    }
}
