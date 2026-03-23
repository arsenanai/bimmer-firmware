<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\SoftwareVersion;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class SoftwareVersionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SoftwareVersion::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Software Version')
            ->setEntityLabelInPlural('Software Versions')
            ->setDefaultSort(['name' => 'ASC', 'systemVersionAlt' => 'ASC'])
            ->setSearchFields(['name', 'systemVersion', 'systemVersionAlt']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield ChoiceField::new('name', 'Product Name')
            ->setChoices([
                'MMI Prime CIC' => 'MMI Prime CIC',
                'MMI Prime NBT' => 'MMI Prime NBT',
                'MMI Prime EVO' => 'MMI Prime EVO',
                'MMI PRO CIC' => 'MMI PRO CIC',
                'MMI PRO NBT' => 'MMI PRO NBT',
                'MMI PRO EVO' => 'MMI PRO EVO',
                'LCI MMI Prime CIC' => 'LCI MMI Prime CIC',
                'LCI MMI Prime NBT' => 'LCI MMI Prime NBT',
                'LCI MMI Prime EVO' => 'LCI MMI Prime EVO',
                'LCI MMI PRO CIC' => 'LCI MMI PRO CIC',
                'LCI MMI PRO NBT' => 'LCI MMI PRO NBT',
                'LCI MMI PRO EVO' => 'LCI MMI PRO EVO',
            ])
            ->setHelp('Select the product variant this firmware is for.');
        yield TextField::new('systemVersion', 'Version (display)')
            ->setHelp('Version with v prefix, e.g. "v3.3.7.mmipri.c"');
        yield TextField::new('systemVersionAlt', 'Version (lookup)')
            ->setHelp('Version without v prefix, e.g. "3.3.7.mmipri.c". This is what customers enter.');
        yield UrlField::new('link', 'General Download Link')->setRequired(false);
        yield UrlField::new('st', 'ST Download Link')
            ->setHelp('Download link for ST hardware variant. Leave empty if not applicable.')
            ->setRequired(false);
        yield UrlField::new('gd', 'GD Download Link')
            ->setHelp('Download link for GD hardware variant. Leave empty if not applicable.')
            ->setRequired(false);
        yield BooleanField::new('isLatest', 'Latest Version')
            ->setHelp('Mark as the latest/current version. Only ONE entry per product line should be marked latest.');
        yield TextField::new('latestDisplayVersion', 'Latest Display Version')
            ->setHelp('Only set on "latest" entries. The version shown to users, e.g. "v3.3.7" or "v3.4.4".')
            ->setRequired(false);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('isLatest');
    }
}
