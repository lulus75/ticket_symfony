<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;


/**
 * User
 * * @ORM\Entity
 * @ORM\Table(name="fos_user")
 *
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     * @ORM\ManyToMany(targetEntity="TicketBundle\Entity\Ticket",cascade={"persist"},mappedBy="authorization")
     */
    private $ticket;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Set ticket
     *
     * @param \TicketBundle\Entity\Ticket $ticket
     *
     * @return User
     */
    public function setTicket(\TicketBundle\Entity\Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return \TicketBundle\Entity\Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Add ticket
     *
     * @param \TicketBundle\Entity\Ticket $ticket
     *
     * @return User
     */
    public function addTicket(\TicketBundle\Entity\Ticket $ticket)
    {
        $this->ticket[] = $ticket;

        return $this;
    }

    /**
     * Remove ticket
     *
     * @param \TicketBundle\Entity\Ticket $ticket
     */
    public function removeTicket(\TicketBundle\Entity\Ticket $ticket)
    {
        $this->ticket->removeElement($ticket);
    }
}
