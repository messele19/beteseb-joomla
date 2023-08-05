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
 * Shoprule controller class
 */
class SellaciousControllerShoprule extends SellaciousControllerForm
{
	/**
	 * @var  string  The prefix to use with controller messages.
	 *
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_SELLACIOUS_SHOPRULE';

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array $data An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	protected function allowAdd($data = array())
	{
		if ($this->helper->access->check('shoprule.create'))
		{
			return true;
		}

		$user           = JFactory::getUser();
		$multi_seller   = $this->helper->config->get('multi_seller', 0);
		$default_seller = $this->helper->config->get('default_seller');

		// Default seller can create shoprules if not multi-seller else only set permissions are used
		return !$multi_seller && $default_seller == $user->id;
	}

	/**
	 * Method to check if you can edit an existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data An array of input data.
	 * @param   string $key  The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		if ($this->helper->access->check('shoprule.edit'))
		{
			return true;
		}

		$user           = JFactory::getUser();
		$multi_seller   = $this->helper->config->get('multi_seller', 0);
		$default_seller = $this->helper->config->get('default_seller');

		// Default seller can create shoprules if not multi-seller else only set permissions are used
		return !$multi_seller && $default_seller == $user->id;
	}

	/**
	 * Store post data into state so as to update form accordingly in model's preprocessForm()
	 *
	 * @throws Exception
	 */
	public function setType()
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		$key  = $app->input->getInt('id');
		$data = $app->input->post->get('jform', array(), 'array');

		//Save the data in the session.
		$app->setUserState('com_sellacious.edit.shoprule.data', $data);
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($key), false));
	}
}
