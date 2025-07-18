<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookSubscription;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookSubscriptionController extends Controller
{
    public function subscribe(Request $request, Book $book)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subscription_type' => 'required|in:monthly,annual'
        ]);
        
        // Verify parent has access to this student
        $this->authorizeParentAccess($request->student_id);
        
        // Check if subscription already exists
        $existingSubscription = BookSubscription::where('student_id', $request->student_id)
            ->where('book_id', $book->id)
            ->where('status', 'active')
            ->first();
            
        if ($existingSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'Student already has an active subscription to this book'
            ], 400);
        }
        
        // Create new subscription
        $subscription = BookSubscription::create([
            'student_id' => $request->student_id,
            'book_id' => $book->id,
            'subscription_type' => $request->subscription_type,
            'status' => 'pending_payment',
            'subscribed_by' => Auth::id(),
            'starts_at' => now(),
            'expires_at' => $request->subscription_type === 'annual' 
                ? now()->addYear() 
                : now()->addMonth()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription created successfully',
            'subscription' => $subscription
        ]);
    }
    
    public function cancel(Request $request, BookSubscription $subscription)
    {
        // Verify parent has access to this subscription
        $this->authorizeParentAccess($subscription->student_id);
        
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully'
        ]);
    }
    
    private function authorizeParentAccess($studentId)
    {
        $hasAccess = StudentParent::where('user_id', Auth::id())
            ->where('student_id', $studentId)
            ->exists();
            
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this student.');
        }
    }
}
