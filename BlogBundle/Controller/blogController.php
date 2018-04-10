<?php

namespace BlogBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Garderie;
use BlogBundle\Entity\test;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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


}
