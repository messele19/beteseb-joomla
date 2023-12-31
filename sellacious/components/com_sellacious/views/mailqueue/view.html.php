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

/**
 * View class for a list of Sellacious.
 */
class SellaciousViewMailQueue extends SellaciousViewList
{
	/** @var  string */
	protected $action_prefix = 'mailqueue';

	/** @var  string */
	protected $view_item = 'mailqueue';

	/** @var  string */
	protected $view_list = 'mailqueue';

	/**
	 * Add the page title and toolbar.
	 *
	 * @since  1.6
	 */
	protected function addToolbar()
	{
		$this->setPageTitle();
	}
}
