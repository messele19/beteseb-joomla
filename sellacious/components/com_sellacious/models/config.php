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

/**
 * Sellacious model.
 */
class SellaciousModelConfig extends SellaciousModelAdmin
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * @note   Calling getState in this method will result in recursion.
	 *
	 * @param  string $ordering
	 * @param  string $direction
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		$app->getUserStateFromRequest('com_sellacious.config.return', 'return', '', 'cmd');

		parent::populateState();
	}

	/**
	 * Method to save the form data
	 *
	 * @param  array  $data  The form data
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function save($data)
	{
		unset($data['tags']);

		foreach ($data as $name => $params)
		{
			$this->helper->config->save($params, $name);
		}

		return true;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  stdClass
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		// Todo: This should load all configurations which are allowed to edit here
		$params = $this->helper->config->getParams();

		if (!$this->helper->access->isSubscribed())
		{
			$params->set('show_brand_footer', 1);
		}

		$data = (object) array('com_sellacious' => $params->toArray());

		return $data;
	}

	/**
	 * Override preprocessForm to load the sellacious plugin group instead of content.
	 *
	 * @param   JForm   $form
	 * @param   mixed   $data
	 * @param   string  $group
	 *
	 * @return  void
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'sellacious')
	{
		$this->helper->core->loadPlugins();

		$form->loadFile('config/shop');
		$form->loadFile('config/layout');
		$form->loadFile('config/media');
		$form->loadFile('config/registration');
		$form->loadFile('config/seller');
		$form->loadFile('config/shipment');
		$form->loadFile('config/rating');
		$form->loadFile('config/b2b');

		if (!$this->helper->access->isSubscribed())
		{
			$form->setFieldAttribute('backoffice_logo', 'disabled', 'true', 'com_sellacious');
			$form->setFieldAttribute('backoffice_logo', 'limit', '1', 'com_sellacious');
			$form->setFieldAttribute('show_brand_footer', 'readonly', 'true', 'com_sellacious');
			$form->setFieldAttribute('allow_client_authorised_users', 'readonly', 'true', 'com_sellacious');
			$form->setFieldAttribute('allow_credit_limit', 'readonly', 'true', 'com_sellacious');
		}

		parent::preprocessForm($form, $data, $group);
	}
}
