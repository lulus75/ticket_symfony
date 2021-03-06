<?php

namespace TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\TicketRepository")
 */
class Ticket
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
    
     * @ORM\OneToMany(targetEntity="Message",mappedBy="ticket",cascade={"persist", "remove"})
     */
    private $message;

    /**
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User",cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User",cascade={"persist"}, inversedBy = "ticket")
     */
    private $authorization;

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
     * Set title
     *
     * @param string $title
     *
     * @return Ticket
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Ticket
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Ticket
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Ticket
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Ticket
     */
    public function addUser(\UserBundle\Entity\User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \UserBundle\Entity\User $user
     */
    public function removeUser(\UserBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Add message
     *
     * @param \TicketBundle\Entity\Message $message
     *
     * @return Ticket
     */
    public function addMessage(\TicketBundle\Entity\Message $message)
    {
        $this->message[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \TicketBundle\Entity\Message $message
     */
    public function removeMessage(\TicketBundle\Entity\Message $message)
    {
        $this->message->removeElement($message);
    }

    /**
     * Get message
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param array $message
     *
     * @return Ticket
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }


    /**
     * Add authorization
     *
     * @param \UserBundle\Entity\User $authorization
     *
     * @return Ticket
     */
    public function addAuthorization(\UserBundle\Entity\User $authorization)
    {
        $this->authorization[] = $authorization;

        return $this;
    }

    /**
     * Remove authorization
     *
     * @param \UserBundle\Entity\User $authorization
     */
    public function removeAuthorization(\UserBundle\Entity\User $authorization)
    {
        $this->authorization->removeElement($authorization);
    }

    /**
     * Get authorization
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }
}
