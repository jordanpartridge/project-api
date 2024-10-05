<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

class ApiTokenForUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        select('user', User::pluck('name', 'id')->toArray());
        $user = User::find(1);
        $token = $user->createToken('api-token')->plainTextToken;
        $this->info('Token: ' . $token);
    }
}
