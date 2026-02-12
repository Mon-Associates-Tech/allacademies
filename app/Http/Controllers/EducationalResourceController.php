<?php

namespace App\Http\Controllers;

use App\Models\EducationalResource;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EducationalResourceController extends Controller
{
    /**
     * Display the resource center index page.
     */
    public function index()
    {
        return view('educational-resources.index');
    }

    /**
     * Display the upload form page.
     */
    public function create()
    {
        return view('educational-resources.create');
    }

    /**
     * Display the edit form page.
     */
    public function edit(EducationalResource $educationalResource)
    {
        $user = auth()->user();

        if (! $this->canEdit($user, $educationalResource)) {
            abort(403, 'You do not have permission to edit this resource.');
        }

        return view('educational-resources.edit', [
            'resource' => $educationalResource,
        ]);
    }

    /**
     * Check if user can edit the resource.
     */
    protected function canEdit($user, EducationalResource $resource): bool
    {
        if (! $user) {
            return false;
        }

        // Administrators, owners, and super-admins can edit any resource
        if ($user->hasAnyRole(['admin', 'owner', 'super-admin'])) {
            return true;
        }

        // Teachers and moderators can edit their own resources
        if ($user->hasAnyRole(['teacher', 'moderator'])) {
            return $resource->uploaded_by === $user->id;
        }

        return false;
    }

    /**
     * Display a specific resource.
     */
    public function show(EducationalResource $educationalResource)
    {
        $user = auth()->user();

        if (! $educationalResource->canBeAccessedBy($user)) {
            abort(403, 'You do not have permission to view this resource.');
        }

        $educationalResource->incrementViewCount();

        return view('educational-resources.show', [
            'resource' => $educationalResource,
        ]);
    }

    /**
     * Download a resource file.
     */
    public function download(EducationalResource $educationalResource)
    {
        $user = auth()->user();

        if (! $educationalResource->canBeAccessedBy($user)) {
            abort(403, 'You do not have permission to download this resource.');
        }

        if (! Storage::disk('public')->exists($educationalResource->file_path)) {
            abort(404, 'File not found.');
        }

        $educationalResource->incrementDownloadCount();

        return Storage::disk('public')->download(
            $educationalResource->file_path,
            $educationalResource->file_name
        );
    }

    /**
     * Stream a resource file (for videos and PDFs).
     */
    public function stream(EducationalResource $educationalResource): StreamedResponse
    {
        $user = auth()->user();

        if (! $educationalResource->canBeAccessedBy($user)) {
            abort(403, 'You do not have permission to view this resource.');
        }

        if (! Storage::disk('public')->exists($educationalResource->file_path)) {
            abort(404, 'File not found.');
        }

        $educationalResource->incrementViewCount();

        return Storage::disk('public')->response($educationalResource->file_path, $educationalResource->file_name, [
            'Content-Type' => $educationalResource->file_type,
        ]);
    }
}
