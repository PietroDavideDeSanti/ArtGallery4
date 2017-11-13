<?php
/**
 * Created by PhpStorm.
 * User: a.salvati
 * Date: 27/10/2017
 * Time: 14:14
 */

namespace CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;


class CoreExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('libraries.yml');
        $loader->load('other.yml');
        $loader->load('parameter.yml');
        $loader->load('services.yml');
    }
}