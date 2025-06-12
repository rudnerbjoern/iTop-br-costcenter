<?php

/**
 * @copyright   Copyright (C) 2024-2025 Björn Rudner
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     2025-06-12
 *
 * Localized data
 */

/** @disregard P1009 Undefined type Dict */
Dict::Add('DE DE', 'German', 'Deutsch', array(
    'Menu:CostCenter' => 'Kostenstellen',
    'Menu:CostCenter+' => 'Kostenstellen',
    'Class:CostCenter' => 'Kostenstelle',
    'Class:CostCenter+' => '',
    'Class:CostCenter/Attribute:code' => 'Kennzahl',
    'Class:CostCenter/Attribute:code+' => '',
    'Class:CostCenter/Attribute:name' => 'Name',
    'Class:CostCenter/Attribute:name+' => '',
    'Class:CostCenter/Attribute:status' => 'Status',
    'Class:CostCenter/Attribute:status+' => '',
    'Class:CostCenter/Attribute:status/Value:active' => 'Aktiv',
    'Class:CostCenter/Attribute:status/Value:active+' => 'Aktiv',
    'Class:CostCenter/Attribute:status/Value:inactive' => 'Inaktiv',
    'Class:CostCenter/Attribute:status/Value:inactive+' => 'Inaktiv',
    'Class:CostCenter/Attribute:org_id' => 'Organisation',
    'Class:CostCenter/Attribute:org_id+' => '',
    'Class:CostCenter/Attribute:org_name' => 'Name der Organisation',
    'Class:CostCenter/Attribute:org_name+' => '',
    'Class:CostCenter/Attribute:parent_id' => 'Übergeordnete Kostenstelle',
    'Class:CostCenter/Attribute:parent_id+' => 'Übergeordnete Kostenstelle',
    'Class:CostCenter/Attribute:parent_name' => 'Name der übergeordneten Kostenstelle',
    'Class:CostCenter/Attribute:parent_name+' => 'Name der übergeordneten Kostenstelle',
    'Class:CostCenter/Attribute:parent_id_friendlyname' => 'Parent',
    'Class:CostCenter/Attribute:parent_id_friendlyname+' => '',
    'Class:CostCenter/Attribute:costcenters_list' => 'Kostenstellen',
    'Class:CostCenter/Attribute:costcenters_list+' => 'Untergeordnete Kostenstellen',
    'Class:CostCenter/Attribute:functionalcis_list' => 'CIs',
    'Class:CostCenter/Attribute:functionalcis_list+' => 'Liste der verknüpften CIs',
    'Class:CostCenter/UniquenessRule:code_org' => 'Die Kennzahl muss eindeutig sein',
    'Class:CostCenter/UniquenessRule:code_org+' => 'Eine Kostenstelle mit dieser Kennzahl existiert bereits',
    'Class:FunctionalCI/Attribute:costcenter_id' => 'Kostenstelle',
    'Class:FunctionalCI/Attribute:costcenter_id+' => 'Kostenstelle',
    'Class:FunctionalCI/Attribute:costcenter_name' => 'Name der Kostenstelle',
    'Class:FunctionalCI/Attribute:costcenter_name+' => 'Name der Kostenstelle',
));
