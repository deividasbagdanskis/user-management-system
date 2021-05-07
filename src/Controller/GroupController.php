<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/groups", name="groups-")
 */
class GroupController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GroupRepository $groupRepository): Response
    {
        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $group = new Group();

        $users = $userRepository->findByRole('ROLE_USER');

        $form = $this->createForm(GroupType::class, $group, [
            'users' => $users
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('groups-index');
        }

        return $this->render('group/new.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Group $group): Response
    {
        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Group $group, UserRepository $userRepository): Response
    {
        $users = $userRepository->findByRole('ROLE_USER');

        $form = $this->createForm(GroupType::class, $group, [
            'users' => $users
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('groups-index');
        }

        return $this->render('group/edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Group $group): Response
    {
        dump($request);
        if ($this->isCsrfTokenValid('delete' . $group->getId(), $request->request->get('_token'))) {
            if ($group->getUsers()->count() != 0) {
                $this->addFlash(
                    'error',
                    'This group cannot be deleted because it has users!'
                );

                return $this->render('group/show.html.twig', [
                    'group' => $group,
                ]);
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($group);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('groups-index');
    }
}
