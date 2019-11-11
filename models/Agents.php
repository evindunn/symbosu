<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Agents
 *
 * @ORM\Table(name="agents", indexes={@ORM\Index(name="firstname", columns={"firstname"}), @ORM\Index(name="FK_preferred_recby", columns={"preferredrecbyid"})})
 * @ORM\Entity
 */
class Agents
{
    /**
     * @var int
     *
     * @ORM\Column(name="agentid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $agentid;

    /**
     * @var string
     *
     * @ORM\Column(name="familyname", type="string", length=45, nullable=false)
     */
    private $familyname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="firstname", type="string", length=45, nullable=true)
     */
    private $firstname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="middlename", type="string", length=45, nullable=true)
     */
    private $middlename;

    /**
     * @var int|null
     *
     * @ORM\Column(name="startyearactive", type="integer", nullable=true)
     */
    private $startyearactive;

    /**
     * @var int|null
     *
     * @ORM\Column(name="endyearactive", type="integer", nullable=true)
     */
    private $endyearactive;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="string", length=255, nullable=true)
     */
    private $notes;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rating", type="integer", nullable=true, options={"default"="10"})
     */
    private $rating = '10';

    /**
     * @var string|null
     *
     * @ORM\Column(name="guid", type="string", length=900, nullable=true)
     */
    private $guid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="initialtimestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $initialtimestamp = 'CURRENT_TIMESTAMP';

    /**
     * @var string|null
     *
     * @ORM\Column(name="uuid", type="string", length=43, nullable=true, options={"fixed"=true})
     */
    private $uuid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="biography", type="text", length=65535, nullable=true)
     */
    private $biography;

    /**
     * @var string|null
     *
     * @ORM\Column(name="taxonomicgroups", type="string", length=900, nullable=true)
     */
    private $taxonomicgroups;

    /**
     * @var string|null
     *
     * @ORM\Column(name="collectionsat", type="string", length=900, nullable=true)
     */
    private $collectionsat;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="curated", type="boolean", nullable=true)
     */
    private $curated = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="nototherwisespecified", type="boolean", nullable=true)
     */
    private $nototherwisespecified = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=0, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prefix", type="string", length=32, nullable=true)
     */
    private $prefix;

    /**
     * @var string|null
     *
     * @ORM\Column(name="suffix", type="string", length=32, nullable=true)
     */
    private $suffix;

    /**
     * @var string|null
     *
     * @ORM\Column(name="namestring", type="text", length=65535, nullable=true)
     */
    private $namestring;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mbox_sha1sum", type="string", length=40, nullable=true, options={"fixed"=true})
     */
    private $mboxSha1sum;

    /**
     * @var int|null
     *
     * @ORM\Column(name="yearofbirth", type="integer", nullable=true)
     */
    private $yearofbirth;

    /**
     * @var string|null
     *
     * @ORM\Column(name="yearofbirthmodifier", type="string", length=12, nullable=true)
     */
    private $yearofbirthmodifier = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="yearofdeath", type="integer", nullable=true)
     */
    private $yearofdeath;

    /**
     * @var string|null
     *
     * @ORM\Column(name="yearofdeathmodifier", type="string", length=12, nullable=true)
     */
    private $yearofdeathmodifier = '';

    /**
     * @var string
     *
     * @ORM\Column(name="living", type="string", length=0, nullable=false, options={"default"="?"})
     */
    private $living = '?';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datelastmodified", type="datetime", nullable=true)
     */
    private $datelastmodified;

    /**
     * @var int|null
     *
     * @ORM\Column(name="lastmodifiedbyuid", type="integer", nullable=true)
     */
    private $lastmodifiedbyuid;

    /**
     * @var \Agents
     *
     * @ORM\ManyToOne(targetEntity="Agents")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="preferredrecbyid", referencedColumnName="agentid")
     * })
     */
    private $preferredrecbyid;


    /**
     * Get agentid.
     *
     * @return int
     */
    public function getAgentid()
    {
        return $this->agentid;
    }

    /**
     * Set familyname.
     *
     * @param string $familyname
     *
     * @return Agents
     */
    public function setFamilyname($familyname)
    {
        $this->familyname = $familyname;

        return $this;
    }

    /**
     * Get familyname.
     *
     * @return string
     */
    public function getFamilyname()
    {
        return $this->familyname;
    }

    /**
     * Set firstname.
     *
     * @param string|null $firstname
     *
     * @return Agents
     */
    public function setFirstname($firstname = null)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname.
     *
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set middlename.
     *
     * @param string|null $middlename
     *
     * @return Agents
     */
    public function setMiddlename($middlename = null)
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * Get middlename.
     *
     * @return string|null
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * Set startyearactive.
     *
     * @param int|null $startyearactive
     *
     * @return Agents
     */
    public function setStartyearactive($startyearactive = null)
    {
        $this->startyearactive = $startyearactive;

        return $this;
    }

    /**
     * Get startyearactive.
     *
     * @return int|null
     */
    public function getStartyearactive()
    {
        return $this->startyearactive;
    }

    /**
     * Set endyearactive.
     *
     * @param int|null $endyearactive
     *
     * @return Agents
     */
    public function setEndyearactive($endyearactive = null)
    {
        $this->endyearactive = $endyearactive;

        return $this;
    }

    /**
     * Get endyearactive.
     *
     * @return int|null
     */
    public function getEndyearactive()
    {
        return $this->endyearactive;
    }

    /**
     * Set notes.
     *
     * @param string|null $notes
     *
     * @return Agents
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
     * Set rating.
     *
     * @param int|null $rating
     *
     * @return Agents
     */
    public function setRating($rating = null)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating.
     *
     * @return int|null
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set guid.
     *
     * @param string|null $guid
     *
     * @return Agents
     */
    public function setGuid($guid = null)
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * Get guid.
     *
     * @return string|null
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Set initialtimestamp.
     *
     * @param \DateTime $initialtimestamp
     *
     * @return Agents
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

    /**
     * Set uuid.
     *
     * @param string|null $uuid
     *
     * @return Agents
     */
    public function setUuid($uuid = null)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid.
     *
     * @return string|null
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set biography.
     *
     * @param string|null $biography
     *
     * @return Agents
     */
    public function setBiography($biography = null)
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * Get biography.
     *
     * @return string|null
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * Set taxonomicgroups.
     *
     * @param string|null $taxonomicgroups
     *
     * @return Agents
     */
    public function setTaxonomicgroups($taxonomicgroups = null)
    {
        $this->taxonomicgroups = $taxonomicgroups;

        return $this;
    }

    /**
     * Get taxonomicgroups.
     *
     * @return string|null
     */
    public function getTaxonomicgroups()
    {
        return $this->taxonomicgroups;
    }

    /**
     * Set collectionsat.
     *
     * @param string|null $collectionsat
     *
     * @return Agents
     */
    public function setCollectionsat($collectionsat = null)
    {
        $this->collectionsat = $collectionsat;

        return $this;
    }

    /**
     * Get collectionsat.
     *
     * @return string|null
     */
    public function getCollectionsat()
    {
        return $this->collectionsat;
    }

    /**
     * Set curated.
     *
     * @param bool|null $curated
     *
     * @return Agents
     */
    public function setCurated($curated = null)
    {
        $this->curated = $curated;

        return $this;
    }

    /**
     * Get curated.
     *
     * @return bool|null
     */
    public function getCurated()
    {
        return $this->curated;
    }

    /**
     * Set nototherwisespecified.
     *
     * @param bool|null $nototherwisespecified
     *
     * @return Agents
     */
    public function setNototherwisespecified($nototherwisespecified = null)
    {
        $this->nototherwisespecified = $nototherwisespecified;

        return $this;
    }

    /**
     * Get nototherwisespecified.
     *
     * @return bool|null
     */
    public function getNototherwisespecified()
    {
        return $this->nototherwisespecified;
    }

    /**
     * Set type.
     *
     * @param string|null $type
     *
     * @return Agents
     */
    public function setType($type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set prefix.
     *
     * @param string|null $prefix
     *
     * @return Agents
     */
    public function setPrefix($prefix = null)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get prefix.
     *
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set suffix.
     *
     * @param string|null $suffix
     *
     * @return Agents
     */
    public function setSuffix($suffix = null)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Get suffix.
     *
     * @return string|null
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * Set namestring.
     *
     * @param string|null $namestring
     *
     * @return Agents
     */
    public function setNamestring($namestring = null)
    {
        $this->namestring = $namestring;

        return $this;
    }

    /**
     * Get namestring.
     *
     * @return string|null
     */
    public function getNamestring()
    {
        return $this->namestring;
    }

    /**
     * Set mboxSha1sum.
     *
     * @param string|null $mboxSha1sum
     *
     * @return Agents
     */
    public function setMboxSha1sum($mboxSha1sum = null)
    {
        $this->mboxSha1sum = $mboxSha1sum;

        return $this;
    }

    /**
     * Get mboxSha1sum.
     *
     * @return string|null
     */
    public function getMboxSha1sum()
    {
        return $this->mboxSha1sum;
    }

    /**
     * Set yearofbirth.
     *
     * @param int|null $yearofbirth
     *
     * @return Agents
     */
    public function setYearofbirth($yearofbirth = null)
    {
        $this->yearofbirth = $yearofbirth;

        return $this;
    }

    /**
     * Get yearofbirth.
     *
     * @return int|null
     */
    public function getYearofbirth()
    {
        return $this->yearofbirth;
    }

    /**
     * Set yearofbirthmodifier.
     *
     * @param string|null $yearofbirthmodifier
     *
     * @return Agents
     */
    public function setYearofbirthmodifier($yearofbirthmodifier = null)
    {
        $this->yearofbirthmodifier = $yearofbirthmodifier;

        return $this;
    }

    /**
     * Get yearofbirthmodifier.
     *
     * @return string|null
     */
    public function getYearofbirthmodifier()
    {
        return $this->yearofbirthmodifier;
    }

    /**
     * Set yearofdeath.
     *
     * @param int|null $yearofdeath
     *
     * @return Agents
     */
    public function setYearofdeath($yearofdeath = null)
    {
        $this->yearofdeath = $yearofdeath;

        return $this;
    }

    /**
     * Get yearofdeath.
     *
     * @return int|null
     */
    public function getYearofdeath()
    {
        return $this->yearofdeath;
    }

    /**
     * Set yearofdeathmodifier.
     *
     * @param string|null $yearofdeathmodifier
     *
     * @return Agents
     */
    public function setYearofdeathmodifier($yearofdeathmodifier = null)
    {
        $this->yearofdeathmodifier = $yearofdeathmodifier;

        return $this;
    }

    /**
     * Get yearofdeathmodifier.
     *
     * @return string|null
     */
    public function getYearofdeathmodifier()
    {
        return $this->yearofdeathmodifier;
    }

    /**
     * Set living.
     *
     * @param string $living
     *
     * @return Agents
     */
    public function setLiving($living)
    {
        $this->living = $living;

        return $this;
    }

    /**
     * Get living.
     *
     * @return string
     */
    public function getLiving()
    {
        return $this->living;
    }

    /**
     * Set datelastmodified.
     *
     * @param \DateTime|null $datelastmodified
     *
     * @return Agents
     */
    public function setDatelastmodified($datelastmodified = null)
    {
        $this->datelastmodified = $datelastmodified;

        return $this;
    }

    /**
     * Get datelastmodified.
     *
     * @return \DateTime|null
     */
    public function getDatelastmodified()
    {
        return $this->datelastmodified;
    }

    /**
     * Set lastmodifiedbyuid.
     *
     * @param int|null $lastmodifiedbyuid
     *
     * @return Agents
     */
    public function setLastmodifiedbyuid($lastmodifiedbyuid = null)
    {
        $this->lastmodifiedbyuid = $lastmodifiedbyuid;

        return $this;
    }

    /**
     * Get lastmodifiedbyuid.
     *
     * @return int|null
     */
    public function getLastmodifiedbyuid()
    {
        return $this->lastmodifiedbyuid;
    }

    /**
     * Set preferredrecbyid.
     *
     * @param \Agents|null $preferredrecbyid
     *
     * @return Agents
     */
    public function setPreferredrecbyid(\Agents $preferredrecbyid = null)
    {
        $this->preferredrecbyid = $preferredrecbyid;

        return $this;
    }

    /**
     * Get preferredrecbyid.
     *
     * @return \Agents|null
     */
    public function getPreferredrecbyid()
    {
        return $this->preferredrecbyid;
    }
}
