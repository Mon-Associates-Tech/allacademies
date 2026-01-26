<?php

namespace App\Interfaces;

/**
 * Interface for models that have media content
 */
interface HasMediaContent
{
    public function media();

    public function getTitle();

    public function getDescription();

    public function hasVideoContent();

    public function hasAudioContent();

    public function hasChapterContent();
}
