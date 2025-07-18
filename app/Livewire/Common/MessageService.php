<?php

namespace App\Livewire\Common;

class MessageService
{
    public static function success($message, $autoHide = null)
    {
        session()->flash('success', $message);
        if ($autoHide !== null) {
            session()->flash('success_auto_hide', $autoHide);
        }
    }

    public static function error($message, $autoHide = null)
    {
        session()->flash('error', $message);
        if ($autoHide !== null) {
            session()->flash('error_auto_hide', $autoHide);
        }
    }

    public static function warning($message, $autoHide = null)
    {
        session()->flash('warning', $message);
        if ($autoHide !== null) {
            session()->flash('warning_auto_hide', $autoHide);
        }
    }

    public static function info($message, $autoHide = null)
    {
        session()->flash('info', $message);
        if ($autoHide !== null) {
            session()->flash('info_auto_hide', $autoHide);
        }
    }
}
