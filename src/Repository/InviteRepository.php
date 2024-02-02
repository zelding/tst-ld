<?php

namespace App\Repository;

use App\Entity\Invite;
use App\Entity\User;
use App\Model\InviteStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invite>
 *
 * @method Invite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invite[]    findAll()
 * @method Invite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invite::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findExistingBetween(User $user, User $otherUser): ?Invite
    {
        $qb = $this->createQueryBuilder('i');

        $qb->select('i')
            ->innerJoin('i.inviter', 'inviter')
            ->innerJoin('i.invitee', 'invitee')
            //->where('i.status <> :deleted')
            ->andWhere('i.status <> :blocked')
            ->andWhere($qb->expr()->in('inviter.username', [$user->getUserIdentifier(), $otherUser->getUserIdentifier()]))
            ->andWhere($qb->expr()->in('invitee.username', [$user->getUserIdentifier(), $otherUser->getUserIdentifier()]))
            //->setParameter('deleted', InviteStatus::DELETED)
            ->setParameter('blocked', InviteStatus::BLOCKED);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByHash(string $hash): ?Invite
    {
        $qb = $this->createQueryBuilder('i');

        $qb->where('i.hash = :hash')
            ->andWhere('i.status <> :deleted')
            ->setParameter('deleted', InviteStatus::DELETED)
            ->setParameter('hash', $hash);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
