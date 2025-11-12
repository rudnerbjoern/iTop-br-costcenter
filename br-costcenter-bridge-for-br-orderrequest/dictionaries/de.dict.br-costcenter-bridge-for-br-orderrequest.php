<?php

/**
 * @copyright   Copyright (C) 2024-2025 Björn Rudner
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     2025-11-12
 *
 * Localized data
 */

/** @disregard P1009 Undefined type Dict */
Dict::Add('DE DE', 'German', 'Deutsch', array(
    'Class:OrderRequest/Attribute:cost_center' => 'Kostenstelle',
    'Class:OrderRequest/Attribute:cost_center+' => 'Verrechnete Kostenstelle.',

    'Class:OrderRequestType/Attribute:default_costcenter_id'   => 'Standard-Kostenstelle',
    'Class:OrderRequestType/Attribute:default_costcenter_id+'  => 'Optionale Standard-Kostenstelle zur Vorbelegung in Anfragen.',
));
