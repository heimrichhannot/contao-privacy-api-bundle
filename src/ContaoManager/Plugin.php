<?php

namespace HeimrichHannot\PrivacyApiBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use HeimrichHannot\ApiBundle\ContaoApiBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create('HeimrichHannot\PrivacyApiBundle\HeimrichHannotContaoPrivacyApiBundle')
                ->setLoadAfter(['privacy', ContaoApiBundle::class])
        ];
    }
}