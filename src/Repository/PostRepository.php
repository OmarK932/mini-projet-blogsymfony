<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Recherche des posts par mot-clé (titre ou contenu)
     */
    public function search(string $term): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.title LIKE :term')
            ->orWhere('p.content LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les posts triés par date
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
