<?php

namespace App\Story;

use App\Factory\ContactFactory;
use Zenstruck\Foundry\Story;

final class DefaultContactStory extends Story
{
    public function build(): void
    {
        ContactFactory::createMany(100);
    }
}
