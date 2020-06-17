<?php

namespace App\Controller\Back;

use App\Form\UserEditYourPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SecurityController
 * @package App\Controller\Back
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/admin/password", name="app_password", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Breadcrumbs $breadcrumbs
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function password(Request $request, EntityManagerInterface $entityManager, Breadcrumbs $breadcrumbs, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Mon Compte", $this->generateUrl('app_password'));
        $breadcrumbs->addItem("Changement de mot de passe");

        $user = $this->getUser();
        $form = $this->createForm(UserEditYourPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($passwordEncoder->isPasswordValid($user, $form["oldpassword"]->getData())) {
                // Ancien mot de passe correct
                $password = $passwordEncoder->encodePassword($user, $form["password"]->getData());
                $user->setPassword($password);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash("success", "Mot de passe modifié");
                return $this->redirectToRoute('app_password');

            } else {
                // Ancien mot de passe incorrect
                $form->get("oldpassword")->addError(new FormError("Ancien mot de passe incorrect."));

                return $this->render('back/security/password.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }

        return $this->render('back/security/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
