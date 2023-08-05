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

class SellaciousViewDashboard extends SellaciousView
{
	/**
	 * @var  stdClass[]
	 */
	protected $balances;

	/**
	 * @var  stdClass[]
	 */
	protected $orderStats;

	/**
	 * @var  bool
	 */
	protected $show_banners;

	public function display($tpl = null)
	{
		$this->show_banners = $this->helper->access->check('config.edit') || !$this->helper->access->isSubscribed();

		return parent::display($tpl);
	}
}
