<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeRepositoryTest extends KernelTestCase
{
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @test
     */
    public function findOneByRecipe_RequestRecipeByRecipeName_RecipeReturned(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'test@test.com']);

        $recipe = $this->entityManager
            ->getRepository(Recipe::class)
            ->findOneByRecipe($user, 'Pizza');

        $this->assertSame('Pizza', $recipe->getName());
        $this->assertSame('Make a salami pizza with cheese', $recipe->getDescription());
        $this->assertSame('Fast food', $recipe->getType()->getName());
        $this->assertSame('test@test.com', $recipe->getUser()->getEmail());
    }
    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
