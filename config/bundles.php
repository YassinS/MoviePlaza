<?php
use CustomerManagementFrameworkBundle\PimcoreCustomerManagementFrameworkBundle;
use Pimcore\Bundle\ObjectMergerBundle\ObjectMergerBundle;

return [
    //Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    PimcoreCustomerManagementFrameworkBundle::class => ['all' => true],
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
    Pimcore\Bundle\TinymceBundle\PimcoreTinymceBundle::class => ['all' => true],
    Pimcore\Bundle\ApplicationLoggerBundle\PimcoreApplicationLoggerBundle::class => ['all' => true],
    ObjectMergerBundle::class => ['all' => true],
];
