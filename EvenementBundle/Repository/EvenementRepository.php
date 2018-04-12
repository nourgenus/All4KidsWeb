<?php
/**
 * Created by PhpStorm.
 * User: Abdennadher Mohamed
 * Date: 08/04/2018
 * Time: 01:21
 */

namespace EvenementBundle\Repository;
use Doctrine\ORM\EntityRepository;

class EvenementRepository extends \Doctrine\ORM\EntityRepository
{
    public function getPopulaire(){
        $q=$this->getEntityManager()
            ->createQuery('SELECT e FROM AppBundle:Evenement e ORDER BY e.nb_vue DESC ')->setMaxResults(5);

        return $q->getResult();


    }
    public function getRecent(){
        $q=$this->getEntityManager()
            ->createQuery('SELECT e FROM AppBundle:Evenement e ORDER BY e.date_deb ASC')->setMaxResults(5);

        return $q->getResult();


    }
    public function geTotalAvis($cat){
        $q=$this->getEntityManager()
            ->createQuery('SELECT AVG(e.rating)  FROM AppBundle:Vote e WHERE e.eventId=:cat')
            ->setParameter(':cat',$cat);

        return $q->getSingleScalarResult();



    }
    public function geSumAvis($cat){
        $q=$this->getEntityManager()
            ->createQuery('SELECT SUM(e.rating)  FROM AppBundle:Vote e WHERE e.eventId=:cat')
            ->setParameter(':cat',$cat);

        return $q->getSingleScalarResult();



    }

    public function geNbCommentaire($cat){
        $q=$this->getEntityManager()
            ->createQuery('SELECT COUNT(e.id)  FROM AppBundle:Commentaire e WHERE e.idEvenement=:cat')
            ->setParameter(':cat',$cat);

        return $q->getSingleScalarResult();



    }
    public function geNbLikes($cat){
        $q=$this->getEntityManager()
            ->createQuery('SELECT COUNT(e.id)  FROM AppBundle:Likes e WHERE e.idevenement=:cat')
            ->setParameter(':cat',$cat);

        return $q->getSingleScalarResult();
        }
    public function getLie($cat){
        $q=$this->getEntityManager()
            ->createQuery('SELECT e  FROM AppBundle:Evenement e WHERE e.categorie=:cat')
            ->setParameter(':cat',$cat);

        return $q->getResult();


    }
    public function getAvis($cat){
        $q=$this->getEntityManager()
            ->createQuery('SELECT e  FROM AppBundle:Vote e WHERE e.eventId=:cat')
            ->setParameter(':cat',$cat);

        return $q->getResult();


    }

    public function getLikesParent($id){
    $q=$this->getEntityManager()
        ->createQuery('SELECT e  FROM AppBundle:Likes e WHERE e.idparent=:id')


        ->setParameter(':id',$id);

    return $q->getResult();


}
    public function getCommentaire($id){
        $q=$this->getEntityManager()
            ->createQuery('SELECT e  FROM AppBundle:Commentaire e WHERE e.idEvenement=:id')


            ->setParameter(':id',$id);

        return $q->getResult();


    }
    public function getParticipation($id){
        $q=$this->getEntityManager()
            ->createQuery('SELECT e  FROM AppBundle:Participation e WHERE e.idvenement=:id')


            ->setParameter(':id',$id);

        return $q->getResult();


    }
    public function getLikes($cat){
        $q=$this->getEntityManager()
            ->createQuery('SELECT e  FROM AppBundle:Likes e WHERE e.idparent=:cat')
            ->setParameter(':cat',$cat);

        return $q->getResult();


    }


}