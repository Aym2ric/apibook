<?php

namespace App\Controller\Back;

use App\Entity\ImageCropping;
use App\Entity\Realisation;
use App\Filter\RealisationFilterType;
use App\Form\RealisationCreateType;
use App\Form\RealisationEditImageCroppingType;
use App\Form\RealisationEditType;
use App\Form\RealisationImageCroppingType;
use App\Repository\RealisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/admin/realisation")
 */
class RealisationController extends AbstractController
{
    /**
     * @Route("/", name="realisation_index", methods={"GET"})
     * @param Breadcrumbs $breadcrumbs
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     * @param RealisationRepository $realisationRepository
     * @return Response
     */
    public function index(Breadcrumbs $breadcrumbs, PaginatorInterface $paginator, EntityManagerInterface $entityManager, Request $request, FormFactoryInterface $formFactory, RealisationRepository $realisationRepository): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Réalisations", $this->generateUrl('realisation_index'));
        $breadcrumbs->addItem("Liste");

        $isForm = false;

        $form = $formFactory->create(RealisationFilterType::class);
        $queryBuilder = $entityManager->getRepository(Realisation::class)->createQueryBuilder('q');

        if ($request->query->has($form->getName())) {
            // Sauvegarde les champs sélectionnés du formulaire
            $form->submit($request->query->get($form->getName()));

            // Test Titre
            if (!empty($request->query->get("realisation_filter")["titre"])) {
                $queryBuilder->andWhere('q.titre = :titre')
                    ->setParameter("titre", $request->query->get("realisation_filter")["titre"]);
            }

            // Test Description
            if (!empty($request->query->get("realisation_filter")["description"])) {
                $queryBuilder->andWhere('q.description = :description')
                    ->setParameter("description", $request->query->get("realisation_filter")["description"]);
            }

            // Test Url
            if (!empty($request->query->get("realisation_filter")["url"])) {
                $queryBuilder->andWhere('q.url = :url')
                    ->setParameter("url", $request->query->get("realisation_filter")["url"]);
            }

            // On renvoie true pour confirmer a la vue qu'il y a une recherche en cours
            // Afin de déplier automatiquement la box du formulaire
            $isForm = true;
        }

        //var_dump($queryBuilder->getDql());
        $query = $queryBuilder->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('back/realisation/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            'isForm' => $isForm,
        ]);
    }

    /**
     * @Route("/new", name="realisation_new", methods={"GET","POST"})
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     * @param KernelInterface $kernel
     * @return Response
     */
    public function new(Request $request, Breadcrumbs $breadcrumbs, KernelInterface $kernel): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Réalisations", $this->generateUrl('realisation_index'));
        $breadcrumbs->addItem("Créer");

        $realisation = new Realisation();
        $form = $this->createForm(RealisationCreateType::class, $realisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On donne un nom à notre nouvelle image et on upload l'image sur le serveur
            $filename = md5(time() . uniqid()) . ".jpg";
            $realisation->getImageCropping()->setImageName($filename);
            $decoded = base64_decode(str_replace("data:image/png;base64,", "", $realisation->getImageCropping()->getBase64()));
            file_put_contents($kernel->getProjectDir() . "/public/upload/" . $filename, $decoded);
            $realisation->getImageCropping()->setBase64(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($realisation);
            $entityManager->flush();

            $this->addFlash("success", "Réalisation créée");
            return $this->redirectToRoute('realisation_index');
        }

        return $this->render('back/realisation/new.html.twig', [
            'realisation' => $realisation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="realisation_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     * @param Realisation $realisation
     * @param KernelInterface $kernel
     * @return Response
     */
    public function edit(Request $request, Breadcrumbs $breadcrumbs, Realisation $realisation,  KernelInterface $kernel, RealisationRepository $realisationRepository): Response
    {
        $breadcrumbs->addItem("Administration", $this->generateUrl('admin_index'));
        $breadcrumbs->addItem("Réalisations", $this->generateUrl('realisation_index'));
        $breadcrumbs->addItem("Modifier");

        // Informations générales
        $form = $this->createForm(RealisationEditType::class, $realisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "Réalisation modifiée");
            return $this->redirectToRoute('realisation_index');
        }

        // CroppingImage
        $formImageCropping = $this->createForm(RealisationEditImageCroppingType::class, $realisation);
        $formImageCropping->handleRequest($request);
        if ($formImageCropping->isSubmitted() && $formImageCropping->isValid()) {
            // On récupère l'ancien imageName puis on supprime l'ancienne image
            $oldRealisation = $realisationRepository->findOneBy(['id' => $realisation->getId()]);
            try {
                unlink ($kernel->getProjectDir() . "/public/upload/" . $oldRealisation->getImageCropping()->getImageName());
            } catch (\Exception $e) {
                // si l'image n'existe pas on continue tout de meme
            }

            // On donne un nom à notre nouvelle image et on upload l'image sur le serveur
            $filename = md5(time() . uniqid()) . ".jpg";
            $realisation->getImageCropping()->setImageName($filename);
            $decoded = base64_decode(str_replace("data:image/png;base64,", "", $realisation->getImageCropping()->getBase64()));
            file_put_contents($kernel->getProjectDir() . "/public/upload/" . $filename, $decoded);
            $realisation->getImageCropping()->setBase64(null);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "Réalisation modifiée");
            return $this->redirectToRoute('realisation_index');
        }


        return $this->render('back/realisation/edit.html.twig', [
            'realisation' => $realisation,
            'form' => $form->createView(),
            'formImageCropping' => $formImageCropping->createView(),
        ]);
    }

    /**
     * @Route("/delete/ajax/", name="realisation_delete_ajax", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function delete_ajax(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($request->request->get("tab_realisations_id") as $id_realisation) {
            $realisation = $entityManager->getRepository(Realisation::class)->findOneBy(["id" => $id_realisation]);
            $entityManager->remove($realisation);
            $entityManager->flush();
        }

        $this->addFlash("success", "Réalisation(s) supprimée(s)");

        return $this->json(["etat" => true]);
    }

    /**
     * @Route("/{id}", name="realisation_delete", methods={"DELETE"})
     * @param Request $request
     * @param Realisation $realisation
     * @return Response
     */
    public function delete(Request $request, Realisation $realisation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $realisation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($realisation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('realisation_index');
    }
}
