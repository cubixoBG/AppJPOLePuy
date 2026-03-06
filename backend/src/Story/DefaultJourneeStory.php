<?php

namespace App\Story;

use App\Factory\JourneeFactory;
use Zenstruck\Foundry\Story;

final class DefaultJourneeStory extends Story
{
    public function build(): void
    {
        JourneeFactory::createMany(20);
    }
}
