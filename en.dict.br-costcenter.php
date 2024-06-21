<?php

/**
 * @copyright   Copyright (C) 2024 Björn Rudner
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     2024-06-21
 *
 * Localized data
 */

Dict::Add('EN US', 'English', 'English', array(
    'Menu:CostCenter' => 'Cost Centers',
    'Class:CostCenter' => 'Cost Center',
    'Class:CostCenter+' => '',
    'Class:CostCenter/Attribute:code' => 'Code',
    'Class:CostCenter/Attribute:code+' => '',
    'Class:CostCenter/Attribute:name' => 'Name',
    'Class:CostCenter/Attribute:name+' => '',
    'Class:CostCenter/Attribute:status' => 'Status',
    'Class:CostCenter/Attribute:status+' => '',
    'Class:CostCenter/Attribute:status/Value:active' => 'Active',
    'Class:CostCenter/Attribute:status/Value:active+' => 'Active',
    'Class:CostCenter/Attribute:status/Value:inactive' => 'Inactive',
    'Class:CostCenter/Attribute:status/Value:inactive+' => 'Inactive',
    'Class:CostCenter/Attribute:org_id' => 'Owner organization',
    'Class:CostCenter/Attribute:org_id+' => '',
    'Class:CostCenter/Attribute:org_name' => 'Name of the owner organization',
    'Class:CostCenter/Attribute:org_name+' => '',
    'Class:CostCenter/Attribute:parent_id' => 'Parent',
    'Class:CostCenter/Attribute:parent_id+' => 'Parent cost center',
    'Class:CostCenter/Attribute:parent_name' => 'Parent name',
    'Class:CostCenter/Attribute:parent_name+' => 'Name of the parent cost center',
    'Class:CostCenter/Attribute:parent_id_friendlyname' => 'Parent',
    'Class:CostCenter/Attribute:parent_id_friendlyname+' => 'Parent cost center',
    'Class:CostCenter/UniquenessRule:code_org' => 'The code must be unique',
    'Class:CostCenter/UniquenessRule:code_org+' => 'This cost center already exists',
));
