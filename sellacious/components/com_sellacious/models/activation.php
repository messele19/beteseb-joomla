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

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

/**
 * Sellacious model.
 */
class SellaciousModelActivation extends SellaciousModel
{
	/**
	 * Stock method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState()
	{
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  Registry
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		// "name","email","sitekey","version","sitename","siteurl","template"
		$app      = JFactory::getApplication();
		$registry = new Joomla\Registry\Registry();
		$license  = $this->helper->config->get('license', array(), 'sellacious', 'application');

		// Get site template
		$tpl      = array('list.select' => 'a.template, a.title', 'list.from' => '#__template_styles', 'client_id' => '0', 'home' => 1);
		$style    = $this->helper->config->loadObject($tpl);
		$template = is_object($style) ? sprintf('%s (%s)', $style->template, $style->title) : 'NA';

		$registry->set('license', $license);
		$registry->set('sitename', $app->get('sitename'));
		$registry->set('siteurl', trim(JUri::root(), '\\/'));
		$registry->set('version', $this->helper->core->getAppVersion());
		$registry->set('template', $template);

		return $registry;
	}

	/**
	 * Register the copy of this software using the submitted credentials
	 *
	 * @param   array  $data
	 * @param   bool   $activate
	 *
	 * @return  void
	 * @throws  Exception
	 */
	public function register($data, $activate = false)
	{
		$active = $this->helper->config->get('license.activated', null, 'sellacious', 'application');

		if ($activate)
		{
			$data['activated'] = true;
		}

		// If already activated copy, hold registration until its fully reactivated with new data
		if (!$active || $activate)
		{
			$this->helper->config->set('license', $data, 'sellacious', 'application');
		}
	}

	/**
	 * Set the subscription information for this copy of this software from the license server provided data
	 *
	 * @param   array  $data
	 *
	 * @return  void
	 * @throws  Exception
	 */
	public function subscription($data)
	{
		$subs   = ArrayHelper::getValue($data, 'subscription');
		$expiry = ArrayHelper::getValue($data, 'expiry_iso');
		$exp_dt = $expiry ? JFactory::getDate($expiry)->toSql() : '';

		if ($subs)
		{
			$this->helper->config->set('license.subscription', $subs, 'sellacious', 'application');
			$this->helper->config->set('license.expiry_date', $exp_dt, 'sellacious', 'application');
			$this->helper->config->set('license.expiry_iso', $expiry, 'sellacious', 'application');
			$this->helper->config->set('license.expiry_iso', $expiry, 'sellacious', 'application');

			if ($expiry)
			{
				$this->helper->config->set('free_forever', 1, 'sellacious', 'application');
			}
		}
	}
}
