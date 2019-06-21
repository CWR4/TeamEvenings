<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use App\Form\ChangeUsernameType;
use App\Form\DeleteUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @return Response
     * @Route("/user", name="all_user")
     */
    public function listAll(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->getAllUser();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @param Request $request
     * @param $userId
     * @return Response
     * @Route("/deleteuser/{userId<\d+>?}", name="delete_user")
     */
    public function deleteUser(Request $request, $userId): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
        $deleteForm = $this->createForm(DeleteUserType::class);
        $deleteForm->add('id', HiddenType::class, ['data' => $userId]);
        $deleteForm->handleRequest($request);

        if($deleteForm->isSubmitted())
        {
            $manager = $this->getDoctrine()->getManager();
            $user = $this->getDoctrine()->getRepository(User::class)->find($deleteForm->getData());
            $this->getDoctrine()->getRepository(Vote::class)->deleteVotes($user);
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', 'Nutzer gelöscht');

            return $this->redirectToRoute('all_user');
        }

        return $this->render('user/deleteUser.html.twig', [
            'form' => $deleteForm->createView(),
            'user' => $user,
        ]);

    }

    /**
     * @param User $id
     * @return Response
     * @Route("/user/edit/{id<\d+>?}", name="edit_user")
     */
    public function editUser(User $id): Response
    {
        return $this->render('user/editUser.html.twig', [
            'user' => $id
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     * @Route("/user/changeusername/{user<\d+>?0}", name="change_username")
     */
    public function changeUsername(Request $request, User $user): Response
    {
        $form = $this->createForm(ChangeUsernameType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager = $this->getDoctrine()->getManager();

            $user->setUsername($form->getData()->getUsername());

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Nutzername geändert!');

            return $this->redirectToRoute('edit_user', ['id' => $user->getId()]);
        }

        return $this->render('user/changeUsername.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
