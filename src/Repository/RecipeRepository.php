<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function save(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByRecipe(UserInterface $user, string $recipe): ?Recipe
    {
        return $this->createQueryBuilder('r')
            ->select('r')
            ->join('r.user', 'u')
            ->join('r.type', 't')
            ->where('u.email = :email')
            ->andWhere('r.name = :recipe')
            ->setParameter('email', $user->getUserIdentifier())
            ->setParameter('recipe', $recipe)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array{Recipe}
     */
    public function findAllRecipe(UserInterface $user): array
    {
        return $this->createQueryBuilder('r')
            ->select('r')
            ->join('r.user', 'u')
            ->join('r.type', 't')
            ->where('u.email = :email')
            ->setParameter('email', $user->getUserIdentifier())
            ->getQuery()
            ->getResult();
    }
}
