<?php

namespace App\Repository;

use App\Entity\Livreur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livreur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livreur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livreur[]    findAll()
 * @method Livreur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livreur::class);
    }
    
    public function findBySearchInput($search_input){

        $query= $this->createQueryBuilder('l');

        if($search_input!=""){
            $query->where('l.nom LIKE :word')
                  ->orWhere('l.prenom LIKE :word')
                  ->orWhere('l.cin LIKE :word')
                  ->setParameter('word', '%'.$search_input.'%');
        }

        return $query->orderBy('l.id', 'DESC')
                        ->getQuery()
                        ->getResult();

    }
    
}