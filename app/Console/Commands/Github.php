<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use JordanPartridge\GithubClient\Contracts\GithubConnectorInterface;
use JordanPartridge\GithubClient\Requests\Repos\Repos;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class Github extends Command
{
    protected $signature = 'github';

    protected $description = 'Interactively explore GitHub data';

    private GithubConnectorInterface $githubConnector;

    public function __construct(GithubConnectorInterface $githubConnector)
    {
        parent::__construct();
        $this->githubConnector = $githubConnector;
    }

    public function handle()
    {
        while (true) {
            $action = $this->selectAction();

            if ($action === 'exit') {
                break;
            }

            $this->performAction($action);
        }

        info('Thanks for exploring GitHub!');
    }

    private function selectAction()
    {
        return select(
            'What would you like to do?',
            [
                'user_info' => 'View User Information',
                'list_repos' => 'List Repositories',
                'recent_commits' => 'View Recent Commits',
                'exit' => 'Exit',
            ]
        );
    }

    private function performAction($action)
    {
        switch ($action) {
            case 'user_info':
                $this->displayUserInfo();
                break;
            case 'list_repos':
                $this->listRepositories();
                break;
            case 'recent_commits':
                $this->viewRecentCommits();
                break;
        }
    }

    private function displayUserInfo()
    {
        $userData = $this->fetchData('user');
        if (! $userData) {
            return;
        }

        info('User Information:');
        table(
            ['Field', 'Value'],
            [
                ['Name', $userData['name'] ?? 'N/A'],
                ['Username', $userData['login']],
                ['Email', $userData['email'] ?? 'N/A'],
                ['Company', $userData['company'] ?? 'N/A'],
                ['Location', $userData['location'] ?? 'N/A'],
                ['Public Repos', $userData['public_repos']],
                ['Followers', $userData['followers']],
                ['Following', $userData['following']],
            ]
        );
    }

    private function listRepositories()
    {
        $personalOnly = confirm('Do you want to see only personal repositories?', true);

        $request = new Repos;
        $request->query()->merge([
            'sort' => 'updated',
            'direction' => 'desc',
            'per_page' => 100,
        ]);

        if ($personalOnly) {
            $request->query()->add('type', 'owner');
        }

        $repos = $this->fetchData($request);
        if (! $repos) {
            return;
        }

        $filteredRepos = array_filter($repos, function ($repo) use ($personalOnly) {
            return ! $personalOnly;
        });

        info($personalOnly ? 'Your Personal Repositories:' : 'Your Repositories (including organizations):');
        $repoData = array_map(function ($repo) {
            return [
                $repo['name'],
                $repo['description'] ?? 'No description',
                $repo['stargazers_count'].' stars',
                $repo['language'] ?? 'N/A',
                $repo['owner']['login'],
            ];
        }, array_slice($filteredRepos, 0, 10));

        table(['Name', 'Description', 'Stars', 'Language', 'Owner'], $repoData);

        $totalCount = count($filteredRepos);
        if ($totalCount > 10) {
            info("Showing first 10 repositories. You have {$totalCount} in total.");
        }
    }

    private function viewRecentCommits()
    {
        $request = new ListRequest;
        $request->query()->add('type', 'owner');
        $repos = $this->fetchData($request);
        if (! $repos) {
            return;
        }

        $repoName = select(
            'Select a repository to view recent commits:',
            collect($repos)->pluck('name')->toArray()
        );

        // Assuming you have a GetCommitsRequest class, or you can create one
        $commits = $this->fetchData("repos/{$this->githubConnector->getAuthenticatedUsername()}/{$repoName}/commits");
        if (! $commits) {
            return;
        }

        info("Recent commits for {$repoName}:");
        $commitData = array_map(function ($commit) {
            return [
                substr($commit['sha'], 0, 7),
                $this->truncate($commit['commit']['message'], 50),
                $commit['commit']['author']['name'],
                $commit['commit']['author']['date'],
            ];
        }, array_slice($commits, 0, 10));

        table(['SHA', 'Message', 'Author', 'Date'], $commitData);
    }

    private function fetchData($request)
    {
        return spin(function () use ($request) {
            $response = $this->githubConnector->send($request);
            if (! $response->successful()) {
                error('Failed to fetch data. Status: '.$response->status());

                return null;
            }

            return $response->json();
        }, 'Fetching data from GitHub');
    }

    private function truncate($string, $length)
    {
        return strlen($string) > $length ? substr($string, 0, $length - 3).'...' : $string;
    }
}
