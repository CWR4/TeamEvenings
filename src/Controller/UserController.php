<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use App\Form\DeleteUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $deleteForm = $this->createForm(DeleteUserType::class);
        $deleteForm->handleRequest($request);

        if($deleteForm->isSubmitted())
        {
            $manager = $this->getDoctrine()->getManager();
            $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
            $this->getDoctrine()->getRepository(Vote::class)->deleteVotes($user);
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', 'Nutzer gelöscht');

            return $this->redirectToRoute('all_user');
        }

        return $this->render('user/deleteUser.html.twig', [
            'form' => $deleteForm->createView(),
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
}
