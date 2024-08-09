<?php

/**
 * @copyright   Copyright (C) 2024 Björn Rudner
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     2024-008-09
 *
 * iTop module definition file
 */

SetupWebPage::AddModule(
    __FILE__, // Path to the current file, all other file names are relative to the directory containing this file
    'br-costcenter-bridge-for-br-peripherals/0.1.0',
    array(
        // Identification
        'label' => 'Bridge - Costcenter + Periperals',
        'category' => 'business',

        // Setup
        'dependencies' => array(
            'br-costcenter/0.1.0',
            'br-peripherals/0.1.0',
        ),
        'mandatory' => false,
        'visible' => true,
        'auto_select' => 'SetupInfo::ModuleIsSelected("br-costcenter") && SetupInfo::ModuleIsSelected("br-peripherals")',

        // Components
        'datamodel' => array(
            'model.br-costcenter-bridge-for-br-peripherals.php'
        ),
        'webservice' => array(),
        'dictionary' => array(),
        'data.struct' => array(),
        'data.sample' => array(),

        // Documentation
        'doc.manual_setup' => '',
        'doc.more_information' => '',

        // Default settings
        'settings' => array(),
    )
);
