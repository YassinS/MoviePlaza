<?php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType{

     public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('query', TextType::class, ["label"=>"Search"]);
    }
}

