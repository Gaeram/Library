<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function add(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchByWord($search)
    {
        // recuperation du query builder
        // c'est unn objet qui crée
        // des requetes SQL en PHP
        $qb = $this->createQueryBuilder('book');

        // Je l'utilise pour faire un select sur la table articles
        $query = $qb->select('article')
            // je récupère les article dont le titre correspond
            // à :search
            ->where('book.title LIKE :search')
            // ici je définie la valeur de search
            // En lui diant que le mot
            // Peut contenir des caracteres avant et apres
            // Il sera quand meme trouvé
            // Je le fais en 2 etapes avec SetParameter
            // Qui permet à Doctrine de sécuriser ma
            // variable $search
            ->setParameter('search', '%'.$search.'%')
            // je récupère la requete générée
            ->getQuery();
        // Que j'exécute en BDD et y récupère les résultats
        return $query->getResult();
    }
//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
