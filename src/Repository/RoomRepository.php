<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 *
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function save(Room $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Room $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Room[] Returns an array of Customer objects
     */
    public function FindRoom($formatDay): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT r.id, r.name, s.slot, COALESCE(s.class, NULL) AS class, NULL AS reason, NULL AS status , NULL AS fullname, NULL AS MSSV
        FROM room r 
        LEFT JOIN schedule s ON r.id = s.room_id AND s.date = :date
        WHERE r.id in (SELECT id FROM room WHERE id >= 1 and id <=22) 
        UNION 
        SELECT r.id, r.name, rv.slot, NULL AS class, COALESCE(rv.reason, NULL) AS reason, COALESCE(rv.status, NULL) AS status,  u.fullname, u.mssv_cb
        FROM room r 
        LEFT JOIN reservation rv ON rv.room_id = r.id AND rv.date = :date
        LEFT JOIN user u ON rv.user_id = u.id
        WHERE r.id IN (SELECT id FROM room WHERE id >= 1 and id <=22) 
        ORDER BY name;";
        $re = $conn->executeQuery($sql,['date'=>$formatDay]);
        return $re->fetchAllAssociative();
    }

    
    //    /**
    //     * @return Room[] Returns an array of Room objects
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

    //    public function findOneBySomeField($value): ?Room
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
