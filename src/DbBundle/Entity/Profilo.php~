<?php 

namespace DbBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as Collection;
//serializer
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;

/**
* Profilo
*
* @ORM\Table(name="profilo")
* @ORM\Entity(repositoryClass="DbBundle\Repository\ProfiloRepository")
*/
class Profilo {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"Profilo", "Profilo.id"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nomeProfilo", type="string", length=255, nullable=true)
     * @Groups({"Profilo", "Profilo.nomeProfilo"})
     */
    private $nomeProfilo;

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
     *
     * Molti Profilo sono associato a molti Utente (profiloUtente è una lista di utenti)
     *
     * @ORM\ManyToMany(targetEntity="Utente", mappedBy="profilo") 
     * @Groups({"Profilo.profiloUtente"})
     */
    private $profiloUtente;

    /**
     * Constructor
     */
    public function __construct(){
        $this->timeInsert = new \DateTime('now');
        $this->timeAction = new \DateTime('now');
        $this->userAction = 0;
        $this->status = "A";

        #####
        $this->profiloutente = new Collection();
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
     * Set timeInsert
     *
     * @param \DateTime $timeInsert
     *
     * @return Profilo
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
     * @return Profilo
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
     * @return Profilo
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
     * @return Profilo
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
     * @return Profilo
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
     * Add profiloUtente
     *
     * @param \DbBundle\Entity\Utente $profiloUtente
     *
     * @return Profilo
     */
    public function addProfiloUtente(\DbBundle\Entity\Utente $profiloUtente)
    {
        $this->profiloUtente[] = $profiloUtente;

        return $this;
    }

    /**
     * Remove profiloUtente
     *
     * @param \DbBundle\Entity\Utente $profiloUtente
     */
    public function removeProfiloUtente(\DbBundle\Entity\Utente $profiloUtente)
    {
        $this->profiloUtente->removeElement($profiloUtente);
    }

    /**
     * Get profiloUtente
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProfiloUtente()
    {
        return $this->profiloUtente;
    }

    /**
     * Set nomeProfilo
     *
     * @param string $nomeProfilo
     *
     * @return Profilo
     */
    public function setNomeProfilo($nomeProfilo)
    {
        $this->nomeProfilo = $nomeProfilo;

        return $this;
    }

    /**
     * Get nomeProfilo
     *
     * @return string
     */
    public function getNomeProfilo()
    {
        return $this->nomeProfilo;
    }
}
