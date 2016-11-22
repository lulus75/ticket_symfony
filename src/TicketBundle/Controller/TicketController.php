<?php

namespace TicketBundle\Controller;

use TicketBundle\Entity\Ticket;
use TicketBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

/**
 * Ticket controller.
 *
 * @Route("ticket")
 */
class TicketController extends Controller
{
    /**
     * Lists all ticket entities.
     *
     * @Route("/", name="ticket_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $userId = $this->get('security.token_storage')->getToken()->getUser();

        $userAuth = $userId->getTicket();

        $em = $this->getDoctrine()->getManager();

        $count = $em->getRepository('TicketBundle:Ticket');
        $tickets = $em->getRepository('TicketBundle:Ticket')->findAll();
        $user = $em->getRepository('UserBundle:User')->findAll();


        $ticketCount = $count->createQueryBuilder('ticket')
            ->select('COUNT(ticket)')
            ->where('ticket.user ='.$userId->getId())
            ->getQuery()
            ->getSingleScalarResult();


        return $this->render('ticket/index.html.twig', array(
            'tickets' => $tickets,
            'user'=>$user,
            'userAuth'=>$userAuth,
            'ticketCount' => $ticketCount
        ));
    }

    /**
     * Lists all ticket entities.
     *
     * @Route("/board", name="ticket_board")
     * @Method({"GET", "POST"})
     */
    public function boardAction(Request $request)
    {


    $form = $this->createForm('TicketBundle\Form\BoardType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form["users"]->getData();
            $ticket= $form["ticket"]->getData();

            $userRepository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')
            ;
            $userDisp = $userRepository->findById($user->getId());
            $ticketRepository = $this->getDoctrine()->getManager()->getRepository('TicketBundle:Ticket')
            ;
            $ticketDisp = $ticketRepository->findByTitle($ticket->getTitle());
            $ticketId = $ticketDisp[0]->addAuthorization($userDisp[0]);

            $em = $this->getDoctrine()->getManager();
            $em->persist($ticketId);
            $em->flush();


        }

        return $this->render('ticket/board.html.twig', array(
            'form' => $form->createView(),
        ));

    }
    /**
     * Creates a new ticket entity.
     *
     * @Route("/new", name="ticket_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ticket = new Ticket();

        $form = $this->createForm('TicketBundle\Form\TicketType', $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userId = $this->get('security.token_storage')->getToken()->getUser();

            $ticket->setCreated(new \DateTime("now"));
            $ticket ->setUser($userId);
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();

            return $this->redirectToRoute('ticket_show', array('id' => $ticket->getId()));
        }

        return $this->render('ticket/new.html.twig', array(
            'ticket' => $ticket,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ticket entity.
     *
     * @Route("/{id}", name="ticket_show")
     * @Method({"GET","POST"})
     */
    public function showAction(Ticket $ticket, Request $request)
    {


        $userId = $ticket->getUser();
        $userManager = $this->get('fos_user.user_manager');


        $userCurrent = $this->get('security.token_storage')->getToken()->getUser();

        $authorisedUser = false;

        if($ticket->getUser()->getId() == $userCurrent->getId()){
            //return $this->redirectToRoute("ticket_index");
            $authorisedUser = true;
        }

        if(in_array($userCurrent, $ticket->getAuthorization()->getValues())){
            $authorisedUser = true;
        }

        if($userCurrent->hasRole('ROLE_ADMIN')){
            $authorisedUser = true;
        }

        if($authorisedUser == false){
            return $this->redirectToRoute("ticket_index");
        }



        $userAuth = $userId->getTicket();
        $user = $userManager->findUserBy(array('id' => $userId));

        $messageRepository = $this->getDoctrine()->getManager()->getRepository('TicketBundle:Message')
        ;
        $messageDisp = $messageRepository->findByTicket($ticket);


        $message = new Message();

        $deleteForm = $this->createDeleteForm($ticket);
        $form = $this->createForm('TicketBundle\Form\MessageType', $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ticketRepository = $this ->getDoctrine() ->getManager()->getRepository('TicketBundle:Ticket');
            $userId = $this->get('security.token_storage')->getToken()->getUser();
                $ticket = $ticketRepository->find($ticket->getId());
                $message->setTicket($ticket);
                $message->setUser($userId);
                $message->setCreated(new \DateTime("now"));
                $em = $this->getDoctrine()->getManager();
                $em->persist($message);
                $em->flush();

            return $this->redirectToRoute('ticket_show', array('id' => $ticket->getId()));
        }

        return $this->render('ticket/show.html.twig', array(
            'ticket' => $ticket,
            'user' => $user,
            'userAuth' => $userAuth,
            'message' => $messageDisp,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ticket entity.
     *
     * @Route("/{id}/edit", name="ticket_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Ticket $ticket)
    {
        $deleteForm = $this->createDeleteForm($ticket);
        $editForm = $this->createForm('TicketBundle\Form\TicketType', $ticket);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_show', array('id' => $ticket->getId()));
        }

        return $this->render('ticket/edit.html.twig', array(
            'ticket' => $ticket,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ticket entity.
     *
     * @Route("/{id}", name="ticket_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Ticket $ticket)
    {
        $form = $this->createDeleteForm($ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ticket);
            $em->flush($ticket);
        }

        return $this->redirectToRoute('ticket_index');
    }

    /**
     * Creates a form to delete a ticket entity.
     *
     * @param Ticket $ticket The ticket entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Ticket $ticket)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ticket_delete', array('id' => $ticket->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


    /**
     * Creates a new message entity.
     *
     * @Route("/new", name="message_new")
     * @Method({"GET", "POST"})
     */
    public function newMessageAction(Request $request)
    {
        $message = new Message();
        $form = $this->createForm('TicketBundle\Form\MessageType', $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush($message);

            return $this->redirectToRoute('message_show', array('id' => $message->getId()));
        }

        return $this->render('ticket/new.html.twig', array(
            'message' => $message,
            'form' => $form->createView(),
        ));
    }

}
