<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class QuestionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($index = 1; $index <= 10; ++$index) {
            $manager->persist(
                (new Question())
                    ->setUsername($faker->userName())
                    ->setContent($faker->paragraph(1))
            );
        }

        $manager->flush();
    }
}
