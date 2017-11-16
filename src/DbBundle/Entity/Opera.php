<?php 

namespace DbBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as Collection;
//serializer
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;

/**
* Opera
*
* @ORM\Table(name="opera")
* @ORM\Entity(repositoryClass="DbBundle\Repository\OperaRepository")
*/
class Opera {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"Opera", "Opera.id"})
     */
    private $id;

    /**
     * Molti Opera sono associati ad un unico Autore
     *
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Autore", inversedBy="opere")
     * @ORM\JoinColumn(name="autore_id", referencedColumnName="id")
     * @Groups({"Opera.autoreId"})
     */
    private $autoreId;

    /**
     *
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Utente", inversedBy="opere")
     * @ORM\JoinColumn(name="utente_id", referencedColumnName="id")
     * @Groups({"Opera.utenteId"})
     */
    private $utenteId;



    /**
     * @var string
     *
     * @ORM\Column(name="titolo", type="string", length=255, nullable=true)
     * @Groups({"Opera", "Opera.titolo"})
     */
    private $titolo;

    /**
     * @var string
     *
     * @ORM\Column(name="tecnica", type="string", length=255, nullable=true)
     * @Groups({"Opera", "Opera.tecnica"})
     */
    private $tecnica;

    /**
     * @var integer
     *
     * @ORM\Column(name="dimensioni", type="integer", nullable=true)
     * @Groups({"Opera", "Opere.dimensioni"})
     */
    private $dimensioni;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data", type="date", nullable=false)
     */
    private $data;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_insert", type="datetime", nullable=false)
     * @Exclude
     */
    private $timeInsert;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_delete", type="datetime", nullable=true)
     * @Exclude
     */
    private $timeDelete;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_action", type="datetime", nullable=false)
     * @Exclude
     */
    private $timeAction;

    /**
     * @var int
     *
     * @ORM\Column(name="user_action", type="integer", nullable=false)
     * @Exclude
     */
    private $userAction;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=1, nullable=false)
     * @Exclude
     */
    private $status;

    /**
     * Constructor
     */
    public function __construct(){
        $this->timeInsert = new \DateTime('now');
        $this->timeAction = new \DateTime('now');
        $this->userAction = 0;
        $this->status = "A";

        #####

        $this->utenti = new Collection();
    }



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titolo
     *
     * @param string $titolo
     *
     * @return Opera
     */
    public function setTitolo($titolo)
    {
        $this->titolo = $titolo;

        return $this;
    }

    /**
     * Get titolo
     *
     * @return string
     */
    public function getTitolo()
    {
        return $this->titolo;
    }

    /**
     * Set tecnica
     *
     * @param string $tecnica
     *
     * @return Opera
     */
    public function setTecnica($tecnica)
    {
        $this->tecnica = $tecnica;

        return $this;
    }

    /**
     * Get tecnica
     *
     * @return string
     */
    public function getTecnica()
    {
        return $this->tecnica;
    }

    /**
     * Set dimensioni
     *
     * @param integer $dimensioni
     *
     * @return Opera
     */
    public function setDimensioni($dimensioni)
    {
        $this->dimensioni = $dimensioni;

        return $this;
    }

    /**
     * Get dimensioni
     *
     * @return integer
     */
    public function getDimensioni()
    {
        return $this->dimensioni;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     *
     * @return Opera
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set timeInsert
     *
     * @param \DateTime $timeInsert
     *
     * @return Opera
     */
    public function setTimeInsert($timeInsert)
    {
        $this->timeInsert = $timeInsert;

        return $this;
    }

    /**
     * Get timeInsert
     *
     * @return \DateTime
     */
    public function getTimeInsert()
    {
        return $this->timeInsert;
    }

    /**
     * Set timeDelete
     *
     * @param \DateTime $timeDelete
     *
     * @return Opera
     */
    public function setTimeDelete($timeDelete)
    {
        $this->timeDelete = $timeDelete;

        return $this;
    }

    /**
     * Get timeDelete
     *
     * @return \DateTime
     */
    public function getTimeDelete()
    {
        return $this->timeDelete;
    }

    /**
     * Set timeAction
     *
     * @param \DateTime $timeAction
     *
     * @return Opera
     */
    public function setTimeAction($timeAction)
    {
        $this->timeAction = $timeAction;

        return $this;
    }

    /**
     * Get timeAction
     *
     * @return \DateTime
     */
    public function getTimeAction()
    {
        return $this->timeAction;
    }

    /**
     * Set userAction
     *
     * @param integer $userAction
     *
     * @return Opera
     */
    public function setUserAction($userAction)
    {
        $this->userAction = $userAction;

        return $this;
    }

    /**
     * Get userAction
     *
     * @return integer
     */
    public function getUserAction()
    {
        return $this->userAction;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Opera
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set autoreId
     *
     * @param \DbBundle\Entity\Autore $autoreId
     *
     * @return Opera
     */
    public function setAutoreId(\DbBundle\Entity\Autore $autoreId = null)
    {
        $this->autoreId = $autoreId;

        return $this;
    }

    /**
     * Get autoreId
     *
     * @return \DbBundle\Entity\Autore
     */
    public function getAutoreId()
    {
        return $this->autoreId;
    }

    /**
     * Set utenteId
     *
     * @param \DbBundle\Entity\Utente $utenteId
     *
     * @return Opera
     */
    public function setUtenteId(\DbBundle\Entity\Utente $utenteId = null)
    {
        $this->utenteId = $utenteId;

        return $this;
    }

    /**
     * Get utenteId
     *
     * @return \DbBundle\Entity\Utente
     */
    public function getUtenteId()
    {
        return $this->utenteId;
    }

    /**
     * Add utenti
     *
     * @param \DbBundle\Entity\Utente $utenti
     *
     * @return Opera
     */
    public function addUtenti(\DbBundle\Entity\Utente $utenti)
    {
        $this->utenti[] = $utenti;

        return $this;
    }

    /**
     * Remove utenti
     *
     * @param \DbBundle\Entity\Utente $utenti
     */
    public function removeUtenti(\DbBundle\Entity\Utente $utenti)
    {
        $this->utenti->removeElement($utenti);
    }

    /**
     * Get utenti
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtenti()
    {
        return $this->utenti;
    }
}
