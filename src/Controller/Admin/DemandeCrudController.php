<?php

namespace App\Controller\Admin;

use App\Entity\Demande;
use App\Entity\Category;
use App\Entity\Devis;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class DemandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Demande::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('demande.La demande')
            ->setEntityLabelInPlural('demande.Les demandes')
            ->setSearchFields(['nom', 'category'])
            ->setDefaultSort(['id' => 'ASC'])
        ;
    }

    public function createEntity(string $entityfcqn){
        $demande = new Demande();
        $demande -> setUser ($this ->getUser());
        return $demande;
    } 

    public function configureFields(string $pageName): iterable
    {
        return [
           
            TextField::new('nom')
                ->setLabel('demande.Nom'),
            TextField::new('prenom')
                ->setLabel('demande.Prénom'),
            TextField::new('description')
                ->setLabel('demande.Description'),
            AssociationField::new('category')
                ->setLabel('demande.Catégorie'),
            
            
        ];
    }


     public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('category'))
            ->add(EntityFilter::new('user'))
            
        ;
    }
    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
