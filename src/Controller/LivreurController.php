<?php

namespace App\Controller;

use App\Entity\Livreur;
use App\Form\LivreurType;
use App\Repository\LivreurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/livreur")
 */
class LivreurController extends AbstractController
{
    /**
     * @Route("/", name="livreur_index", methods={"GET"})
     */
    public function index(Request $request,LivreurRepository $livreurRepository,PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $search_input = !empty($request->get('search_input')) ? $request->get('search_input'):'';
        $query = $em->getRepository(Livreur::class)->findBySearchInput($search_input);

     
        $pagination = $paginator->paginate(
            $query,


            $request->query->getInt('page', 1),
            3
        );
        
        return $this->render('livreur/index.html.twig', [
            'livreurs' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="livreur_new", methods={"GET","POST"})
     */
    public function new(Request $request, \Swift_Mailer $mailer): Response
    {
        $livreur = new Livreur();
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($livreur);
            $entityManager->flush();

            $message = (new \Swift_Message('Hello Email'))
            ->setFrom("houcemhamed85@gmail.com")
            ->setTo($livreur->getEmail())
            ->setBody('Operation effectuée avec succès');
            $mailer->send($message);
            
            return $this->redirectToRoute('livreur_index');
        }

        return $this->render('livreur/new.html.twig', [
            'livreur' => $livreur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="livreur_show", methods={"GET"})
     */
    public function show(Livreur $livreur): Response
    {
        return $this->render('livreur/show.html.twig', [
            'livreur' => $livreur,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="livreur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Livreur $livreur): Response
    {
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);
     
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('livreur_index');
        }  
        dump($form->createView());
        dump($livreur);
        return $this->render('livreur/update.html.twig', [
            'livreur' => $livreur,
            'form' => $form->createView(),
        ]);
    }

  

    /**
     * @Route("/delete/{id}", name="livreur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Livreur $livreur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livreur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($livreur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('livreur_index');
    }
}