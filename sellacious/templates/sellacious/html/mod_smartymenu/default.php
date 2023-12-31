<?php
/**
 * @version     1.5.3
 * @package     sellacious
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     SPL Sellacious Private License; see http://www.sellacious.com/spl.html
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
// no direct access.
defined('_JEXEC') or die;

$document   = JFactory::getDocument();
$direction  = $document->direction == 'rtl' ? 'pull-right' : '';
$layoutPath = JModuleHelper::getLayoutPath('mod_smartymenu', $enabled ? 'default_enabled' : 'default_disabled');

require $layoutPath;

$menu->renderMenu('smartymenu', $enabled ? ' ' . $direction : 'nav disabled ' . $direction);
