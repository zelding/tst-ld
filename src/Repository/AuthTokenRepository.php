<?php

namespace App\Repository;

use App\Entity\AuthToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuthToken>
 *
 * @method AuthToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthToken[]    findAll()
 * @method AuthToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthToken::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findActiveToken(User $user, ?string $token): ?AuthToken
    {
        $qb = $this->createQueryBuilder('a');

        $qb->select('a, u')
           ->innerJoin('a.user', 'u')
           ->where('u.username = :username')
           ->andWhere('a.valid_until > :now')
            //timezones!
           ->setParameter('now', (new \DateTimeImmutable())->format('Y-m-d H:i:s'))
           ->setParameter('username', $user->getUsername());

        if ( $token ) {
            $qb->andWhere('a.token = :token')
                ->setParameter('token', $token);
        }

        return $qb->getQuery()
                  ->getOneOrNullResult();
    }
}
