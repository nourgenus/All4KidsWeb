<?php

namespace BabysitterBundle\Controller;

use AppBundle\Entity\Babysitter;
use AppBundle\Entity\Parents;
use AppBundle\Entity\Plannification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BabysitterBundle:Default:index.html.twig');
    }

    public function adminBabysitterListAction()
    {
        $em = $this->getDoctrine()->getRepository(Babysitter::class)->findAll();
        return $this->render('@Babysitter/admin_liste_babysitter.html.twig', array('babysitters' =>$em));
    }
    public function fullInfoAction($id)
    {
        $babysitter = new Babysitter();
        $repo = $this->getDoctrine()->getRepository(Babysitter::class);
        $babysitter = $repo->find($id);
        return $this->render('BabysitterBundle::full_info.html.twig', array('babysitters' =>$babysitter));
    }
    public function desactiverAction($id)
    {
        $babysitter = new Babysitter();
        $repo = $this->getDoctrine()->getRepository(Babysitter::class);
        $babysitter = $repo->find($id);
        $babysitter->setEnabled(0);
        $em=$this->getDoctrine()->getManager();
        $em->persist($babysitter);
        $em->flush();
        return $this->redirectToRoute('admin_babysitter_list');
    }
    public function activerAction($id)
    {
        $babysitter = new Babysitter();
        $repo = $this->getDoctrine()->getRepository(Babysitter::class);
        $babysitter = $repo->find($id);
        $babysitter->setEnabled(1);
        $em=$this->getDoctrine()->getManager();
        $em->persist($babysitter);
        $em->flush();
        return $this->redirectToRoute('admin_babysitter_list');
    }
    public function babysitterNearToParentAction($id)
    {
        $parents = new Parents();
        $repo = $this->getDoctrine()->getRepository(Parents::class);
        $parents = $repo->find($id);
        $em = $this->getDoctrine()->getRepository("AppBundle:Babysitter")->findBy(array('adresse'=>$parents->getAdresse(),'enabled'=>1));
        return $this->render('@Babysitter/babysitter_near_to_parent.html.twig', array('babysitters' =>$em));
    }
    public function parentBabysitterListAction()
    {
        $em = $this->getDoctrine()->getRepository(Babysitter::class)->findBy(array('enabled'=>1));
        return $this->render('@Babysitter/parent_liste_babysitter.html.twig', array('babysitters' =>$em));
    }
    public function parentBabysitterSelectioneAction($id){
        $babysitter=new Babysitter();
        $repo=$this->getDoctrine()->getRepository(Babysitter::class);
        $babysitter = $repo->find($id);
        return $this->render('@Babysitter/Parent_babysitter_selectione.html.twig', array('babysitters' =>$babysitter
        ));
    }
    public function reserverBabysitterAction(Request $request)
    {
        if($request->isMethod('Post')) {
            $date = date_create(date($request->get('datesouhaite')));
            $em = $this->getDoctrine()->getRepository(Plannification::class)->findBy(array('date'=>$date,'status'=>1));
            $babysitters=$this->getDoctrine()->getRepository("AppBundle:Babysitter")->findBy(array('enabled'=>1));
            $babysitter = array();
            foreach ($babysitters as $baby){
                $disp=true;
                foreach ($em as $plan){
                    if ($plan->getBabysitter()->getId() == $baby->getId()){
                        $disp=false;
                    }
                }
                if ($disp==true){
                    array_push($babysitter,$baby);
                }
                $disp=true;
            }
            return $this->render('@Babysitter/reserver_babysitter.html.twig', array('babysitters' =>$babysitter,'d'=>$date));
        }
        $date=date_create(date("Y-m-d H:i:s"));
        $em = $this->getDoctrine()->getRepository(Babysitter::class)->findBy(array('enabled'=>1));
        return $this->render('@Babysitter/reserver_babysitter.html.twig', array('babysitters' =>$em,'d'=>$date));

    }
    public function adminAjouterBabysitterAction(Request $request)
    {
        if($request->isMethod('Post')) {
            $babysitter = new Babysitter();
            $babysitter->setEmail($request->get('email'));
            $babysitter->setUsername($request->get('username'));
            $salt = substr(str_replace('+', '.', base64_encode(md5(mt_rand(), true))), 0, 16);
            $rounds = 10000;
            $babysitter->setPassword(crypt($request->get('password'), sprintf('$6$rounds=%d$%s$', $rounds, $salt)));
            $babysitter->setNom($request->get('nom'));
            $babysitter->setPrenom($request->get('prenom'));
            $date = date_create(date($request->get('dn')));
            $babysitter->setDateNaissance($date);
            $babysitter->setCin($request->get('cin'));
            $babysitter->setTel($request->get('tel'));
            $babysitter->setAdresse($request->get('adresse'));
            $babysitter->setSalaire($request->get('salaire'));
            $babysitter->addRole('ROLE_BABYSITTER');
            $em = $this->getDoctrine()->getManager();
            $em->persist($babysitter);
            $em->flush();

            return $this->render('@Babysitter/admin_ajouter_babysitter.html.twig', array('success' =>1));
        }
        return $this->render('@Babysitter/admin_ajouter_babysitter.html.twig', array('success' =>0));
    }
    public function plannifierBabysitterAction($baby,$id,$date){
        $repo=$this->getDoctrine()->getRepository(Babysitter::class);
        $babysitter = $repo->find($baby);
        $parents=$this->getDoctrine()->getRepository(Parents::class)->find($id);
        $plannification=new Plannification();
        $plannification->setStatus(0);
        $plannification->setBabysitter($babysitter);
        $plannification->setParents($parents);
        $date = date_create(date($date));
        $plannification->setDate($date);
        $em = $this->getDoctrine()->getManager();
        $em->persist($plannification);
        $em->flush();
        return $this->render('@User/Default/dashboard_parent.html.twig');
    }
    public function parentReservationsAction($id){
        $parents=$this->getDoctrine()->getRepository(Parents::class)->find($id);
        $plannifications = $this->getDoctrine()->getRepository(Plannification::class)->findBy(array('parents'=>$parents));
        return $this->render('@Babysitter/parent_reservations.html.twig',array('plannifications' =>$plannifications));
    }
    public function babysitterDemandesAction($id){
        $babysitter=$this->getDoctrine()->getRepository(Babysitter::class)->find($id);
        $demandes = $this->getDoctrine()->getRepository(Plannification::class)->findBy(array('babysitter'=>$babysitter));
        return $this->render('@Babysitter/babysitter_demandes.html.twig',array('demandes' =>$demandes));
    }
    public function babysitterAnnulerDemandesAction($id){
        $demande=$this->getDoctrine()->getRepository(Plannification::class)->find($id);
        $demande->setStatus(-1);
        $em=$this->getDoctrine()->getManager();
        $em->persist($demande);
        $em->flush();
        $demandes = $this->getDoctrine()->getRepository(Plannification::class)->findBy(array('babysitter'=>$demande->getBabysitter()));
        return $this->render('@Babysitter/babysitter_demandes.html.twig',array('demandes' =>$demandes));
    }
    public function babysitterConfirmerDemandesAction($id){
        $demande=$this->getDoctrine()->getRepository(Plannification::class)->find($id);
        $demande->setStatus(1);
        $em=$this->getDoctrine()->getManager();
        $em->persist($demande);
        $em->flush();
        $demandes = $this->getDoctrine()->getRepository(Plannification::class)->findBy(array('babysitter'=>$demande->getBabysitter()));
        return $this->render('@Babysitter/babysitter_demandes.html.twig',array('demandes' =>$demandes));
    }



    }
