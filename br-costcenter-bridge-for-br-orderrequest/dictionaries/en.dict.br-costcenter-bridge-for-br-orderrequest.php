<?php

/**
 * @copyright   Copyright (C) 2024-2025 Björn Rudner
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     2025-11-12
 *
 * Localized data
 */

/** @disregard P1009 Undefined type Dict */
Dict::Add('EN US', 'English', 'English', array(
    'Class:OrderRequest/Attribute:cost_center' => 'Cost center',
    'Class:OrderRequest/Attribute:cost_center+' => 'Billing cost center for this request.',

    'Class:OrderRequestType/Attribute:default_costcenter_id'   => 'Default cost center',
    'Class:OrderRequestType/Attribute:default_costcenter_id+'  => 'Optional default cost center to prefill on requests.',
));
