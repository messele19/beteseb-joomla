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
 * User controller class.
 */
class SellaciousControllerUser extends SellaciousControllerForm
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 *
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_SELLACIOUS_USER';

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
		return $this->helper->access->check('user.create');
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
		return $this->helper->access->check('user.edit');
	}

	/**
	 * Method to save an address via Ajax request.
	 *
	 * @return  void
	 */
	public function saveAddressAjax()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		/** @var SellaciousModelVariant $model */
		$model = $this->getModel();
		$post  = $this->input->post->get('jform', array(), 'array');
		$app   = JFactory::getApplication();

		try
		{
			if (!$this->allowSave($post['address'], 'user_id'))
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			}

			/** @var JForm $form */
			$form      = JForm::getInstance('com_sellacious.user.address', 'user/address');
			$validData = $model->validate($form, $post);

			if ($validData == false)
			{
				throw new Exception(JText::_($this->text_prefix . '_ADDRESS_SAVE_ERROR_INVALID_DATA', $model->getError()));
			}

			$address = $validData['address'];
			$address = $this->helper->user->saveAddress($address, $address['user_id']);
			$message = JText::_($this->text_prefix . '_ADDRESS_SAVE_SUCCESS_BACKEND');
			$data    = $address->id;
			$state   = 1;
		}
		catch (Exception $e)
		{
			$state   = 0;
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $e->getMessage());
			$data    = null;
		}

		echo json_encode(array('state' => $state, 'message' => $message, 'data' => $data));

		$app->close();
	}

	/**
	 * Method to get an address via Ajax request.
	 *
	 * @return  void
	 */
	public function getAddressAjax()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
		$pk  = $app->input->post->getInt('id');

		try
		{
			// fixme: This state value read here may be unreliable at times, not sure!
			$user_id = $app->getUserState('com_sellacious.edit.user.id');
			$address = $this->helper->user->getAddressById($pk);

			// Make sure edit id matches the selected user.
			if ($pk && (!isset($address) || $address->user_id != $user_id))
			{
				throw new Exception(JText::_('COM_SELLACIOUS_USER_ADDRESS_LOAD_FAILED'));
			}

			$data    = null;
			$state   = 1;
			$message = '';

			if ($address)
			{
				$html       = JLayoutHelper::render('com_sellacious.user.address.row', $address);
				$data       = $address;
				$data->html = preg_replace(array('|[\n\t]|', '|\s+|'), array('', ' '), $html);
			}
		}
		catch (Exception $e)
		{
			$data    = null;
			$state   = 0;
			$message = $e->getMessage();
		}

		echo json_encode(array('state' => $state, 'message' => $message, 'data' => $data));

		$app->close();
	}

	/**
	 * Method to get an address via Ajax request.
	 *
	 * @return  void
	 */
	public function deleteAddressAjax()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
		$pk  = $app->input->post->getInt('id');

		try
		{
			// fixme: This state value read here may be unreliable at times, not sure!
			$user_id = $app->getUserState('com_sellacious.edit.user.id');
			$this->helper->user->removeAddress($pk, $user_id);
			$message = JText::plural($this->text_prefix . '_ADDRESS_REMOVE_SUCCESS_N', 1);
			$state   = 1;
		}
		catch (Exception $e)
		{
			$message = JText::sprintf($this->text_prefix . '_ADDRESS_REMOVE_FAILED', $e->getMessage());
			$state   = 0;
		}

		echo json_encode(array('state' => $state, 'message' => $message, 'data' => null));

		$app->close();
	}

	/**
	 * Get a user object via ajax
	 *
	 * @return  void
	 */
	public function getUserAjax()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();

		try
		{
			// Allow caller to match desired key among: id, email, username
			$key   = $app->input->post->getCmd('key', 'id');
			$value = $app->input->post->getString('value', null);

			/** @var JTableUser $table */
			$table = SellaciousTable::getInstance('User');

			if (!property_exists($table, $key))
			{
				throw new Exception(JText::sprintf($this->text_prefix . '_INVALID_QUERY_KEY_USER_TABLE', $key));
			}

			$item = $this->helper->user->getItem(array($key => $value));

			if ($item->id == 0)
			{
				throw new Exception(JText::sprintf($this->text_prefix . '_RECORD_NO_MATCH', $key));
			}

			$data    = $item;
			$state   = 1;
			$message = '';
		}
		catch (Exception $e)
		{
			$data    = null;
			$state   = 0;
			$message = $e->getMessage();
		}

		echo json_encode(array('state' => $state, 'message' => $message, 'data' => $data));

		$app->close();
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   12.2
	 */
	protected function getRedirectToListAppend()
	{
		$app    = JFactory::getApplication();
		$append = '&filter[profile_type]=';

		$str = $this->option . '.' . $this->view_list . '.filter.profile_type';

		if ($filter_type = $app->getUserState($str))
		{
			$append = $append . $filter_type;
		}

		return $append . parent::getRedirectToListAppend();
	}
}
