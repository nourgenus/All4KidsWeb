<?php

namespace BlogBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Garderie;
use BlogBundle\Entity\Quiz;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class backblogController extends Controller
{


    public function ajouterarticleAction(Request $request)
    {
        $article=new Article();
        if($request->isMethod('POST'))
        {
            $em=$this->getDoctrine()->getManager();
            $article->setTitre($request->get('titre'));

            $article->setCategorie($request->get('categorie'));
            $article->setContenu($request->get('contenu'));

            $em->persist($article);
            $em->flush();


            $file = $request->files->get('imgblog');
            var_dump($file);
            $file->move(
                $this->getParameter('blog_images_directory'),
                $article->getTitre()."_".$article->getId().".png"
            );



            return $this->redirectToRoute('afficherarticle');

        }
        return $this->render('BlogBundle:backblog:ajouterarticle.html.twig', array(
            "articles"=>$article
        ));
    }

    public function afficherarticleAction()
    {
        $em=$this->getDoctrine()->getManager();
        $article=$em->getRepository(Article::class)->findAll();
        return $this->render('BlogBundle:backblog:afficherarticle.html.twig', array('articles'=>$article));
    }


    public function supprimerarticleAction($id){
        $article = new Article();
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($id);
        $em->remove($article);
        $em->flush();
        return  $this->redirectToRoute('afficherarticle');
    }

    public function modifierarticleAction(Request $request,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        if($request->isMethod('POST'))
        {

            $article->setTitre($request->request->get('titre'));
            $article->setCategorie($request->request->get('categorie'));
            $article->setContenu($request->request->get('contenu'));

            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('afficherarticle');
        }


        return $this->render('BlogBundle:backblog:modifierarticle.html.twig',array("articles"=>$article));
    }

   public function DashboardAction()
    {
        $number= array('');
        $quizs= array('');
        $quizzs = $this->getDoctrine()->getRepository(Quiz::class)->findAll();

        foreach ($quizzs as $quiz)
        {
            echo $quiz->getNom();
            array_push( $quizs ,$quiz->getNom());
        }
        foreach ($quizzs as $quiz)
        {
            echo $quiz->getNbr();
            array_push( $number , $quiz->getNbr());
        }

        $sellsHistory = array(
            array(
                "name" => "Nombre",
                "data" => $number
            ),


        );
        $ob = new Highchart();
        // ID de l'élement de DOM que vous utilisez comme conteneur
        $ob->chart->renderTo('barchart');
        $ob->title->text('Nombre de quizz jouée');
        $ob->chart->type('column');

        $ob->yAxis->title(array('text' => "Bénéfices (millions d'euros)"));

        $ob->xAxis->title(array('text' => "Date du jours"));
        $ob->xAxis->categories($quizs);

        $ob->series($sellsHistory);


        return $this->render('BlogBundle:backblog:statquiz.html.twig', array(
            'barchart' => $ob
        ));

    }
    public function affichergarderieAction()
    {
        $em=$this->getDoctrine()->getManager();
        $garderie=$em->getRepository(Garderie::class)->findAll();
        return $this->render('BlogBundle:backblog:affichergarderie.html.twig', array('garderies'=>$garderie));
    }


    public function ajoutergarderieAction(Request $request)
    {
        $garderie=new Garderie();
        if($request->isMethod('POST'))
        {
            $em=$this->getDoctrine()->getManager();
            $garderie->setNom($request->get('nom'));

            $garderie->setDescription($request->get('description'));
            $garderie->setAdresse($request->get('adresse'));

            $em->persist($garderie);
            $em->flush();


            $file = $request->files->get('imggard');
            var_dump($file);
            $file->move(
                $this->getParameter('blog_images_directory'),
                $garderie->getNom()."_".$garderie->getId().".png"
            );



            return $this->redirectToRoute('affichergarderie');

        }
        return $this->render('BlogBundle:backblog:ajoutergarderie.html.twig', array(
            "garderies"=>$garderie
        ));
    }

    public function supprimergarderieAction($id){
        $garderie = new Garderie();
        $em = $this->getDoctrine()->getManager();
        $garderie = $em->getRepository(Garderie::class)->find($id);
        $em->remove($garderie);
        $em->flush();
        return  $this->redirectToRoute('affichergarderie');
    }
    public function modifiergarderieAction(Request $request,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $garderie = $this->getDoctrine()->getRepository(Garderie::class)->find($id);
        if($request->isMethod('POST'))
        {

            $garderie->setNom($request->request->get('nom'));
            $garderie->setDescription($request->request->get('description'));
            $garderie->setAdresse($request->request->get('adresse'));

            $em->persist($garderie);
            $em->flush();
            return $this->redirectToRoute('affichergarderie');
        }


        return $this->render('BlogBundle:backblog:modifiergarderie.html.twig',array("garderies"=>$garderie));
    }


    public function ajoutercourAction(Request $request)
    {
        $cour=new Cours();
        if($request->isMethod('POST'))
        {
            $em=$this->getDoctrine()->getManager();
            $cour->setTitre($request->get('titre'));

            $cour->setClasse($request->get('classe'));
            $cour->setCategorie($request->get('categorie'));


            $em->persist($cour);
            $em->flush();


            $file = $request->files->get('pdf');
            var_dump($file);
            $file->move(
                $this->getParameter('pdf_directory'),
                $cour->getTitre()."_".$cour->getId().".pdf"
            );


        }
        return $this->render('BlogBundle:backblog:ajoutercour.html.twig', array(
            "cours"=>$cour
        ));
    }

}
