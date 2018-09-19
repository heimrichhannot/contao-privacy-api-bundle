<?php

$dca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Fields
 */
$fields = [
    'formHybridApiApp' => [
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['formHybridApiApp'],
        'exclude'                 => true,
        'filter'                  => true,
        'inputType'               => 'select',
        'options_callback' => function (\Contao\DataContainer $dc) {
            return System::getContainer()->get('huh.utils.choice.model_instance')->getCachedChoices([
                'dataContainer' => 'tl_api_app'
            ]);
        },
        'eval'                    => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
        'sql'                     => "int(10) unsigned NOT NULL default '0'"
    ],
];

$dca['fields'] += $fields;