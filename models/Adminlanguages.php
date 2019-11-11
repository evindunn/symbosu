<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Adminlanguages
 *
 * @ORM\Table(name="adminlanguages", uniqueConstraints={@ORM\UniqueConstraint(name="index_langname_unique", columns={"langname"})})
 * @ORM\Entity
 */
class Adminlanguages
{
    /**
     * @var int
     *
     * @ORM\Column(name="langid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $langid;

    /**
     * @var string
     *
     * @ORM\Column(name="langname", type="string", length=45, nullable=false)
     */
    private $langname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="iso639_1", type="string", length=2, nullable=true)
     */
    private $iso6391;

    /**
     * @var string|null
     *
     * @ORM\Column(name="iso639_2", type="string", length=3, nullable=true)
     */
    private $iso6392;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="string", length=45, nullable=true)
     */
    private $notes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="initialtimestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $initialtimestamp = 'CURRENT_TIMESTAMP';


    /**
     * Get langid.
     *
     * @return int
     */
    public function getLangid()
    {
        return $this->langid;
    }

    /**
     * Set langname.
     *
     * @param string $langname
     *
     * @return Adminlanguages
     */
    public function setLangname($langname)
    {
        $this->langname = $langname;

        return $this;
    }

    /**
     * Get langname.
     *
     * @return string
     */
    public function getLangname()
    {
        return $this->langname;
    }

    /**
     * Set iso6391.
     *
     * @param string|null $iso6391
     *
     * @return Adminlanguages
     */
    public function setIso6391($iso6391 = null)
    {
        $this->iso6391 = $iso6391;

        return $this;
    }

    /**
     * Get iso6391.
     *
     * @return string|null
     */
    public function getIso6391()
    {
        return $this->iso6391;
    }

    /**
     * Set iso6392.
     *
     * @param string|null $iso6392
     *
     * @return Adminlanguages
     */
    public function setIso6392($iso6392 = null)
    {
        $this->iso6392 = $iso6392;

        return $this;
    }

    /**
     * Get iso6392.
     *
     * @return string|null
     */
    public function getIso6392()
    {
        return $this->iso6392;
    }

    /**
     * Set notes.
     *
     * @param string|null $notes
     *
     * @return Adminlanguages
     */
    public function setNotes($notes = null)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes.
     *
     * @return string|null
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set initialtimestamp.
     *
     * @param \DateTime $initialtimestamp
     *
     * @return Adminlanguages
     */
    public function setInitialtimestamp($initialtimestamp)
    {
        $this->initialtimestamp = $initialtimestamp;

        return $this;
    }

    /**
     * Get initialtimestamp.
     *
     * @return \DateTime
     */
    public function getInitialtimestamp()
    {
        return $this->initialtimestamp;
    }
}
