<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function save(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findReservation($day,$from,$to): array
    {
     $conn = $this->getEntityManager()->getConnection();
     $sql = '
        SELECT  r.id, `user_id`, u.mssv_cb, u.fullname, `room_id`, ro.name, `reason`, `date`, `status`, `slot`  
        FROM `reservation` r left join `user` u on r.user_id=u.id
        inner join `room` ro ON r.room_id = ro.id
        WHERE r.date >= :day AND r.room_id>=:from AND r.room_id<=:to AND r.status = 0
     ';
     $re = $conn->executeQuery($sql,['day'=>$day,'from'=>$from,'to'=>$to]);
 
     return $re->fetchAllAssociative();
    }

    public function delExpiredReservation($date): void
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'DELETE FROM `reservation` WHERE date<:date';
        $re = $conn->executeQuery($sql,['date'=>$date]);
    }

    public function findDetailOfReservation($id): array
    {
     $conn = $this->getEntityManager()->getConnection();
     $sql = '
        SELECT u.email, ro.name, r.room_id, r.slot
        FROM `reservation` r INNER JOIN user u ON r.user_id = u.id
        INNER JOIN room ro ON r.room_id = ro.id
        WHERE r.id = :id
     ';
     $re = $conn->executeQuery($sql,['id'=>$id]);
 
     return $re->fetchAllAssociative();
    }

    public function findSameOfReservationAndSchedule():array{
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT r.id, r.room_id, r.date, r.slot 
            FROM `reservation` r, schedule s 
            WHERE r.room_id = s.room_id AND r.date = s.date AND r.slot = s.slot
        ';
        $re = $conn->executeQuery($sql);

        return $re->fetchAllAssociative();
    }

    public function deleteReservation($id): void
    {
     $conn = $this->getEntityManager()->getConnection();
     $sql = 'DELETE FROM `reservation` WHERE id = :id';
     $re = $conn->executeQuery($sql,['id'=>$id]);
    }

    public function acceptReservation($id): array
    {
     $conn = $this->getEntityManager()->getConnection();
     $sql = 'UPDATE `reservation` SET `status`= 1 WHERE id= :id';
     $re = $conn->executeQuery($sql,['id'=>$id]);

     $roomid = $this->findDetailOfReservation($id)[0]['room_id'];
     $slot = $this->findDetailOfReservation($id)[0]['slot'];

     $sql = 'SELECT id FROM `reservation` WHERE `status`= 0 AND room_id= :roomid AND slot=:slot';
     $re = $conn->executeQuery($sql,['roomid'=>$roomid,'slot'=>$slot]);

     return $re->fetchAllAssociative();
    }



//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
