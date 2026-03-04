<?php

namespace App\Story;

use App\Factory\EdtFactory;
use Zenstruck\Foundry\Story;

final class DefaultEdtStory extends Story
{
    public function build(): void
    {
        EdtFactory::createMany(100);
    }
}
