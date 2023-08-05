<?php
/**
 * @version     1.5.3
 * @package     sellacious
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     SPL Sellacious Private License; see http://www.sellacious.com/spl.html
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
// no direct access
defined('_JEXEC') or die;

class SellaciousViewActivation extends SellaciousView
{
	/**
	 * @var  Joomla\Registry\Registry
	 */
	protected $item;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed   A string if successful, otherwise a Error object.
	 *
	 * @see     JViewLegacy::loadTemplate()
	 * @since   12.2
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$app->input->set('hidemainmenu', 1);

		$this->item = $this->get('item');

		if (!$this->item->get('license.sitekey'))
		{
			$this->setLayout('register');
		}

		return parent::display($tpl);
	}
}
