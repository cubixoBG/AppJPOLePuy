<?php

namespace App\Story;

use App\Factory\CourFactory;
use Zenstruck\Foundry\Story;

final class DefaultCourStory extends Story
{
    public function build(): void
    {
        CourFactory::createMany(100);
    }
}
