<?php

namespace EvenementBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Commentaire;
use AppBundle\Entity\Evenement;
use AppBundle\Entity\Likes;
use AppBundle\Entity\Parc;
use AppBundle\Entity\Parents;
use AppBundle\Entity\Participation;
use AppBundle\Entity\Vote;
use Doctrine\ORM\EntityRepository;
use EvenementBundle\Form\EvenementForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;

class EvenementController extends Controller
{
    public function ListerAction(Request $request)
    {
        $em=$this->getDoctrine();
        $modele=$em->getRepository("AppBundle:Evenement")->findAll();
        $populaire=$em->getRepository("AppBundle:Evenement")->getPopulaire();
        $recent=$em->getRepository("AppBundle:Evenement")->getRecent();


        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator=$this->get('knp_paginator');
        $result=$paginator->paginate(
            $modele,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',3)

        );



        return $this->render('@Evenement/Evenement/lister.html.twig', array("eve"=>$result,"pop"=>$populaire,"rec"=>$recent

        ));
    }

    public function ParticiperAction(Request $request,$id)
    {
        $em=$this->getDoctrine();
        $event=$em->getRepository('AppBundle:Evenement')->find($id);

        $event->setNbpart($event->getNbpart()-1);
        $part= new Participation();
        $part->setIdParent($this->getUser());
        $part->setIdvenement($event);

        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView(
                '@Evenement/Evenement/pdf.html.twig',

                    ['name' => $event->getNom(),'datedeb'=>$event->getDateDeb(),'datefin'=>$event->getDateFin(),'nameparent'=>$this->getUser()]

            ),
            'C:/wamp/www/pidevv/web/pdf/'.$this->getUser().'-'.$event->getId().'.pdf'
        );
        $em = $this->getDoctrine()->getManager();
        $em->persist($part);
        $em->persist($event);
        $em->flush();
        return $this->redirectToRoute('_consulter',array('id'=>$id));

    }

    public function ConsulterEvenementAction($id)
    {
        $em=$this->getDoctrine();
        $event=$em->getRepository("AppBundle:Evenement")->find($id);
        $likes=$em->getRepository('AppBundle:Evenement')->geNbLikes($id);
        $event->setNbVue($event->getNbVue()+1);
        $event->setNbLikes($likes);
        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();
        $avis=$em->getRepository('AppBundle:Evenement')->getAvis($id);
        $lie=$em->getRepository("AppBundle:Evenement")->getLie($event->getCategorie());
        $verifpart=$em->getRepository('AppBundle:Evenement')->getParticipation($id);
        $tot=$em->getRepository('AppBundle:Evenement')->geTotalAvis($id);
        $sum=$em->getRepository('AppBundle:Evenement')->geSumAvis($id);
        $comm=$em->getRepository('AppBundle:Evenement')->geNbCommentaire($id);
        $comment=$em->getRepository('AppBundle:Evenement')->getCommentaire($id);
        $usr=$this->getUser();
        $l=$em->getRepository('AppBundle:Evenement')->getLikes($usr);

        $lp=$em->getRepository('AppBundle:Evenement')->getLikesParent($usr);



        $a=(float) $tot;
        $b=(float) $sum;




        return $this->render('EvenementBundle:Evenement:consulter.html.twig', array("event"=>$event,"lie"=>$lie,"avis"=>$avis,"total"=>$a,"sum"=>$b,"comm"=>$comm,"like"=>$likes,"l"=>$l,"lp"=>$lp,"co"=>$comment,"part"=>$verifpart

        ));
    }
    public function AjoutRatingAction(Request $request,$id,$ip)
    {

            $em = $this->getDoctrine();
            $usr = $em->getRepository("AppBundle:User")->find($ip);
            $event = $em->getRepository("AppBundle:Evenement")->find($id);
            $vote = new Vote();

            $vote->setEventId($event);
            $vote->setParentId($usr);

            $vote->setRating((integer) $request->get('rate'));
            $em = $this->getDoctrine()->getManager();

            $em->persist($vote);
            $em->flush();


            return $this->redirectToRoute('_consulter',array('id'=>$id));

    }
    public function AjouterLikesAction(Request $request,$id,$ip)
    {

        $em = $this->getDoctrine();
        $usr = $em->getRepository("AppBundle:User")->find($ip);
        $event = $em->getRepository("AppBundle:Evenement")->find($id);
        $like = new Likes();

        $like->setIdevenement($event);
        $like->setIdparent($usr);

        $em = $this->getDoctrine()->getManager();

        $em->persist($like);
        $em->flush();


        return $this->redirectToRoute('_consulter',array('id'=>$id));

    }
    public function AjouterDisLikesAction(Request $request,$id,$ip)
    {

        $em = $this->getDoctrine();
        $like=$em->getRepository('AppBundle:Evenement')->getLikesParent($ip,$id);
        var_dump($like);

        $em = $this->getDoctrine()->getManager();

        $em->remove($like[0]);
        $em->flush();


        return $this->redirectToRoute('_consulter',array('id'=>$id));

    }

    public function AjouterCommentaireAction(Request $request,$id,$ip)
    {

        $em = $this->getDoctrine();
        $event=$em->getRepository('AppBundle:Evenement')->find($id);
        $usr=$em->getRepository('AppBundle:User')->find($ip);
        $com=new Commentaire();
        $com->setIdEvenement($event);
        $com->setIdParents($usr);
        $com->setCommentaire($request->get('message'));


        $em = $this->getDoctrine()->getManager();

        $em->persist($com);
        $em->flush();


        return $this->redirectToRoute('_consulter',array('id'=>$id));

    }

    public function affichereventAction()
    {
        $em=$this->getDoctrine()->getManager();
        $event=$em->getRepository(Evenement::class)->findAll();
        return $this->render('EvenementBundle:Evenement:afficherevent.html.twig', array('events'=>$event));
    }

    public function supprimereventAction($id){
        var_dump($id);
        $event = new Evenement();
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('AppBundle:Evenement')->find($id);
        $em->remove($event);
        $em->flush();
        return  $this->redirectToRoute('afficherevent');
    }


    public function ajoutereventAction(Request $request)
    {
        $event=new Evenement();
        if($request->isMethod('POST'))
        {
            $em=$this->getDoctrine()->getManager();
            $event->setNom($request->get('nom'));

            $event->setCategorie($request->get('categorie'));
            $event->setDescription($request->get('description'));
            $event->setPrix($request->get('prix'));

            $event->setAdresse($request->get('adresse'));

            $event->setOrganisateur($request->get('organisateur'));

            $event->setEmail($request->get('email'));
            $v=$request->get('date_deb');
            $v2=$request->get('date_fin');

            $datedeb=\DateTime::createFromFormat('Y-m-d:H:i',str_replace("T",":",$v));
            $datefin=\DateTime::createFromFormat('Y-m-d:H:i',str_replace("T",":",$v2));

            $event->setDateDeb($datedeb);

            $event->setDateFin($datefin);



            $em->persist($event);
            $em->flush();


            $file = $request->files->get('imgevent');
            var_dump($file);
            $file->move(
                $this->getParameter('event_images_directory'),
                $event->getNom()."_".$event->getId().".png"
            );



            return $this->redirectToRoute('afficherarticle');

        }
        return $this->render('EvenementBundle:Evenement:ajouterevent.html.twig', array(
            "events"=>$event
        ));
    }


    public function afficherparcAction()
    {
        $em=$this->getDoctrine()->getManager();
        $parc=$em->getRepository(Parc::class)->findAll();
        return $this->render('EvenementBundle:Evenement:afficherparc.html.twig', array('parcs'=>$parc));
    }


    public function supprimerparcAction($id){var_dump($id);
        $parc = new Evenement();
        $em = $this->getDoctrine()->getManager();
        $parc = $em->getRepository(Parc::class)->find($id);
        $em->remove($parc);
        $em->flush();
        return  $this->redirectToRoute('afficherparc');
    }

    public function ajouterparcAction(Request $request)
    {
        $parc=new Parc();
        if($request->isMethod('POST'))
        {
            $em=$this->getDoctrine()->getManager();
            $parc->setNom($request->get('nom'));

            $parc->setDecription($request->get('description'));
            $parc->setAdresse($request->get('adresse'));

            $em->persist($parc);
            $em->flush();


            $file = $request->files->get('imgparc');

            $file->move(
                $this->getParameter('event_images_directory'),
                $parc->getNom()."_".$parc->getId().".png"
            );



            return $this->redirectToRoute('afficherparc');

        }
        return $this->render('EvenementBundle:Evenement:ajouterparc.html.twig', array(
            "parcs"=>$parc
        ));



    }
    public function modifierparcAction(Request $request,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $parc = $this->getDoctrine()->getRepository(Parc::class)->find($id);
        if($request->isMethod('POST'))
        {

            $parc->setNom($request->request->get('nom'));
            $parc->setDecription($request->request->get('description'));
            $parc->setAdresse($request->request->get('adresse'));

            $em->persist($parc);
            $em->flush();
            return $this->redirectToRoute('afficherparc');
        }


        return $this->render('EvenementBundle:Evenement:modifierparc.html.twig',array('parcs'=>$parc));
    }
    public function afficherparcsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $parc = $em->getRepository(Parc::class)->findAll();
        return $this->render('EvenementBundle:Evenement:afficherparcs.html.twig', array('parcs' => $parc));

    }

    public function modifiereventAction(Request $request,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $event = $this->getDoctrine()->getRepository(Evenement::class)->find($id);
        if($request->isMethod('POST'))
        {

            $event->setNom($request->request->get('nom'));
            $event->setDescription($request->request->get('description'));
            $event->setAdresse($request->request->get('adresse'));
            $event->setPrix($request->request->get('prix'));

            $v=$request->get('datedeb');
            $v2=$request->get('datefin');


            $datedeb=\DateTime::createFromFormat('Y-m-d:H:i',str_replace("T",":",$v));
            $datefin=\DateTime::createFromFormat('Y-m-d:H:i',str_replace("T",":",$v2));

            $event->setDateDeb($datedeb);

            $event->setDateFin($datefin);
            $event->setOrganisateur($request->request->get('organisateur'));



            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('afficherevent');
        }


        return $this->render('EvenementBundle:Evenement:modifierevent.html.twig',array('events'=>$event));
    }

}
