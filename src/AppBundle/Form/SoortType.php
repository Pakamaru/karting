<?php
/**
 * Created by PhpStorm.
 * User: Amaru Signore
 * Date: 21-3-2018
 * Time: 09:54
 */

namespace AppBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SoortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('naam',TextType::class, array('label' => 'Naam'))
            ->add('min_leeftijd', IntegerType::class, array('label' => 'Minimale Leeftijd'))
            ->add('tijdsduur', IntegerType::class, array('label' => 'Tijdsduur'))
            ->add('prijs', MoneyType::class, array('label' => 'Prijs'));
    }
}