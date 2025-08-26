<?php

namespace App\Controller\Admin;

use App\Entity\Devis;
use App\Entity\Facture;
use App\Entity\Prestation;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use App\Repository\DevisRepository;


class DevisCrudController extends AbstractCrudController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager=$entityManager;
    }



    public static function getEntityFqcn(): string
    {
        return Devis::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets

        ->addJsFile('build/admin.js');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Le devis')
            ->setEntityLabelInPlural('Les devis')
            ->setSearchFields(['nom', 'numero'])
            ->setDefaultSort(['id' => 'ASC'])
        ;
    }

    public function createEntity(string $entityFqcn)
    {
        $devis = new Devis();

        // Générer l'année courante
        $year = (new \DateTime())->format('Y');

        // Compter combien de devis existent déjà cette année
        $count = $this->entityManager->getRepository(Devis::class)
            ->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->where('d.numero LIKE :pattern')
            ->setParameter('pattern', 'DEV' . $year . '%')
            ->getQuery()
            ->getSingleScalarResult();

        // Incrémentation avec padding 3 chiffres
        $nextNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
       
        // Créer le numéro
        $devis->setNumero('DEV' . $year . $nextNumber);
        $devis->setStatut("En attente");
        return $devis;
        
    }

    public function configureFields(string $pageName): iterable
    {
        
        return [
        
            IdField::new('id')->onlyOnIndex(),
            DateField::new('date_devis')
                // ->setFormTypeOption('data', new \DateTime())
                ->setFormTypeOption('disabled', true),

            TextField::new('numero')
                ->setFormTypeOption('disabled', true),
            TextField::new('statut')
                ->setFormTypeOption('disabled', true),

            NumberField::new('total')
                ->setLabel('Total (€)'),

            AssociationField::new('demande')->setCrudController(DemandeCrudController::class),
            // AssociationField::new('facture')->setCrudController(FactureCrudController::class),
            CollectionField::new('devisPrestations')
                ->useEntryCrudForm(DevisPrestationCrudController::class)
                ->setLabel('Prestations du devis')
                ->onlyOnForms()
        ];
    }
}
