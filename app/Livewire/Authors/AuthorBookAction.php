<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use Illuminate\Support\Facades\Storage;

class AuthorBookAction extends AppComponent
{
    public function deleteBook($bookToDelete): void
    {
        if ($bookToDelete) {
            $bookTitle = $bookToDelete->title;
            $bookId = $bookToDelete->id;
            $coverImagePath = $bookToDelete->cover_image_path;
            $pdfPath = $bookToDelete->pdf_file_path;

            // Delete associated files
            if ($bookToDelete->cover_image_path) {
                Storage::disk('public')->delete($bookToDelete->cover_image_path);
            }
            if ($bookToDelete->pdf_file_path) {
                Storage::disk('public')->delete($bookToDelete->pdf_file_path);
            }

            $bookToDelete->delete();

            // Log activity
            $bookToDelete->logActivity('delete', 'Book Deleted', 'book', [
                'book_title' => $bookTitle,
                'book_id' => $bookId,
                'files_deleted' => [
                    'cover_image' => (bool) $coverImagePath,
                    'pdf_file' => (bool) $pdfPath,
                ],
                'deleted_by' => auth()->user()?->name ?? 'Unknown',
            ]);

            session()->flash('success', 'Book deleted successfully!');
        }
        session()->flash('success', 'Book not removed');
    }
}
