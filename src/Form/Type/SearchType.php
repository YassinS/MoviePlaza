<?php
namespace App\Form\Type;

use App\Entity\MovieEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType{

     public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('searchTerm', SearchType::class, ["label"=>"Search"]);
    }
}

