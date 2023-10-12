<?php

namespace App\Controller\Admin;

use App\Entity\Usr;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UsrCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Usr::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nickname'),
            EmailField::new('email'),
            TextField::new('password')->setFormType(PasswordType::class),
            ArrayField::new('roles'),
            DateField::new('birthday'),
            BooleanField::new('isVerified')

            // Add other fields as needed
        ];
    }

}
