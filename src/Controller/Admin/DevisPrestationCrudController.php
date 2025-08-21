<?php

namespace App\Controller\Admin;

use App\Entity\DevisPrestation;
use App\Entity\Devis;
use App\Entity\Prestation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class DevisPrestationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DevisPrestation::class;
    }
    public static function getEntityFqc(): string
    {
        return Devis::class;
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Le devis avec prestatios')
            ->setEntityLabelInPlural('Les devis avec prestations')
            // ->setSearchFields(['nom', 'numero'])
            ->setDefaultSort(['id' => 'ASC'])
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('prestation'),
            TextField::new('soustotal'),
            
        ];
    }
    
}
