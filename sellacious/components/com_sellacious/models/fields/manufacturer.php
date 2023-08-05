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

JFormHelper::loadFieldClass('List');

/**
 * Form Field class for the manufacturers list.
 *
 */
class JFormFieldManufacturer extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var  string
	 */
	protected $type = 'manufacturer';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		$options = array();
		$helper  = SellaciousHelper::getInstance();
		$filters = array('list.select' => 'a.user_id, a.title, a.code', 'state' => 1);
		$items   = $helper->manufacturer->loadObjectList($filters);

		foreach ($items as $item)
		{
			$text      = JText::sprintf(trim($item->code) ? '%s (%s)' : '%s', $item->title, $item->code);
			$options[] = JHtml::_('select.option', $item->user_id, $text, 'value', 'text');
		}

		return array_merge(parent::getOptions(), $options);
	}

}
