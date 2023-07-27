<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\RecipeType;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecipeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setPassword('$2y$13$Le.kN6J8cTZbWIYJqKoIGOKjT17M7K7J2uq8aYBxGJsh.YLXQtHi.');
        $user->setRoles([]);

        $type = new RecipeType();
        $type->setName('Fast food');

        $hamburger = new Recipe();
        $hamburger->setName('Hamburger');
        $hamburger->setDescription('Make a good Hamburger');
        $hamburger->setPhoto('');
        $hamburger->setType($type);
        $hamburger->setUser($user);

        $pizza = new Recipe();
        $pizza->setName('Pizza');
        $pizza->setDescription('Make a salami pizza with cheese');
        $pizza->setPhoto('');
        $pizza->setType($type);
        $pizza->setUser($user);

        $manager->persist($user);
        $manager->persist($type);
        $manager->persist($hamburger);
        $manager->persist($pizza);

        $manager->flush();
    }
}
