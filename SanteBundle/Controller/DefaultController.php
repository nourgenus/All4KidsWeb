<?php

namespace SanteBundle\Controller;
use AppBundle\Entity\Pharmacie;
use AppBundle\Entity\Vaccin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $phar = new Pharmacie();
        if ($request->isMethod('POST')) {
            $phar->setNom($request->get('nom'));
            $phar->setType($request->get('type'));
            $phar->setAdresse($request->get('loc'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($phar);
            $em->flush();

            return $this->redirectToRoute('afficher');
        }

        return $this->render('SanteBundle:Default:index.html.twig');
    }

    public function indexparentAction($id)
    {    $em = $this->getDoctrine()->getManager();
        $modele = $em->getRepository("AppBundle:Enfant")->findBy(array('parent'=>$id));
        $v =  $em->getRepository("AppBundle:Vaccin")->findAll();
        $ev =  $em->getRepository("AppBundle:EnfantVaccin")->findAll();

     $res=" ";
        foreach ($modele as $post) {
            foreach ($v as $va) {


                if ($post->getAge() ==  $va->getAge()*30-2) {
                  $res = $va->getNom()." ** ".$res ;

                }
            }


        }
        $message = \Swift_Message::newInstance()
            ->setSubject('Prochaine Vaccines')
            ->setFrom('makremzitoun242@gmail.com')
            ->setTo('makrem.zitoun@esprit.tn')
            ->setBody($res);
        $this->get('mailer')->send($message);



        return $this->render('SanteBundle:Default:indexparent.html.twig');
    }

    public function listAction()
    {
        $em = $this->getDoctrine();
        $pharmacie = $em->getRepository("AppBundle:Pharmacie")->findAll();

        return $this->render('SanteBundle:Default:allInMap.html.twig', array("phars" => $pharmacie
            // ...
        ));
    }

    public function parentchildAction($id)
    {     $em = $this->getDoctrine()->getManager();
        $modele = $em->getRepository("AppBundle:Enfant")->findBy(array('parent'=>$id));
        return $this->render('SanteBundle:Default:parentchild.html.twig',array('mods'=>$modele));


    }

    public function parentphAction()
    {     $em = $this->getDoctrine();
        $pharmacie = $em->getRepository("AppBundle:Pharmacie")->findAll();

        return $this->render('SanteBundle:Default:parentpharmacie.html.twig', array("phars" => $pharmacie));

    }

    public function prochainevAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $modele = $em->getRepository("AppBundle:Enfant")->findBy(array('parent'=>$id));
        $v =  $em->getRepository("AppBundle:Vaccin")->findAll();
        $ev =  $em->getRepository("AppBundle:EnfantVaccin")->findAll();

        return $this->render('SanteBundle:Default:prochVaccin.html.twig',array('enf'=>$modele,'vacc'=>$v ,'sat'=>$ev));

    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $modele = $em->getRepository("AppBundle:Pharmacie")->find($id);
        $em->remove($modele);

        $em->flush();
        $this->addFlash('success', 'Pharmacie Supprimée');
        return $this->redirectToRoute('all');

    }


    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $modele = $em->getRepository("AppBundle:Pharmacie")->find($id);
        if ($request->isMethod('POST')) {
            $modele->setNom($request->get('nom'));
            $modele->setType($request->get('type'));
            $modele->setAdresse($request->get('loc'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($modele);
            $em->flush();

            $this->addFlash('success', 'Pharmacie Modifier');

            return $this->redirectToRoute('all');
        }
        return $this->render('SanteBundle:Default:edit.html.twig', array('mod' => $modele));

    }

    public function allAction(Request $request)
    {           $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM AppBundle:Pharmacie a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

  return $this->render('SanteBundle:Default:afficher.html.twig', array("phars" => $pagination
            // ...
        ));
    }
    public function deletevacinAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $modele = $em->getRepository("AppBundle:Vaccin")->find($id);
        $em->remove($modele);

        $em->flush();
          $this->addFlash('success', 'Vaccin Supprimé');
        return $this->redirectToRoute('allvin');
    }


    public function editvacinAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $modele = $em->getRepository("AppBundle:Vaccin")->find($id);
        if ($request->isMethod('POST')) {
            $modele->setNom($request->get('nom'));
            $modele->setDescription($request->get('desc'));
            $modele->setAge($request->get('age'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($modele);
            $em->flush();
            $this->addFlash('success', 'Vaccin Modifier');

            return $this->redirectToRoute('allvin');
        }
        return $this->render('SanteBundle:Default:editv.html.twig', array('mod' => $modele));
    }


    public function allvacinAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM AppBundle:Vaccin a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/);
        return $this->render('SanteBundle:Default:allVaccin.html.twig', array("phars" => $pagination ));
            // ...
    }

    public function indexvacinAction(Request $request)
    {
        $v = new Vaccin();
        if ($request->isMethod('POST')) {
            $v->setNom($request->get('nom'));
            $v->setDescription($request->get('desc'));
            $v->setAge($request->get('age'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($v);
            $em->flush();
            $this->addFlash('success', 'Vaccin Modifier');

        }
        return $this->render('@Sante/Default/vaccin_add.html.twig');

    }

    public function journaleAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $query= $em->getRepository("AppBundle:EnfantVaccin")->findBy(array('statut'=>1,'enfant'=>$id));


        return $this->render('SanteBundle:Default:journale.html.twig',array('query'=>$query));
    }



}