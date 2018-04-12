<?php

namespace BlogBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Garderie;
use BlogBundle\Entity\Quiz;
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

    public function affichergarderieAction()
    {
        $em = $this->getDoctrine()->getManager();
        $garderie = $em->getRepository(Garderie::class)->findAll();
        return $this->render('BlogBundle:Blog:affichergarderie.html.twig', array('garderies' => $garderie));

    }

    public function affichergarderie1Action(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $garderie = $em->getRepository(Garderie::class)->find($id);

        return $this->render('BlogBundle:Blog:affichergarderie1.html.twig', array('garderies' => $garderie));
    }






    public function affichercourAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cour = $em->getRepository(Cours::class)->findAll();
        return $this->render('BlogBundle:Blog:affichercour.html.twig', array('cours' => $cour));

    }
    public function afficherquizAction()
    {

        return $this->render('BlogBundle:Blog:quiz1.html.twig', array());
    }
    public function afficherquiz2Action()
    {

        return $this->render('BlogBundle:Blog:quiz2.html.twig', array());
    }
    public function afficherquiz3Action()
    {

        return $this->render('BlogBundle:Blog:quiz3.html.twig', array());
    }
    public function scorequizAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $quiz = $em->getRepository(Quiz::class)->find($id);
        $quiz->setNbr($quiz->getNbr()+1);
        $em->flush();

        var_dump($id);
        return $this->render('BlogBundle:Blog:scorequiz.html.twig', array());
    }

}
