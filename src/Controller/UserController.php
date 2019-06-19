<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        dump($users);

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @return Response
     * @Route("/deleteuser/{userid<\d+>?}", name="delete_user")
     */
    public function deleteUser($userid): Response
    {
        if($userid !== null)
        {
            $manager = $this->getDoctrine()->getManager();
            $user = $this->getDoctrine()->getRepository(User::class)->find($userid);
            $this->getDoctrine()->getRepository(Vote::class)->deleteVotes($user);
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', 'Nutzer gelöscht');
        }

        return $this->redirectToRoute('all_user');
    }
}
