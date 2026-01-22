<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class UpdateUserOnlineStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-online-status';

    protected $description = 'Update user online status based on last seen activity';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::where('is_online', true)->each(function ($user) {
            if (! Cache::has('user-online-'.$user->id)) {
                $user->update(['is_online' => false]);
            }
        });
    }
}
