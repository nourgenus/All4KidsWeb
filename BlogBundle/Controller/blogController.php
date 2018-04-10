<?php

namespace BlogBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Garderie;
use BlogBundle\Entity\test;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class blogController extends Controller
{

    public function afficherbAction()
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->findAll();
        return $this->render('BlogBundle:Blog:afficherblog.html.twig', array('articles' => $article));

    }

    public function afficherarticleAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $Article = $em->getRepository(Article::class)->find($id);
        $Article->setVue($Article->getVue()+1);
        $em->flush();
        return $this->render('BlogBundle:Blog:afficherblogarticle.html.twig', array('articles' => $Article));
    }


    public function afficherblogtrierAction()
    {
        $em=$this->getDoctrine()->getManager();
        $article=$em->getRepository(test::class)->findTrierDQL();
        for ( $i=0 ; $i<2 ; $i++)
        {
            $a[$i]=$article[$i];
        }
        return $this->render('BlogBundle:Blog:afficherblogtrier.html.twig', array('articles'=>$a));
    }

    public function afficherquizsAction()
    {

        return $this->render('BlogBundle:Blog:quiz.html.twig', array());
    }
    public function afficherquiz1Action()
    {

        return $this->render('BlogBundle:Blog:quiz1.html.twig', array());
    }
    public function affichergarderieAction()
    {
        $em = $this->getDoctrine()->getManager();
        $garderie = $em->getRepository(Garderie::class)->findAll();
        return $this->render('BlogBundle:Blog:affichergarderie.html.twig', array('garderies' => $garderie));

    }

    public function affichergarderie1Action($id)
    {
        $em = $this->getDoctrine()->getManager();
        $garderie = $em->getRepository(Garderie::class)->find($id);
        return $this->render('BlogBundle:Blog:affichergarderie1.html.twig', array('garderies' => $garderie));
    }


    public function CalculMontantAction($id, $nbr){
        $em = $this->getDoctrine()->getManager();
        $garderie = $em->getRepository(Garderie::class)->find($id);
        //$request->request->get('nbre');
        $oldtarif=$garderie->getPrix();

        if ($nbr==1)
        {
            $newtarif=$oldtarif;
        }
        if ($nbr==2)
        {
            $newtarif=$oldtarif+$oldtarif*0.75;
        }
        if (($nbr)==3)
        {
            $newtarif=2*$oldtarif+$oldtarif*0.5;
        }
        if (($nbr)>3)
        {
            $newtarif=3*$oldtarif+$oldtarif*0.10;
        }

        // return $this->render('EducationBundle:Garderie:garderie.html.twig', array('garderie'=>$garderie,'newtarif'=>$newtarif,'f'=>$form->createView()));
        return new JsonResponse(array('newtarif' => $newtarif));
    }




}
