<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreateGithubIssue extends Command
{
    protected $signature = 'github:create-issue';

    protected $description = 'Create a GitHub issue for AI documentation improvements, because even AI needs a bit of human touch';

    public function handle()
    {
        $issueData = [
            'title' => 'Enhance AI-Generated Model Documentation: From Meh to Marvelous',
            'body' => $this->getIssueBody(),
            'labels' => ['enhancement', 'documentation', 'AI', 'please-make-it-better'],
        ];

        // Using Laravel's HTTP facade for simplicity, but you can replace this with any HTTP client you prefer
        $response = Http::withToken(config('services.github.token'))
            ->post('https://api.github.com/repos/jordanpartridge/project-api/issues', $issueData);

        if ($response->successful()) {
            $this->info('✅ Issue created! Now, let\'s watch the magic unfold...');
            $this->info('URL: ' . $response->json()['html_url']);
        } else {
            $this->error('Failed to create issue: ' . $response->status() . '. Maybe the universe is telling us to use carrier pigeons?');
            $this->error($response->json()['message'] ?? 'Unknown error - blame the matrix');
        }
    }

    private function getIssueBody(): string
    {
        // The getIssueBody method remains unchanged since it's just generating the content
        return <<<'EOT'
## Overview
Our AI-generated model documentation is about as exciting as watching paint dry. Time for some upgrades!

## Current Limitations
- **Prompt Structure**: Simpler than a "Hello World" program
- **Generation**: One pass? That's like painting with one brush stroke.
- **Business Context**: As vague as a politician's promises
- **Examples**: Could be more engaging than a tax form
- **Accuracy Check**: What accuracy check?

## Proposed Improvements

### 1. Enhanced Prompt Engineering
```php
$enhancedPrompt = <<<EOT
Analyze and document the {$modelName} model with flair:

1. **Core Purpose & Business Context**:
   - Why does this model exist? (Hint: Not just for show)
   - Workflows it powers
   - Integration points

2. **Technical Implementation**:
   - Why did we choose this schema? (Was it a coin toss?)
   - Performance - because time is money
   - Validation - no garbage in, no garbage out
   - Common pitfalls - so we can avoid them

3. **Usage Patterns**:
   - Real-world use cases (Think beyond "it works on my machine")
   - Query optimization - because slow is the new no
   - Transaction handling - because life's transactions are messy
   - Event patterns - not just for Christmas

4. **Testing & Quality**:
   - Test scenarios - because we love edge cases
   - Data integrity - keep the data honest
   - Edge cases - where the bugs love to hide
EOT;
    }
}
