<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use App\Form\ChangePasswordType;
use App\Form\ChangeUsernameType;
use App\Form\DeleteUserType;
use App\Form\SetUserRoleType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 *
 * @Route("/user/", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="all")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @return Response
     */
    public function listAll(): Response
    {
        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->getAllUser();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("{user<\d+>?}/delete", name="delete")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request http request for form
     * @param User    $user    user
     *
     * @return Response
     */
    public function deleteUser(Request $request, User $user): Response
    {
        $deleteForm = $this->createForm(DeleteUserType::class);
        $deleteForm->add('id', HiddenType::class, ['data' => $user->getId()]);
        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted()) {
            $manager = $this->getDoctrine()->getManager();
            $user = $this->getDoctrine()->getRepository(User::class)->find($deleteForm->getData());
            $this->getDoctrine()->getRepository(Vote::class)->deleteVotes($user);
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', 'Nutzer gelöscht');

            return $this->redirectToRoute('user_all');
        }

        return $this->render('user/deleteUser.html.twig', [
            'form' => $deleteForm->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("{user<\d+>?}/edit", name="edit")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param User $user user as parameter
     *
     * @return Response
     */
    public function editUser(User $user): Response
    {
        return $this->render('user/editUser.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("{user<\d+>?}/changeusername", name="change_username")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request http request for form
     * @param User    $user    user as parameter
     *
     * @return Response
     */
    public function changeUsername(Request $request, User $user): Response
    {
        $form = $this->createForm(ChangeUsernameType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();

            $user->setUsername($form->getData()->getUsername());

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Nutzername geändert!');

            return $this->redirectToRoute('user_edit', ['user' => $user->getId()]);
        }

        return $this->render('user/changeUsername.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{user<\d+>?}/changerole", name="change_role")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request http request for form
     * @param User    $user    user as parameter
     *
     * @return Response
     */
    public function changeRole(Request $request, User $user): Response
    {
        $form = $this->createForm(SetUserRoleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $user->setRoles([$form->getData()['roles']]);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Rolle erfolgreich geändert!');

            return $this->redirectToRoute('user_edit', ['user' => $user->getId()]);
        }

        return $this->render('user/changeRole.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{user<\d+>?}/changepassword", name="change_password")
     *
     * @IsGranted("ROLE_USER")
     *
     * @param UserPasswordEncoderInterface $encoder dependency injection
     * @param Request                      $request http request for form
     * @param User                         $user    user as parameter
     *
     * @return Response
     *
     * @TODO Bug fix!!
     */
    public function changePassword(UserPasswordEncoderInterface $encoder, Request $request, User $user): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($encoder->isPasswordValid($user, $form->getData()->getPassword())) {
                $user->setPassword($encoder->encodePassword($user, $form->get('newPassword')->getData()));
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Passwort erfolgreich geändert!');

                return $this->redirectToRoute('user_edit', ['user' => $user->getId()]);
            }
            $form->get('password')->addError(new FormError('Passwort falsch!'));
        }

        return $this->render('user/changePassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
