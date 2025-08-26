<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class FactureController extends AbstractController
{
    #[Route('/facture', name: 'app_facture')]
    public function facture(): Response
    {
        return $this->render('facture/index.html.twig', [
            'controller_name' => 'FactureController',
        ]);
    }

    #[Route('/from-devis/{id}', name: 'facture_from_devis', methods: ['GET', 'POST'])]
    public function fromDevis(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $devis = $em->getRepository(Devis::class)->find($id);

        if (!$devis) {
            throw $this->createNotFoundException('Devis introuvable');
        }

    // Création d'une nouvelle facture basée sur le devis
    $facture = new Facture();
    $facture->setNumero('FAC-' . uniqid());
    $facture->setMontant($devis->getTotal());
    $facture->setDateEmission(new \DateTime())->setTime(0, 0);
    $facture->setEstPayee(false);
    $facture->setDevis($devis);

    $form = $this->createForm(FactureType::class, $facture);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($facture);
        $em->flush();

        $this->addFlash('success', 'Facture créée depuis le devis avec succès.');
        return $this->redirectToRoute('facture_index');
    }

    return $this->render('facture/new.html.twig', [
        'facture' => $facture,
        'form' => $form->createView(),
        'devis' => $devis
    ]);
}
}
