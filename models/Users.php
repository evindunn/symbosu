<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="Index_email", columns={"email", "lastname"})})
 * @ORM\Entity
 */
class Users
{
    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $uid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="firstname", type="string", length=45, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=45, nullable=false)
     */
    private $lastname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=150, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="institution", type="string", length=200, nullable=true)
     */
    private $institution;

    /**
     * @var string|null
     *
     * @ORM\Column(name="department", type="string", length=200, nullable=true)
     */
    private $department;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=100, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="state", type="string", length=50, nullable=true)
     */
    private $state;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zip", type="string", length=15, nullable=true)
     */
    private $zip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="country", type="string", length=50, nullable=true)
     */
    private $country;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=45, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RegionOfInterest", type="string", length=45, nullable=true)
     */
    private $regionofinterest;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="string", length=400, nullable=true)
     */
    private $url;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Biography", type="string", length=1500, nullable=true)
     */
    private $biography;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="string", length=255, nullable=true)
     */
    private $notes;

    /**
     * @var int
     *
     * @ORM\Column(name="ispublic", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $ispublic = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="defaultrights", type="string", length=250, nullable=true)
     */
    private $defaultrights;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rightsholder", type="string", length=250, nullable=true)
     */
    private $rightsholder;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rights", type="string", length=250, nullable=true)
     */
    private $rights;

    /**
     * @var string|null
     *
     * @ORM\Column(name="accessrights", type="string", length=250, nullable=true)
     */
    private $accessrights;

    /**
     * @var string|null
     *
     * @ORM\Column(name="guid", type="string", length=45, nullable=true)
     */
    private $guid;

    /**
     * @var string
     *
     * @ORM\Column(name="validated", type="string", length=45, nullable=false)
     */
    private $validated = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="usergroups", type="string", length=100, nullable=true)
     */
    private $usergroups;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="InitialTimeStamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $initialtimestamp = 'CURRENT_TIMESTAMP';


    /**
     * Get uid.
     *
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set firstname.
     *
     * @param string|null $firstname
     *
     * @return Users
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
     * Set lastname.
     *
     * @param string $lastname
     *
     * @return Users
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname.
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Users
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set institution.
     *
     * @param string|null $institution
     *
     * @return Users
     */
    public function setInstitution($institution = null)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution.
     *
     * @return string|null
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set department.
     *
     * @param string|null $department
     *
     * @return Users
     */
    public function setDepartment($department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department.
     *
     * @return string|null
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return Users
     */
    public function setAddress($address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city.
     *
     * @param string|null $city
     *
     * @return Users
     */
    public function setCity($city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state.
     *
     * @param string|null $state
     *
     * @return Users
     */
    public function setState($state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return string|null
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set zip.
     *
     * @param string|null $zip
     *
     * @return Users
     */
    public function setZip($zip = null)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip.
     *
     * @return string|null
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set country.
     *
     * @param string|null $country
     *
     * @return Users
     */
    public function setCountry($country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country.
     *
     * @return string|null
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     *
     * @return Users
     */
    public function setPhone($phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set regionofinterest.
     *
     * @param string|null $regionofinterest
     *
     * @return Users
     */
    public function setRegionofinterest($regionofinterest = null)
    {
        $this->regionofinterest = $regionofinterest;

        return $this;
    }

    /**
     * Get regionofinterest.
     *
     * @return string|null
     */
    public function getRegionofinterest()
    {
        return $this->regionofinterest;
    }

    /**
     * Set url.
     *
     * @param string|null $url
     *
     * @return Users
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set biography.
     *
     * @param string|null $biography
     *
     * @return Users
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
     * Set notes.
     *
     * @param string|null $notes
     *
     * @return Users
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
     * Set ispublic.
     *
     * @param int $ispublic
     *
     * @return Users
     */
    public function setIspublic($ispublic)
    {
        $this->ispublic = $ispublic;

        return $this;
    }

    /**
     * Get ispublic.
     *
     * @return int
     */
    public function getIspublic()
    {
        return $this->ispublic;
    }

    /**
     * Set defaultrights.
     *
     * @param string|null $defaultrights
     *
     * @return Users
     */
    public function setDefaultrights($defaultrights = null)
    {
        $this->defaultrights = $defaultrights;

        return $this;
    }

    /**
     * Get defaultrights.
     *
     * @return string|null
     */
    public function getDefaultrights()
    {
        return $this->defaultrights;
    }

    /**
     * Set rightsholder.
     *
     * @param string|null $rightsholder
     *
     * @return Users
     */
    public function setRightsholder($rightsholder = null)
    {
        $this->rightsholder = $rightsholder;

        return $this;
    }

    /**
     * Get rightsholder.
     *
     * @return string|null
     */
    public function getRightsholder()
    {
        return $this->rightsholder;
    }

    /**
     * Set rights.
     *
     * @param string|null $rights
     *
     * @return Users
     */
    public function setRights($rights = null)
    {
        $this->rights = $rights;

        return $this;
    }

    /**
     * Get rights.
     *
     * @return string|null
     */
    public function getRights()
    {
        return $this->rights;
    }

    /**
     * Set accessrights.
     *
     * @param string|null $accessrights
     *
     * @return Users
     */
    public function setAccessrights($accessrights = null)
    {
        $this->accessrights = $accessrights;

        return $this;
    }

    /**
     * Get accessrights.
     *
     * @return string|null
     */
    public function getAccessrights()
    {
        return $this->accessrights;
    }

    /**
     * Set guid.
     *
     * @param string|null $guid
     *
     * @return Users
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
     * Set validated.
     *
     * @param string $validated
     *
     * @return Users
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Get validated.
     *
     * @return string
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * Set usergroups.
     *
     * @param string|null $usergroups
     *
     * @return Users
     */
    public function setUsergroups($usergroups = null)
    {
        $this->usergroups = $usergroups;

        return $this;
    }

    /**
     * Get usergroups.
     *
     * @return string|null
     */
    public function getUsergroups()
    {
        return $this->usergroups;
    }

    /**
     * Set initialtimestamp.
     *
     * @param \DateTime $initialtimestamp
     *
     * @return Users
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
