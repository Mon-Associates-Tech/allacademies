<?php

namespace App\Livewire\Common;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class PdfReader extends Component
{
    public $showPdfReader = false;
    public $pdfUrl = '';
    public $title = '';
    public $currentPage = 1;

    protected $listeners = [
        'openPdfReader',
        'closePdfReader',
        'saveReadingProgress'
    ];

    public function mount($url = '', $title = 'PDF Document', $currentPage = 1)
    {
        $this->pdfUrl = $url;
        $this->title = $title;
        $this->currentPage = $currentPage;

        if (!empty($url)) {
            $this->showPdfReader = true;
        }
    }

    public function openPdfReader($pdfUrl, $title = 'PDF Document', $currentPage = 1)
    {
        Log::info('PdfReader: openPdfReader called', [
            'pdfUrl' => $pdfUrl,
            'title' => $title,
            'currentPage' => $currentPage
        ]);

        $this->pdfUrl = $pdfUrl;
        $this->title = $title;
        $this->currentPage = $currentPage;
        $this->showPdfReader = true;

        // Dispatch event to trigger Alpine.js PDF reader
        $this->dispatch('pdf-reader-open', [
            'pdfUrl' => $pdfUrl,
            'title' => $title,
            'currentPage' => $currentPage
        ]);

        // Also dispatch a JavaScript event for better compatibility
        $this->js("
            window.currentPdfUrl = '{$pdfUrl}';
            window.dispatchEvent(new CustomEvent('pdf-reader-open', {
                detail: {
                    pdfUrl: '{$pdfUrl}',
                    title: '{$title}',
                    currentPage: {$currentPage}
                }
            }));
        ");
    }

    public function closePdfReader()
    {
        Log::info('PdfReader: closePdfReader called');

        $this->showPdfReader = false;
        $this->pdfUrl = '';
        $this->title = '';
        $this->currentPage = 1;
    }

    public function saveReadingProgress($page, $totalPages)
    {
        Log::info('PdfReader: saveReadingProgress called', [
            'page' => $page,
            'totalPages' => $totalPages
        ]);

        $this->currentPage = $page;

        // You can extend this to save progress to database
        // For example, if you have a Book model with reading progress
        // $this->emit('updateReadingProgress', ['page' => $page, 'totalPages' => $totalPages]);
    }

    public function render()
    {
        return view('livewire.common.pdf-reader');
    }
}
