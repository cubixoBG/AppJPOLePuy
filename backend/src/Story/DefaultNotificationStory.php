<?php

namespace App\Story;

use App\Entity\Notification;
use App\Factory\NotificationFactory;
use Zenstruck\Foundry\Story;

final class DefaultNotificationStory extends Story
{
    public function build(): void
    {
        NotificationFactory::createMany(10);
    }
}
