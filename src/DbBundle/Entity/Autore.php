<?php 

namespace DbBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as Collection;
//serializer
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;

/**
* Autore
*
* @ORM\Table(name="autore")
* @ORM\Entity(repositoryClass="DbBundle\Repository\AutoreRepository")
*/
class Autore {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"Autore", "Autore.id"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=255, nullable=true)
     * @Groups({"Autore", "Autore.nome"})
     */
    private $nome;

    /**
     * @var integer
     *
     * @ORM\Column(name="eta", type="integer", nullable=true)
     * @Groups({"Autore", "Autore.eta"})
     */
    private $eta;

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
     * Un Autore e' associato a molti Opera
     *
     * @ORM\OneToMany(targetEntity="Opera", mappedBy="autoreId")
     * @Groups({"Autore.opere"})
     */
    private $opere;

    /**
     * Constructor
     */
    public function __construct(){
        $this->timeInsert = new \DateTime('now');
        $this->timeAction = new \DateTime('now');
        $this->userAction = 0;
        $this->status = "A";

        #####
        $this->opere = new Collection();
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
     * Set nome
     *
     * @param string $nome
     *
     * @return Autore
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set eta
     *
     * @param integer $eta
     *
     * @return Autore
     */
    public function setEta($eta)
    {
        $this->eta = $eta;

        return $this;
    }

    /**
     * Get eta
     *
     * @return integer
     */
    public function getEta()
    {
        return $this->eta;
    }

    /**
     * Set timeInsert
     *
     * @param \DateTime $timeInsert
     *
     * @return Autore
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
     * @return Autore
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
     * @return Autore
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
     * @return Autore
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
     * @return Autore
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
     * Add opere
     *
     * @param \DbBundle\Entity\Opera $opere
     *
     * @return Autore
     */
    public function addOpere(\DbBundle\Entity\Opera $opere)
    {
        $this->opere[] = $opere;

        return $this;
    }

    /**
     * Remove opere
     *
     * @param \DbBundle\Entity\Opera $opere
     */
    public function removeOpere(\DbBundle\Entity\Opera $opere)
    {
        $this->opere->removeElement($opere);
    }

    /**
     * Get opere
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpere()
    {
        return $this->opere;
    }
}
