<?php

namespace App\Services\GitHub;

use App\Models\GitHub\{Owner, Repository, Language, Commit, Issue};
use JordanPartridge\GithubClient\Github;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncService
{
    protected $githubClient;

    public function __construct(Github $githubClient)
    {
        $this->githubClient = $githubClient;
    }

    /**
     * Synchronize an owner's information from GitHub
     *
     * @param array $ownerData Raw GitHub owner data
     * @return Owner Synchronized owner model
     */
    public function syncOwner(array $ownerData): Owner
    {
        return Owner::updateOrCreate(
            ['github_id' => $ownerData['id']],
            [
                'login' => $ownerData['login'],
                'type' => $ownerData['type'] ?? 'User',
                'name' => $ownerData['name'] ?? null,
                'avatar_url' => $ownerData['avatar_url'] ?? null,
                'html_url' => $ownerData['html_url'] ?? null,
                'company' => $ownerData['company'] ?? null,
                'blog' => $ownerData['blog'] ?? null,
                'location' => $ownerData['location'] ?? null,
                'email' => $ownerData['email'] ?? null,
                'bio' => $ownerData['bio'] ?? null,
                'public_repos' => $ownerData['public_repos'] ?? 0,
                'public_gists' => $ownerData['public_gists'] ?? 0,
                'followers' => $ownerData['followers'] ?? 0,
                'following' => $ownerData['following'] ?? 0
            ]
        );
    }

    /**
     * Synchronize a repository's information from GitHub
     *
     * @param array $repoData Raw GitHub repository data
     * @return Repository Synchronized repository model
     */
    public function syncRepository(array $repoData): Repository
    {
        // Sync owner first
        $owner = $this->syncOwner($repoData['owner']);

        // Sync language if exists
        $language = $repoData['language'] 
            ? Language::firstOrCreate(['name' => $repoData['language']])
            : null;

        return Repository::updateOrCreate(
            ['github_id' => $repoData['id']],
            [
                'owner_id' => $owner->id,
                'language_id' => $language ? $language->id : null,
                'name' => $repoData['name'],
                'full_name' => $repoData['full_name'],
                'description' => $repoData['description'] ?? null,
                'private' => $repoData['private'] ?? false,
                'fork' => $repoData['fork'] ?? false,
                'url' => $repoData['url'] ?? null,
                'html_url' => $repoData['html_url'] ?? null,
                'clone_url' => $repoData['clone_url'] ?? null,
                'ssh_url' => $repoData['ssh_url'] ?? null,
                'homepage' => $repoData['homepage'] ?? null,
                'size' => $repoData['size'] ?? 0,
                'stars_count' => $repoData['stargazers_count'] ?? 0,
                'watchers_count' => $repoData['watchers_count'] ?? 0,
                'forks_count' => $repoData['forks_count'] ?? 0,
                'open_issues_count' => $repoData['open_issues_count'] ?? 0,
                'default_branch' => $repoData['default_branch'] ?? null,
                'topics' => $repoData['topics'] ?? [],
                'archived' => $repoData['archived'] ?? false,
                'disabled' => $repoData['disabled'] ?? false,
                'visibility' => $repoData['visibility'] ?? null
            ]
        );
    }

    /**
     * Synchronize commits for a specific repository
     *
     * @param Repository $repository Repository to sync commits for
     * @param array $commitData Raw GitHub commit data
     * @return void
     */
    public function syncCommits(Repository $repository, array $commitData): void
    {
        foreach ($commitData as $commitInfo) {
            $author = $commitInfo['author'] 
                ? $this->syncOwner($commitInfo['author']) 
                : null;
            $committer = $commitInfo['committer']
                ? $this->syncOwner($commitInfo['committer'])
                : null;

            Commit::updateOrCreate(
                ['sha' => $commitInfo['sha']],
                [
                    'repo_id' => $repository->id,
                    'message' => $commitInfo['commit']['message'] ?? null,
                    'author_id' => $author ? $author->id : null,
                    'committer_id' => $committer ? $committer->id : null,
                    'tree_sha' => $commitInfo['commit']['tree']['sha'] ?? null,
                    'parent_shas' => array_column($commitInfo['parents'] ?? [], 'sha'),
                    'html_url' => $commitInfo['html_url'] ?? null,
                    'verified' => $commitInfo['commit']['verification']['verified'] ?? false,
                    'additions' => $commitInfo['stats']['additions'] ?? 0,
                    'deletions' => $commitInfo['stats']['deletions'] ?? 0,
                    'total_changes' => $commitInfo['stats']['total'] ?? 0,
                    'committed_at' => Carbon::parse($commitInfo['commit']['author']['date'] ?? now())
                ]
            );
        }
    }

    /**
     * Synchronize issues for a specific repository
     *
     * @param Repository $repository Repository to sync issues for
     * @param array $issueData Raw GitHub issue data
     * @return void
     */
    public function syncIssues(Repository $repository, array $issueData): void
    {
        foreach ($issueData as $issueInfo) {
            $author = $issueInfo['user'] 
                ? $this->syncOwner($issueInfo['user']) 
                : null;

            $assigneeIds = $issueInfo['assignees'] 
                ? array_map(function($assignee) {
                    return $this->syncOwner($assignee)->id;
                }, $issueInfo['assignees']) 
                : [];

            Issue::updateOrCreate(
                ['github_id' => $issueInfo['id']],
                [
                    'repo_id' => $repository->id,
                    'number' => $issueInfo['number'],
                    'title' => $issueInfo['title'],
                    'body' => $issueInfo['body'] ?? null,
                    'state' => $issueInfo['state'],
                    'author_id' => $author ? $author->id : null,
                    'assignee_ids' => $assigneeIds,
                    'labels' => array_column($issueInfo['labels'] ?? [], 'name'),
                    'comments_count' => $issueInfo['comments'] ?? 0,
                    'closed_at' => $issueInfo['closed_at'] 
                        ? Carbon::parse($issueInfo['closed_at']) 
                        : null
                ]
            );
        }
    }

    /**
     * Perform full synchronization for a given repository
     *
     * @param string $repositoryFullName Full name of the repository (owner/repo)
     * @return Repository Synchronized repository
     */
    public function fullSync(string $repositoryFullName): Repository
    {
        DB::beginTransaction();
        try {
            // Fetch repository details
            $repoData = $this->githubClient->repos()->get($repositoryFullName);
            $repository = $this->syncRepository($repoData);

            // Fetch and sync commits
            $commits = $this->githubClient->repos()->commits()->list($repositoryFullName);
            $this->syncCommits($repository, $commits);

            // Fetch and sync issues
            $issues = $this->githubClient->repos()->issues()->list($repositoryFullName);
            $this->syncIssues($repository, $issues);

            DB::commit();
            return $repository;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('GitHub Sync Failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
