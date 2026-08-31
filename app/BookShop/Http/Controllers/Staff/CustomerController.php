<?php

namespace App\BookShop\Http\Controllers\Staff;

use App\BookShop\Http\Controllers\Controller;
use App\BookShop\Models\Customer;
use App\BookShop\Models\Staff;
use App\BookShop\Notifications\StaffMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        $customers = Customer::query()
            ->withCount('orders')
            ->visibleTo($staff)
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('bookshop::staff.customers.index', compact('customers', 'staff'));
    }

    public function sendEmail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_ids' => ['required', 'array', 'min:1'],
            'customer_ids.*' => ['integer', 'exists:bookshop_customers,id'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        /** @var Staff $staff */
        $staff = Auth::guard('bookshop_staff')->user();

        // Re-scoped server-side rather than trusting the submitted IDs
        // outright - the index page only ever shows a branch admin their
        // own visible() customers, but the form itself could be tampered
        // with to include IDs outside that scope.
        $customers = Customer::query()
            ->whereIn('id', $data['customer_ids'])
            ->visibleTo($staff)
            ->get();

        if ($customers->isEmpty()) {
            return back()->withErrors(['customer_ids' => 'No valid customers selected.'])->withInput();
        }

        Notification::send($customers, new StaffMessageNotification($staff, $data['subject'], $data['message']));

        return back()->with('status', "Email sent to {$customers->count()} customer(s).");
    }
}
