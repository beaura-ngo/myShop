<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findProductInSameCategory(Product $product) : array {
        $entityManager = $this->getEntityManager();
 
        $query = $entityManager->createQuery(
            'SELECT p 
            FROM App\Entity\Product p
            WHERE p.category = :category
            AND p.id != :idToExclude
            ORDER BY p.id ASC
            '
        )->setParameter('category', $product->getCategory())
        ->setParameter('idToExclude', $product->getId())
        ->setMaxResults(4);
 
        return $query->getResult();
    }

   /**
    * @return Product[] Returns an array of Product objects
    */
   public function findProductByCategory($value): array
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.category = :val')
           ->setParameter('val', $value)
           ->orderBy('p.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
