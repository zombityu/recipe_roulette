<?php

namespace App\Tests\Unit\Service;

use App\DTO\RecipeRequestDTO;
use App\DTO\RecipeResponseDTO;
use App\Entity\Recipe;
use App\Entity\RecipeType;
use App\Entity\User;
use App\Repository\RecipeRepository;
use App\Repository\RecipeTypeRepository;
use App\Repository\UserRepository;
use App\Service\RecipeService;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class RecipeServiceTest extends TestCase
{
    private RecipeRequestDTO|MockObject $dtoMock;
    private RecipeTypeRepository|MockObject $recipeTypeRepoMock;
    private UserRepository|MockObject $userRepoMock;
    private RecipeRepository|MockObject $recipeRepositoryMock;
    private UserInterface|MockObject $userInterfaceMock;
    private RecipeService $service;
    public function setUp(): void
    {
        parent::setUp();

        $this->recipeTypeRepoMock = $this->createMock(RecipeTypeRepository::class);

        $this->userRepoMock = $this->createMock(UserRepository::class);

        $this->recipeRepositoryMock = $this->createMock(RecipeRepository::class);

        $this->userInterfaceMock = $this->createMock(UserInterface::class);

        $this->dtoMock = $this->createMock(RecipeRequestDTO::class);

        $this->service = new RecipeService(
            $this->recipeRepositoryMock,
            $this->recipeTypeRepoMock,
            $this->userRepoMock
        );
    }

    /**
     * @test
     */
    public function save_SaveRecipeWithValidData_OK(): void
    {
        $email = 'test@test.com';
        $recipeName = 'receipt';
        $recipeTypeId = 1;

        $user = new User();
        $user = $user->setEmail($email);

        $this->dtoMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn($recipeName);

        $this->recipeRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => $recipeName])
            ->willReturn(null);

        $this->dtoMock
            ->expects($this->once())
            ->method('getTypeId')
            ->willReturn($recipeTypeId);

        $this->recipeTypeRepoMock
            ->expects($this->once())
            ->method('find')
            ->with($recipeTypeId)
            ->willReturn(new RecipeType());

        $this->userInterfaceMock
            ->method('getUserIdentifier')
            ->willReturn($email);

        $this->userRepoMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $email])
            ->willReturn($user);

        $this->service->save($this->dtoMock, $this->userInterfaceMock);
    }

    /**
     * @test
     */
    public function save_SaveWithAlreadyExistReceipt_ExceptionReturned(): void
    {

        $recipeName = 'receipt';

        $this->dtoMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn($recipeName);

        $this->recipeRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => $recipeName])
            ->willReturn(new Recipe());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Recipe already exist!');

        $this->service->save($this->dtoMock, $this->userInterfaceMock);
    }

    /**
     * @test
     */
    public function save_SaveWithoutRecipeType_ExceptionReturned(): void
    {
        $recipeTypeId = 1;

        $this->dtoMock
            ->expects($this->once())
            ->method('getTypeId')
            ->willReturn($recipeTypeId);

        $this->recipeTypeRepoMock
            ->expects($this->once())
            ->method('find')
            ->with($recipeTypeId)
            ->willReturn(null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Recipe Type does not exist!');

        $this->service->save($this->dtoMock, $this->userInterfaceMock);
    }

    /**
     * @test
     */
    public function getAllRecipes_RequestAllRecipes_AllDataReturned(): void
    {
        $this->recipeRepositoryMock
            ->expects($this->once())
            ->method('findAllRecipe')
            ->with($this->userInterfaceMock)
            ->willReturn([
                $this->createRecipe(),
            ]);

        $expected = $this->service->getAllRecipes($this->userInterfaceMock);

        $this->assertEquals(
            [$this->createRecipeResponseDTO()],
            $expected
        );
    }

    /**
     * @test
     */
    public function getRecipe_RequestOneSpecifiedRecipe_RequestedRecipeReturned(): void
    {
        $user = $this->userInterfaceMock;
        $recipeName = 'Test';

        $this->recipeRepositoryMock
            ->expects($this->once())
            ->method('findOneByRecipe')
            ->with($user, $recipeName)
            ->willReturn($this->createRecipe());

        $actual = $this->service->getRecipe($user, $recipeName);

        $this->assertEquals($this->createRecipeResponseDTO(), $actual);
    }

    /**
     * @test
     */
    public function getRecipe_RequestADoesNotExistRecipe_ExceptionReturned(): void
    {
        $user = $this->userInterfaceMock;
        $recipeName = 'Test';

        $this->recipeRepositoryMock
            ->expects($this->once())
            ->method('findOneByRecipe')
            ->with($user, $recipeName)
            ->willReturn(null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This recipe does not exist!');

        $this->service->getRecipe($user, $recipeName);
    }

    private function createRecipeResponseDTO(): RecipeResponseDTO
    {
        return new RecipeResponseDTO(
            'Test',
            'Test',
            'Test',
            'Meat'
        );
    }

    private function createRecipe(): Recipe
    {
        $type = new RecipeType();
        $type->setName('Meat');

        $recipe = new Recipe();
        $recipe->setName('Test');
        $recipe->setDescription('Test');
        $recipe->setPhoto('Test');
        $recipe->setType($type);
        return $recipe;
    }
}
