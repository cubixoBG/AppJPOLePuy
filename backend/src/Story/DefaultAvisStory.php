<?php

namespace App\Story;

use App\Factory\AvisFactory;
use Zenstruck\Foundry\Story;

final class DefaultAvisStory extends Story
{
    public function build(): void
    {
        AvisFactory::createMany(900);
    }
}
