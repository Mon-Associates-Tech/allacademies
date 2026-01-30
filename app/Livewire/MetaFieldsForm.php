<?php

namespace App\Livewire;

use Livewire\Component;

class MetaFieldsForm extends Component
{
    public array $selectedOptions = [];

    public int $pagesCount = 1;

    public int $spacesCount = 1;

    public $file;

    public $image;

    public function render()
    {
        $metafields_options = [
            'page' => 'Insert Blank Page',
            'external' => 'Insert Document',
            'image' => 'Insert Image',
            'space' => 'Insert Empty Spaces',
        ];

        return view('livewire.meta-fields-form', compact('metafields_options'));
    }

    public function __construct($id = null, $pagesCount = 0, $spacesCount = 0, $file = null, $image = null)
    {
        parent::__construct($id);
        $this->pagesCount = $pagesCount;
        $this->spacesCount = $spacesCount;
        $this->file = $file;
        $this->image = $image;
    }
}
