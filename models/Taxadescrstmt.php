<?php


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Taxadescrstmt
 *
 * @ORM\Table(name="taxadescrstmts")
 * @ORM\Entity
 */
class Taxadescrstmt
{
  /**
   * @var integer
   *
   * @ORM\Column(name="tdsid")
   * @ORM\Id
   */
  private $tdsid;

  /**
   * @var integer
   *
   * @ORM\Column(name="tdbid")
   */
  private $tdbid;

  /**
   * @var string
   *
   * @ORM\Column(name="statement")
   */
  private $statement;

  /**
   * @var integer
   *
   * @ORM\Column(name="sortsequence")
   */
  private $sortsequence;

  public function getTdsid() {
    return $this->tdsid;
  }

  public function getTdbid() {
    return $this->tdbid;
  }

  public function getStatement() {
    return $this->statement;
  }
}

?>