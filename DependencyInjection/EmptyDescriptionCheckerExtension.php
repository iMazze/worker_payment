<?php
/*
 * This file is part of the EmptyDescriptionCheckerBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\EmptyDescriptionCheckerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EmptyDescriptionCheckerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        try {
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
            $loader->load('services.yaml');
        } catch (\Exception $e) {
            echo '[EmptyDescriptionCheckerExtension] invalid services config found: ' . $e->getMessage();
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('kimai', [
            'permissions' => [
                'roles' => [
                    'ROLE_SUPER_ADMIN' => [
                        'empty_description_checker',
                    ],
                ],
            ],
        ]);
    }
}
