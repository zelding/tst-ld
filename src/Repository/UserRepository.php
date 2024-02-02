<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\InviteStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
* @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User|null findOneByUsername(string $username, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param User $user
     *
     * @return array{0: User}
     */
    public function getUserWithInvites(User $user): array
    {
        $qb = $this->createQueryBuilder('u');

        $qb->select('u, accept_out, accept_in, inv_in, inv_out, inu, outu')
           ->leftJoin('u.invites', 'accept_out', Join::WITH, 'accept_out.status = :accepted')
           ->leftJoin('accept_out.invitee', 'a1u')

           ->leftJoin('u.invited', 'accept_in', Join::WITH, 'accept_in.status = :accepted')
           ->leftJoin('accept_in.inviter', 'a2u')

           ->leftJoin('u.invited', 'inv_out', Join::WITH, 'inv_out.status = :pending')
           ->leftJoin('inv_out.invitee', 'inu')

           ->leftJoin('u.invites', 'inv_in', Join::WITH, 'inv_in.status = :pending')
           ->leftJoin('inv_in.inviter', 'outu')

           ->setParameter('accepted', InviteStatus::ACCEPTED)
           ->setParameter('pending', InviteStatus::SENT)

           ->andWhere('u.username = :username')
           ->setParameter('username', $user->getUsername());

        return $qb->getQuery()->getResult();
    }
}
