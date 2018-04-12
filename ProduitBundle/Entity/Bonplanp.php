<?php

namespace ProduitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bonplanp
 *
 * @ORM\Table(name="bonplanp")
 * @ORM\Entity(repositoryClass="ProduitBundle\Repository\BonplanpRepository")
 */
class Bonplanp
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var \Produit
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Produit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="produit", referencedColumnName="id")
     * })
     */
    private $produit;

    /**
     * @return \Produit
     */
    public function getProduit()
    {
        return $this->produit;
    }

    /**
     * @param \Produit $produit
     */
    public function setProduit($produit)
    {
        $this->produit = $produit;
    }

}

