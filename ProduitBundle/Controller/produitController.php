<?php

namespace ProduitBundle\Controller;

use AppBundle\Entity\Categories;
use AppBundle\Entity\Parents;
use AppBundle\Entity\Produit;
use ProduitBundle\Entity\Bonplanp;
use ProduitBundle\Entity\Favoris;
use ProduitBundle\Entity\test;
use ProduitBundle\Repository\testRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
class produitController extends Controller
{
    public function afficherAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $produit=$em->getRepository(Produit::class)->findAll();
        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator=$this->get('knp_paginator');
        $result=$paginator->paginate(
            $produit,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',1)

        );
        $favoris = $em->getRepository(Favoris::class)->findBy( array('parent' => $this->getUser()->getId()));
        return $this->render('ProduitBundle:Produit:afficherproduit.html.twig', array('produits'=>$result,'favoris'=>$favoris));
    }

    public function ajouterproduitAction(Request $request)
    {


        $produit=new Produit();

        if($request->isMethod('POST'))
        {

            $em=$this->getDoctrine()->getManager();

            $parent = $em->getRepository(Parents::class)->find($this->getUser()->getId());

            var_dump($this->getUser()->getId());

            $produit->setNom($request->get('nom'));
            $produit->setDescription($request->get('description'));
            $produit->setCategorie($request->get('categorie'));
            $produit->setPrix($request->get('prix'));

            $produit->setParent($parent);


            $em->persist($produit);
            $em->flush();

            $file = $request->files->get('imgproduit');



            $file->move(
                $this->getParameter('products_images_directory'),
                $produit->getId()."_".$this->getUser()->getId().".png"
            );

            return $this->redirectToRoute('afficherproduit');


        }
        return $this->render('ProduitBundle:Produit:ajouterproduit.html.twig', array(
            "produits"=>$produit
        ));
    }
    public function afficherseulAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $categories=$em->getRepository(Categories::class)->findAll();
        $produit=$em->getRepository(Produit::class)->find($id);
        return $this->render('ProduitBundle:Produit:afficherproduitseul.html.twig', array('produits'=>$produit,'categs'=>$categories));
    }
    public function editerseulAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $categories=$em->getRepository(Categories::class)->findAll();
        $produit=$em->getRepository(Produit::class)->find($id);
        return $this->render('ProduitBundle:Produit:editmonproduit.html.twig', array('produits'=>$produit,'categs'=>$categories));
    }

    public function trieproduitprixAction()
    {
        $em=$this->getDoctrine()->getManager();
        $produit=$em->getRepository(Produit::class)->findBy(array(), array('prix' => 'desc'));
        return $this->render('ProduitBundle:Produit:afficherproduit.html.twig', array('produits'=>$produit));
    }

    public function mesproduitsAction()
    {
        $em=$this->getDoctrine()->getManager();
        $produit=$em->getRepository(Produit::class)->findBy( array('parent' => $this->getUser()->getId()));
        return $this->render('ProduitBundle:Produit:affichermesproduits.html.twig', array('produits'=>$produit));
    }
    public function statproduitAction()
    {
        $em=$this->getDoctrine()->getManager();

        $stat=$em->getRepository(test::class)->findStatData();
        $stat2=$em->getRepository(test::class)->findTotalProds();




        return $this->render('ProduitBundle:Produit:stat.html.twig',array('array'=>$stat,'pourcentage'=>$stat2));
    }

    public function updateproduitAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        echo "ID PROD EST  est ".$request->request->get('idProd');
        echo "Nom produit est ".$request->request->get('NomProduit');
        $produit=$em->getRepository(Produit::class)->find($request->request->get('idProd'));
        //var_dump($produit);
        $produit->setNom($request->request->get('NomProduit'));
        $produit->setDescription($request->request->get('Description'));
        $produit->setCategorie($request->request->get('categs'));
        $produit->setPrix($request->request->get('Prix'));


        // $produit->setImage($request->request->get('imgProd'));


        $em->flush();
        $produits=$em->getRepository(Produit::class)->findBy( array('parent' => $this->getUser()->getId()));


        return $this->render('ProduitBundle:Produit:afficherproduit.html.twig', array('produits'=>$produits));
    }

    public function supprimerproduitAction($id){
        $produit = new Produit();
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository(Produit::class)->find($id);
        $em->remove($produit);
        $em->flush();
        return  $this->redirectToRoute('mesproduits');
    }

    public function afficherproduitbackAction()
    {
        $em=$this->getDoctrine()->getManager();
        $produit=$em->getRepository(Produit::class)->findAll();
        return $this->render('ProduitBundle:Produit:afficherproduitback.html.twig', array('produits'=>$produit));
    }


    public function supprimerproduitbackAction($id){
        $produit = new Produit();
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository(Produit::class)->find($id);
        $em->remove($produit);
        $em->flush();
        return  $this->redirectToRoute('afficherproduitback');
    }

    public function ajouterfavorisAction($id){

        $favoris = new Favoris();
        $em = $this->getDoctrine()->getManager();

        $produit = $em->getRepository(Produit::class)->find($id);
        var_dump($produit->getNbr());

        $favoris->setProduit($produit);
        $favoris->setParent($this->getUser());

        $n = $produit->getNbr();
        if($n >= 2 ){$this->ajoutbonplant($produit->getId());}
        $em->persist($favoris);
        $em->flush();



        return  $this->redirectToRoute('afficherproduit');

    }

    public function supprimerfavorisAction($id){

        $favoris = new Favoris();
        $em = $this->getDoctrine()->getManager();

        $favoris = $em->getRepository(Favoris::class)->findBy( array('produit' => $id,'parent' => $this->getUser()->getId()));

        $em->remove($favoris[0]);
        $em->flush();
        return  $this->redirectToRoute('afficherproduit');
    }



    public function ajoutbonplant($id){

        $em = $this->getDoctrine()->getManager();

        $bonplan = new Bonplanp();
        $produit = $em->getRepository(Produit::class)->find($id);
        $bonplan->setProduit($produit);
        $em->persist($bonplan);
        $em->flush();
    }


}
