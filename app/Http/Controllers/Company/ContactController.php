<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use App\Services\NewsletterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'newsletter' => ['in:true,false,1,0'],
        ]);
        $validated['newsletter'] = $validated['newsletter'] ?? false;

        try {

            Mail::to(config('company.email', 'allacademies2023@gmail.com'))
                ->send(new ContactFormMail($validated));

            if ($validated['newsletter']) {
                app(NewsletterService::class)->subscribe($validated['email']);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Thank you for your message! We\'ll get back to you soon.',
                ]);
            }

            return redirect()->back()->with('success', 'Thank you for your message! We\'ll get back to you soon.');

        } catch (\Exception $e) {
            \Log::error('Contact form submission failed: '.$e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'An error occurred while sending your message. Please try again.',
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'An error occurred while sending your message. Please try again.')
                ->withInput();
        }
    }
}
