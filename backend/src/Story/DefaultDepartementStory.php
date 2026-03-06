<?php

namespace App\Story;

use App\Factory\DepartementFactory;
use Zenstruck\Foundry\Story;

final class DefaultDepartementStory extends Story
{
    public function build(): void
    {
        DepartementFactory::createMany(5);
    }
}
