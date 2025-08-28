<?php

namespace App\Controller\Admin;

use App\Entity\DevisPrestation;
use App\Entity\Devis;
use App\Entity\Prestation;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;

class DevisPrestationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DevisPrestation::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets

        ->addJsFile('js/admin.js');
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('demande.Le devis avec prestation')
            ->setEntityLabelInPlural('demande.Les devis avec prestations')
            // ->setSearchFields(['nom', 'numero'])
            ->setDefaultSort(['id' => 'ASC'])
        ;
    }
    
    
    public function configureFields(string $pageName): iterable
    {
        $prestationField = AssociationField::new('prestation')
            ->setLabel('demande.Prestation')
            ->setRequired(true)
            ->formatValue(function($value, $entity) {
                return $entity ? $entity->getNom() : '';
            })
            ->setFormTypeOption('choice_attr', function($prestation) {
                return ['data-pu' => $prestation->getPu()];
            });

        $puField = NumberField::new('pu')
            ->setLabel('demande.Prix Unitaire (€)')
            
            ->setFormTypeOption('disabled', true); // lecture seule

        $quantityField = NumberField::new('quantity')
            ->setLabel('demande.Quantité');

        $soustotalField = NumberField::new('soustotal')
            ->setLabel('demande.Sous-total (€)')
            ->setFormTypeOption('disabled', true); // lecture seule

        return [
            IdField::new('id')->onlyOnIndex(),

            $prestationField,
            $quantityField,
            $puField,
            $soustotalField,
        ];
    }
    public function createEntity(string $entityFqcn)
    {
        return new DevisPrestation();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof DevisPrestation) {
            $this->updatePuAndSoustotal($entityInstance);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof DevisPrestation) {
            $this->updatePuAndSoustotal($entityInstance);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function updatePuAndSoustotal(DevisPrestation $devisPrestation)
    {
        $prestation = $devisPrestation->getPrestation();
        if ($prestation) {
            $pu = $prestation->getPu();
            $quantity = $devisPrestation->getQuantity() ?? 0;
            $devisPrestation->setPu($pu);
            $devisPrestation->setSoustotal($pu * $quantity);
        }
    }
    
}
