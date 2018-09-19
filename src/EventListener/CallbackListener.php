<?php

namespace HeimrichHannot\PrivacyApiBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\DataContainer;
use Contao\System;

class CallbackListener
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
        $this->framework  = $framework;
    }

    public function modifyDca(DataContainer $dc)
    {
        $app   = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_api_app', $dc->id);
        $dca = &$GLOBALS['TL_DCA']['tl_api_app'];

        if (null !== $app)
        {
            if (System::getContainer()->get('huh.utils.string')->startsWith($app->resource, 'privacy_protocol_entry_'))
            {
                // dca

                /**
                 * Palettes
                 */
                $dca['palettes']['resource'] = str_replace('{security', '{privacy_legend},privacyProtocolArchive,privacyProtocolEntryType,privacyProtocolDescription,privacyProtocolFieldMapping,addPrivacyReferenceEntity,privacyUpdateReferenceEntityFields,privacyAddAppCategoryToReferenceEntity,privacyDeleteReferenceEntityAfterOptAction,addPrivacyActivationProtocolEntry;{security', $dca['palettes']['resource']);

                /**
                 * Fields
                 */
                $dca['fields']['resourceActions']['options'] = ['api_resource_create'];

                // lang
                $GLOBALS['TL_LANG']['tl_api_app']['reference']['resourceActions']['api_resource_create'] =
                    $GLOBALS['TL_LANG']['tl_api_app']['reference']['privacyApiBundle']['resourceActions']['api_resource_create'];
            }
        }
    }
}