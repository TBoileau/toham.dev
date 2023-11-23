<?php

namespace App\DataFixtures;

use App\Entity\Command;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(
            (new Command())
                ->setName('!youtube')
                ->setTitle('Youtube')
                ->setMessage('https://www.youtube.com/@Toham')
        );

        $manager->persist(
            (new Command())
                ->setName('!x')
                ->setTitle('x')
                ->setMessage('https://www.x.com/toham_tech')
        );

        $manager->flush();
    }
}
