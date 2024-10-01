<?php

namespace App\States;

use Thunk\Verbs\State;

class ProjectState extends State
{
    /**
     * @var string The name of the project.
     */
    public string $name;

    /**
     * @var string The slug of the project.
     */
    public string $slug;

    /**
     * @var string The description of the project.
     */
    public ?string $description = null;
}
