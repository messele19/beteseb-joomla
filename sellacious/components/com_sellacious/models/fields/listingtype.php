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

JFormHelper::loadFieldClass('Radio');

/**
 * Form Field class.
 */
class JFormFieldListingType extends JFormFieldRadio
{
	/**
	 * The field type.
	 *
	 * @var  string
	 */
	protected $type = 'ListingType';

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
		$values  = array();
		$helper  = SellaciousHelper::getInstance();
		$allow   = (array) $helper->config->get('allowed_listing_type', array());
		$allow   = $allow ?: array(1, 2, 3);

		if (in_array(1, $allow))
		{
			$values[]  = 1;
			$options[] = JHtml::_('select.option', 1, 'COM_SELLACIOUS_PRODUCT_FIELD_LISTING_TYPE_NEW');
		}

		if (in_array(2, $allow))
		{
			$values[]  = 2;
			$options[] = JHtml::_('select.option', 2, 'COM_SELLACIOUS_PRODUCT_FIELD_LISTING_TYPE_USED');
		}

		if (in_array(3, $allow))
		{
			$values[]  = 3;
			$options[] = JHtml::_('select.option', 3, 'COM_SELLACIOUS_PRODUCT_FIELD_LISTING_TYPE_REFURBISHED');
		}

		// If selected value is not enabled change to one of the available one
		if (!in_array($this->value, $values))
		{
			$this->value = reset($values);
		}

		return array_merge(parent::getOptions(), $options);
	}
}
