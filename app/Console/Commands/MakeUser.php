<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use function Laravel\Prompts\text;
use function Laravel\Prompts\table;
use function Laravel\Prompts\select;

class MakeUser extends Command
{
    protected $signature = 'make:user';
    protected $description = 'Create a user and generate a Sanctum key with style and pizzazz!';

    public function handle()
    {
        $this->info('🎭 Welcome to the Headless User Creator Extravaganza! 🎭');
        $this->newLine();

        $name = text(
            label: '🤔 What shall we call you, O Nameless One?',
            placeholder: 'Sir Codealot'
        );

        $email = text(
            label: '📧 What mystical address shall we use to summon you?',
            placeholder: 'merlin@camelot.com',
            default: Str::slug($name) . '@example.com'
        );

        $password = text(
            label: '🔐 Whisper your secret passphrase (don\'t worry, we won\'t tell the dragons)',
            placeholder: 'Open Sesame!',
            default: 'password'
        );

        $favoriteColor = select(
            label: '🌈 Choose your power color!',
            options: [
                'red' => 'Fiery Red (for the bold)',
                'blue' => 'Ocean Blue (for the calm)',
                'green' => 'Forest Green (for the nature lovers)',
                'purple' => 'Royal Purple (for the fabulous)',
                'rainbow' => 'Rainbow (for the indecisive)'
            ]
        );

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'favorite_color' => $favoriteColor,
        ]);

        $token = $user->createToken('api')->plainTextToken;

        $this->newLine();
        $this->info('🎉 Huzzah! Your digital alter ego has been conjured! 🎉');
        $this->newLine();

        table(['Attribute', 'Value'], [
            ['Name', $name],
            ['Email', $email],
            ['Favorite Color', $favoriteColor],
            ['Secret Identity', 'Shh... it\'s a secret!']
        ]);

        $this->newLine();
        $this->info('🔑 Your magical key to the digital realm:');
        $this->newLine();
        $this->line($token);
        $this->newLine();
        $this->info('Guard it well, for it holds great power! (And by "great power," we mean "access to your API")');
        $this->newLine();
        $this->info('May your code be bug-free and your coffee be strong! 🚀☕');
    }
}
