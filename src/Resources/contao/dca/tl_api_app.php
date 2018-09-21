<?php

$dca = &$GLOBALS['TL_DCA']['tl_api_app'];

$protocolManager = new \HeimrichHannot\Privacy\Manager\ProtocolManager();

\System::loadLanguageFile('tl_module');

/**
 * Callbacks
 */
$dca['config']['onload_callback']['privacyApiBundle'] = ['huh.privacy_api.event_listener.callback_listener', 'modifyDca'];

/**
 * Palettes
 */
$dca['palettes']['__selector__'][] = 'addPrivacyActivationProtocolEntry';
$dca['palettes']['__selector__'][] = 'privacyAddAppCategoryToReferenceEntity';

/**
 * Subpalettes
 */
$dca['subpalettes']['addPrivacyActivationProtocolEntry'] = 'privacyProtocolActivationNotification,privacyProtocolActivationJumpTo';
$dca['subpalettes']['privacyAddAppCategoryToReferenceEntity'] = 'privacyReferenceCategoryField';

/**
 * Fields
 */
$fields = [
    'privacyProtocolArchive'                     => $protocolManager->getArchiveFieldDca(),
    'privacyProtocolEntryType'                   => $protocolManager->getTypeFieldDca(),
    'privacyProtocolDescription'                 => $protocolManager->getDescriptionFieldDca(),
    'privacyProtocolFieldMapping'                => $protocolManager->getTextualFieldMappingFieldDca(),
    'addPrivacyActivationProtocolEntry'          => $protocolManager->getSelectorFieldDca(),
    'privacyProtocolActivationNotification'      => $protocolManager->getNotificationFieldDca(),
    'privacyProtocolActivationJumpTo'            => $protocolManager->getActivationJumpToFieldDca(),
    'privacyAddAppCategoryToReferenceEntity'     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_api_app']['privacyAddAppCategoryToReferenceEntity'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'privacyReferenceCategoryField' => [
        'label'                   => &$GLOBALS['TL_LANG']['tl_api_app']['privacyReferenceCategoryField'],
        'exclude'                 => true,
        'filter'                  => true,
        'inputType'               => 'select',
        'options_callback' => function (\Contao\DataContainer $dc) {
            if (!$dc->activeRecord->privacyProtocolArchive)
            {
                return [];
            }

            if (null === ($protocolArchive = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_privacy_protocol_archive', $dc->activeRecord->privacyProtocolArchive)))
            {
                return [];
            }

            return System::getContainer()->get('huh.utils.choice.field')->getCachedChoices([
                'dataContainer' => $protocolArchive->referenceFieldTable
            ]);
        },
        'eval'                    => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
        'sql'                     => "varchar(64) NOT NULL default ''"
    ],
    'privacyUpdateReferenceEntityFields'         => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['privacyUpdateReferenceEntityFields'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'privacyDeleteReferenceEntityAfterOptAction' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['privacyDeleteReferenceEntityAfterOptAction'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
];

$fields['addPrivacyActivationProtocolEntry']['label'][0]     .= ' (nach Aktivierung)';

$dca['fields'] += $fields;