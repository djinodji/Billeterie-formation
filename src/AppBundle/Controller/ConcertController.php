<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use AppBundle\Entity\Concert;
use AppBundle\Type\ConcertType;

/**
 * Concert controller.
 *
 * @Route("/admin/concert")
 */
class ConcertController extends Controller
{
    /**
     * Lists all Concert entities.
     *
     * @Route("/", name="concert_index")
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $concerts = $this->getDoctrine()->getRepository('AppBundle:Concert')->findAll();

        return array('concerts' => $concerts);

    }

    /**
     * Creates a new Concert entity.
     *
     * @Route("/new", name="concert_new")
     * @Template
     */
    public function newAction(Request $request)
    {
        $concert = new Concert();
        $form = $this->createForm(ConcertType::class, $concert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($concert);
            $em->flush();

            $this->addFlash('success', 'The concert has been successfully added.');

            return $this->redirectToRoute('concert_new');
        }

        return array('concertForm' => $form->createView());
    }

    /**
     * Finds and displays a Concert entity.
     * @Template
     * @Route("/{id}", name="concert_show")
     * @Method("GET")
     */
    public function showAction(Concert $concert)
    {
        $deleteForm = $this->createDeleteForm($concert);
        $editForm = $this->createForm(ConcertType::class, $concert);

        return array('concert' => $concert);
        return array('form' => $editForm->createView());

    }

    /**
     * Displays a form to edit an existing Concert entity.
     * @Template
     * @Route("/{id}/edit", name="concert_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Concert $concert)
    {
        $deleteForm = $this->createDeleteForm($concert);
        $editForm = $this->createForm(ConcertType::class, $concert);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($concert);
            $em->flush();

            return $this->redirectToRoute('concert_show', array('id' => $concert->getId()));
        }

        return array('edit_form' => $editForm->createView());

    }

    /**
     * Deletes a Concert entity.
     *
     * @Route("/{id}", name="concert_delete")
     *
     * @Method("POST")
     */
    public function deleteAction(Request $request, Concert $concert)
    {
        $form = $this->createDeleteForm($concert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($concert);
            $em->flush();
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($concert);
        $em->flush();
        return $this->redirectToRoute('concert_index');
    }

    /**
     * Creates a form to delete a Concert entity.
     *
     * @param Concert $concert The Concert entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Concert $concert)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('concert_delete', array('id' => $concert->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
