<?php

namespace CCS\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CCS\BaseBundle\Entity\Character;
use CCS\BaseBundle\Form\CharacterType;

/**
 * Character controller.
 *
 */
class CharacterController extends BaseController
{
    /**
     * Lists all Character entities.
     *
     */
    public function getCharactersAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CCSBaseBundle:Character')->findAll();

        $view = $this->view(array('characters' => $entities), 200)
            ->setTemplate("CCSBaseBundle:Character:index.html.twig")
            ->setTemplateVar('characters')
        ;

        return $this->handleView($view);
    }

    /**
     * Finds and displays a Character entity.
     *
     */
    public function getCharacterAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CCSBaseBundle:Character')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Character entity.');
        }

        $view = $this->view($entity, 200)
            ->setTemplate("CCSBaseBundle:Character:index.html.twig")
            ->setTemplateVar('character')
        ;

        return $this->handleView($view);
    }

    /**
     * Displays a form to create a new Character entity.
     *
     */
    public function newCharactersAction()
    {
        $entity = new Character();
        $form   = $this->createForm(new CharacterType(), $entity);

        return $this->render('CCSBaseBundle:Character:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Character entity.
     *
     */
    public function postCharactersAction(Request $request)
    {
        $entity  = new Character();
        $form = $this->createForm(new CharacterType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('character_show', array('id' => $entity->getId())));
        }

        return $this->render('CCSBaseBundle:Character:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Character entity.
     *
     */
    public function editCharactersAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CCSBaseBundle:Character')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Character entity.');
        }

        $editForm = $this->createForm(new CharacterType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CCSBaseBundle:Character:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Character entity.
     *
     */
    public function updateCharactersAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CCSBaseBundle:Character')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Character entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CharacterType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('character_edit', array('id' => $id)));
        }

        return $this->render('CCSBaseBundle:Character:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Character entity.
     *
     */
    public function deleteCharactersAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CCSBaseBundle:Character')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Character entity.');
        }

        $em->remove($entity);
        $em->flush();


        $view = $this->view(null, 200);

        return $this->handleView($view);
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }


}
