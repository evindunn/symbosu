<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Kmcs
 *
 * @ORM\Table(name="kmdescr")
 * @ORM\Entity
 */
class Kmdescr {
  public static $GARDEN_CIDS = [
    # Core attributes
      680,    # Sunlight
      683,    # Moisture
      738,    # Width
      140,    # Height
      # Plant features
      612,    # Flower color
      165,    # Bloom months
      685,    # Wildlife support
      136,    # Lifespan
      100,    # Foliage type
      137,    # Plant type
      # Growth & maintenance
      679,    # Landscape uses
      767,    # Cultivation preferences
      688,    # Plant behavior
      670,    # Propagation
      684,    # Ease of growth
      # Beyond the garden
      163,    # Habitat
      19,     # Ecoregion
    ];
  
  /**
   * @var integer
   * @ORM\Id
   * @ORM\Column(name="tid", type="integer")
   */
  private $tid;

  /**
   * @var integer
   * @ORM\Id
   * @ORM\Column(name="cid", type="integer")
   */
  private $cid;

  /**
   * @var string
   * @ORM\Id
   * @ORM\Column(name="cs", type="integer")
   */
  private $cs;

  /**
   * @var integer
   * @ORM\Id
   * @ORM\Column(name="seq", type="integer")
   */
  private $seq;

  /**
   * @return int
   */
  public function getTid() {
    return $this->tid;
  }

  /**
   * @return int
   */
  public function getCid() {
    return $this->cid;
  }

  /**
   * @return string
   */
  public function getCs() {
    return $this->cs;
  }

  /**
   * @return int
   */
  public function getSeq() {
    return $this->seq;
  }
}
?>