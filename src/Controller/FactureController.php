<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Facture;
use App\Repository\FactureRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Request;

final class FactureController extends AbstractController
{
    #[Route('/facture', name: 'app_facture')]
    public function facture(
        Request $request,
        FactureRepository $factureRepository
    ): Response {
        $page = $request->query->getInt('page', 1);
        $limit = 10; // Nombre de factures par page

        // On appelle la méthode de notre repository pour récupérer les factures de la page actuelle
        $factures = $factureRepository->findWithPagination($page, $limit);

        // On appelle la méthode de notre repository pour compter toutes les factures
        $totalFactures = $factureRepository->countAll();
        $totalPages = ceil($totalFactures / $limit);

        return $this->render('facture/index.html.twig', [
            'factures' => $factures,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/facture/{id}', name: 'facture_show')]
    public function showFacture(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        $facture = $entityManager->getRepository(Facture::class)->find($id);

        return $this->render('facture/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    #[Route('/facture/pdf/{id}', name: 'facture_pdf')]
    public function exportPdf(
        int $id,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {

        $facture = $entityManager->getRepository(Facture::class)->find($id);
        
        if (!$facture) {
            throw $this->createNotFoundException('Facture non trouvée');
        }
        // Désactive la barre de debug si elle est active
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }

        $logoPath = $request->getSchemeAndHttpHost() . '/images/ordi.PNG';

        $htmlTemplate = $this->renderView('facture/pdf.html.twig', [
            'facture' => $facture,
            'logo_url' => $logoPath,
        ]);
        $htmlTemplate=str_replace("€","&euro;",$htmlTemplate);
        $htmlTemplate=str_replace("É","&Eacute;",$htmlTemplate);
        $htmlTemplate=str_replace("é","&eacute;",$htmlTemplate);
        //Configuration de domppdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(mb_convert_encoding($htmlTemplate,"ISO-8859-1"));
        $dompdf->setPaper('A4', 'portrait');
        try {
            $dompdf->render();
        } catch (\Exception $e) {
            return new Response('<h1>Erreur Dompdf</h1><pre>'.$e->getMessage().'</pre>');
        }

        // Génération du PDF en mémoire
        $pdfOutput = $dompdf->output();
        // Étape 1 : enregistrer le PDF sur le serveur
        $pdfDir = $this->getParameter('kernel.project_dir').'/public/uploads/factures';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }

        $pdfFilename = $facture->getNumero().'.pdf';
        $pdfPath = $pdfDir.'/'.$pdfFilename;
        file_put_contents($pdfPath, $pdfOutput);

        // Étape 2 : enregistrer le chemin dans la base de données
        $facture->setFacture('/uploads/factures/'.$pdfFilename);
        $entityManager->persist($facture);
        $entityManager->flush();

        while (ob_get_level()) {
            ob_end_clean();
        }

        $response = new Response($pdfOutput);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Length', (string) strlen($pdfOutput));
        $response->headers->set('Content-Disposition', 'inline; filename="'.$pdfFilename.'"');
        
        return $response;
    }
}
