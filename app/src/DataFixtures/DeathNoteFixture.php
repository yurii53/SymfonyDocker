<?php

namespace App\DataFixtures;

use App\Entity\DeathNote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DeathNoteFixture extends Fixture {

    private $faker;

    public function __construct() {

        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager) {

        for ($i = 0; $i < 10; $i++) {
            $manager->persist($this->getDeathNote());
        }
        $manager->flush();
    }

    private function getDeathNote() {

        return new DeathNote(
            $this->faker->name(),
            $this->faker->year(),
            $this->faker->city(),
            $this->faker->year(),
            $this->faker->city()
        );
    }
}