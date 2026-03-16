<?php

namespace App\Story;

use App\Factory\IndiceFactory;
use Zenstruck\Foundry\Story;

final class DefaultIndiceStory extends Story
{
    public function build(): void
    {
        IndiceFactory::createmany(10);
    }
}
