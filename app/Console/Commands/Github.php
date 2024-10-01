<?php

namespace App\Console\Commands;

use App\Http\Integrations\Github\Github as GithubIntegration;
use App\Http\Integrations\Github\Requests\GetAuthenticatedUserRequest;
use Illuminate\Console\Command;
use Saloon\Http\Auth\TokenAuthenticator;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class Github extends Command
{
    protected $signature = 'github';
    protected $description = 'Fetch authenticated user info from GitHub';

    private $sections = [
        'Basic Info' => ['login', 'name', 'email', 'bio', 'company', 'location', 'blog', 'twitter_username'],
        'GitHub Stats' => ['public_repos', 'public_gists', 'followers', 'following', 'created_at', 'updated_at'],
        'Account Details' => ['id', 'node_id', 'type', 'site_admin', 'hireable', 'two_factor_authentication'],
    ];

    public function handle()
    {
        $token = config('services.github.token');

        if (! $token) {
            error('GitHub token is not set in the config.');

            return 1;
        }

        $github = new GithubIntegration;
        $github->authenticate(new TokenAuthenticator($token));

        info('Fetching user data from GitHub...');

        $userData = spin(function () use ($github) {
            $request = new GetAuthenticatedUserRequest;
            $response = $github->send($request);

            if (! $response->successful()) {
                error('Failed to fetch user data. Status: ' . $response->status());
                error('Response: ' . $response->body());
                exit(1);
            }

            return $response->json();
        }, 'Fetching data');

        info('User data fetched successfully!');

        while (true) {
            $choice = select(
                'What would you like to do?',
                ['View all data', 'View specific section', 'Search fields', 'Exit']
            );

            if ($choice === 'View all data') {
                $this->displayAllSections($userData);
            } elseif ($choice === 'View specific section') {
                $section = select('Choose a section to view:', array_keys($this->sections));
                $this->displaySection($section, $userData);
            } elseif ($choice === 'Search fields') {
                $field = search('Enter a field name', array_keys($userData));
                if (isset($userData[$field])) {
                    info("{$field}: " . $this->formatValue($userData[$field]));
                } else {
                    error("Field '{$field}' not found.");
                }
            } else {
                break;
            }
        }
    }

    private function displayAllSections($userData)
    {
        foreach ($this->sections as $sectionName => $fields) {
            $this->displaySection($sectionName, $userData);
        }
    }

    private function displaySection($sectionName, $userData)
    {
        note($sectionName);
        $data = [];
        foreach ($this->sections[$sectionName] as $field) {
            if (isset($userData[$field])) {
                $data[] = [$field, $this->formatValue($userData[$field])];
            }
        }
        table(['Field', 'Value'], $data);
    }

    private function formatValue($value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        } elseif (is_null($value)) {
            return 'N/A';
        } elseif (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }

        return (string) $value;
    }
}
