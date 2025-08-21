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
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;



class DevisCrudController extends AbstractCrudController
{
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

    public function configureFields(string $pageName): iterable
    {
        return [
        
            IdField::new('id')->onlyOnIndex(),
            TextField::new('numero'),
            ChoiceField::new('statut')
                ->setLabel("statut")
                ->setChoices([
                    'En attente' => 'En attente',
                    'Accepter' => 'Accepter',
                    'Refuser' => 'Refuser',
                ]),

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

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ($entityInstance instanceof Devis) {
            $this->recalculateTotal($entityInstance);
        }
        parent::updateEntity($em, $entityInstance);
    }

    private function recalculateTotal(Devis $devis): void
    {
        $total = 0;
        foreach ($devis->getDevisPrestations() as $ligne) {
            $total += (float) $ligne->getSoustotal();
        }
        $devis->setTotal($total);
    }

}
