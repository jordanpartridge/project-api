<?php

namespace App\Filament\Widgets;

use App\Models\Documentation;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Spatie\Activitylog\Models\Activity;

class DocumentationActivityList extends Widget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function getActivityLog()
    {
        return Activity::query()
            ->where('subject_type', Documentation::class)
            ->with('causer')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function render(): View
    {
        return view('filament.widgets.documentation-activity-list', [
            'activities' => $this->getActivityLog(),
        ]);
    }
}
