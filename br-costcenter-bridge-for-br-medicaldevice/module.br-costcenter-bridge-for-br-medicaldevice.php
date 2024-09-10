<?php

/**
 * @copyright   Copyright (C) 2024 Björn Rudner
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     2024-09-10
 *
 * iTop module definition file
 */

SetupWebPage::AddModule(
    __FILE__, // Path to the current file, all other file names are relative to the directory containing this file
    'br-costcenter-bridge-for-br-medicaldevice/2.7.4',
    array(
        // Identification
        'label' => 'Bridge - Costcenter + Medicaldevice',
        'category' => 'business',

        // Setup
        'dependencies' => array(
            'br-costcenter/2.7.4',
            'br-costcenter/2.7.4||br-medicaldevice/2.7.9',
        ),
        'mandatory' => false,
        'visible' => true,
        'auto_select' => 'SetupInfo::ModuleIsSelected("br-costcenter") && SetupInfo::ModuleIsSelected("br-medicaldevice")',

        // Components
        'datamodel' => array(
            'model.br-costcenter-bridge-for-br-medicaldevice.php'
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
