<?php

namespace HeimrichHannot\PrivacyApiBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\DataContainer;
use Contao\Model;
use Contao\System;
use HeimrichHannot\Privacy\Model\ProtocolArchiveModel;
use HeimrichHannot\Privacy\Util\ProtocolUtil;

class HookListener
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework
     */
    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    public function addAppCategoriesToModel($jwtData, $module)
    {
        if (null === ($app = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_api_app', $module->formHybridApiApp)))
        {
            return;
        }

        if (($protocolArchive = ProtocolArchiveModel::findByPk($module->formHybridOptInPrivacyProtocolArchive)) === null)
        {
            return null;
        }

        $protocolUtil = new ProtocolUtil();

        if (!$jwtData->submission)
        {
            return;
        }

        $instance = $protocolUtil->findReferenceEntity(
            $protocolArchive->referenceFieldTable,
            $protocolArchive->referenceFieldForeignKey,
            $jwtData->submission->{$protocolArchive->referenceFieldForeignKey}
        );

        if ($instance instanceof Model)
        {
            System::getContainer()->get('huh.privacy_api.util.privacy_api_util')->addAppCategoriesToModel(
                $instance, $app
            );
        }
    }
}