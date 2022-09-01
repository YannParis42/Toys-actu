<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\News;
use App\Models\News\NewsSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 *
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

        public function search(NewsSearch $newsSearch): array
        {
        $qb = $this->createQueryBuilder('n')
            ->leftJoin('n.categories', 'c')
            ->addSelect('n, c')
            ->orderBy('n.releaseDate', 'desc');

            if($newsSearch->getCategory()){
                $qb->andWhere('c.id = :category')
                ->setParameter('category', $newsSearch->getCategory());
            }

            if($newsSearch->getUser()){
                $qb->join ('n.createdBy', 'u')
                ->andWhere('u.id = :user')
                ->setParameter('user', $newsSearch->getUser());
            }

            if($newsSearch->getKeyword()){
                $qb->andWhere('n.title LIKE :search')
                ->setParameter('search', '%'.$newsSearch->getKeyword().'%') ;
            }

            return $qb
            ->getQuery()
            ->getResult()
        ;
    }
    // pas touche
    public function add(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    // pas touche
    public function remove(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countNews(): int
    {
        return $this->createQueryBuilder('n')
        ->select('count(n)')
        ->getQuery()
        ->getResult((Query::HYDRATE_SINGLE_SCALAR))
    ;
    }

//    public function findOneBySomeField($value): ?News
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
