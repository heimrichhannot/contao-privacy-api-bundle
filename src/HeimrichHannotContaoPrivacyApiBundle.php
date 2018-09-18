<?php

namespace HeimrichHannot\PrivacyApiBundle;

use HeimrichHannot\PrivacyApiBundle\DependencyInjection\PrivacyApiExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoPrivacyApiBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new PrivacyApiExtension();
    }
}