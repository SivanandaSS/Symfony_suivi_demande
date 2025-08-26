<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Facture;
use Dompdf\Dompdf;
use Dompdf\Options;

final class FactureController extends AbstractController
{
    #[Route('/facture', name: 'app_facture')]
    public function facture(
        EntityManagerInterface $entityManager
    ): Response
    {
        $factures = $entityManager->getRepository(Facture::class)->findAll();

        return $this->render('facture/index.html.twig', [
            'factures' => $factures,
        ]);
    }

    #[Route('/facture/{id}', name: 'facture_show')]
    public function showFacture(
        int $id,
        EntityManagerInterface $entityManager
    ): Response
    {
        $facture = $entityManager->getRepository(Facture::class)->find($id);

        return $this->render('facture/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    #[Route('/facture/pdf/{id}', name: 'facture_pdf')]
    public function exportPdf(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {

        $facture = $entityManager->getRepository(Facture::class)->find($id);
        
        if (!$facture) {
            throw $this->createNotFoundException('Facture non trouvée');
        }
        // Désactive la barre de debug si elle est active
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }

        $htmlTemplate= $this->renderView('facture/pdf.html.twig', [
            'facture' => $facture,
        ]);

        //Configuration de domppdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->setIsHtml5ParserEnabled(true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlTemplate);
        $dompdf->setPaper('A4', 'portrait');
        try {
            $dompdf->render();
        } catch (\Exception $e) {
            return new Response('<h1>Erreur Dompdf</h1><pre>'.$e->getMessage().'</pre>');
        }

        // Génération du PDF en mémoire
        $pdfOutput = $dompdf->output();

        while (ob_get_level()) {
            ob_end_clean();
        }
        $response = new Response($pdfOutput);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Length', (string) strlen($pdfOutput));
        $response->headers->set('Content-Disposition', 'attachment; filename="facture_'.$facture->getNumero().'.pdf"');
        
        return $response;
}
}
