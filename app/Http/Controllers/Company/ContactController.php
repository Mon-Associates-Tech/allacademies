<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'newsletter' => ['in:true,false,1,0']
        ]);


        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        try {
            // Send email to admin

            Mail::to(config('company.email', 'allacademies2023@gmail.com'))
                ->send(new ContactFormMail($validated));

            // If newsletter subscription is requested, handle it here
            if ($validated['newsletter']) {
                // Add to newsletter subscription logic
                // This could be added to a newsletter service or database
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Thank you for your message! We\'ll get back to you soon.'
                ]);
            }

            return redirect()->back()->with('success', 'Thank you for your message! We\'ll get back to you soon.');

        } catch (\Exception $e) {
            \Log::error('Contact form submission failed: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'An error occurred while sending your message. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'An error occurred while sending your message. Please try again.')
                ->withInput();
        }
    }
}
