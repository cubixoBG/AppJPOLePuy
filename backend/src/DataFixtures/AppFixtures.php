<?php

namespace App\DataFixtures;

use App\Story\DefaultAvisStory;
use App\Story\DefaultCourStory;
use App\Story\DefaultDepartementStory;
use App\Story\DefaultEdtStory;
use App\Story\DefaultIndiceStory;
use App\Story\DefaultJourneeStory;
use App\Story\DefaultNotificationStory;
use App\Story\DefaultUserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Story\DefaultContactStory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        DefaultDepartementStory::load();
        DefaultContactStory::load();
        DefaultUserStory::load();
        DefaultCourStory::load();
        DefaultEdtStory::load();
        DefaultJourneeStory::load();
        DefaultAvisStory::load();
        DefaultNotificationStory::load();
        DefaultIndiceStory::load();
    }
}
