<?php
/**
 * @version     1.5.3
 * @package     sellacious
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     SPL Sellacious Private License; see http://www.sellacious.com/spl.html
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

/**
 * Sellacious model.
 */
class SellaciousModelProfile extends SellaciousModelAdmin
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
		parent::populateState();

		$app = JFactory::getApplication();
		$uid = JFactory::getUser()->id;

		// This form view is for user's own profile only with restricted access.
		$app->setUserState('com_sellacious.edit.profile.id', $uid);
		$this->state->set('profile.id', $uid);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    Table name
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for table. Optional.
	 *
	 * @return  JTable
	 */
	public function getTable($type = 'User', $prefix = 'SellaciousTable', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @throws  Exception
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserStateFromRequest("$this->option.edit.$this->name.data", 'jform', array(), 'array');

		if (empty($data))
		{
			// Load user info
			$data = $this->getItem();

			// Remove password
			unset($data->password);

			// Add profile info
			$profile = $this->helper->profile->getItem(array('user_id' => $data->get('id')));

			$data->set('profile', $profile);

			$data->custom_profile = $this->helper->field->getValue('profile', $data->get('id'));

			// This is the form data so we only load active records
			$accounts = $this->helper->user->getLinkedAccounts($data->get('id'), true);

			foreach ($accounts as $type => $account)
			{
				// Load seller shippable locations also
				if ($type == 'seller' && !empty($account))
				{
					$account->shipping_geo = $this->helper->seller->getShipLocations($data->get('id'), true);
				}

				$data->set($type, $account);
			}
		}

		$this->preprocessData('com_sellacious.' . $this->name, $data);

		return $data;
	}

	/**
	 * Override preprocessForm to load the sellacious plugin group instead of content.
	 *
	 * @param   JForm   $form   A form object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  Plugin group to load
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'sellacious')
	{
		$obj     = is_array($data) ? ArrayHelper::toObject($data) : $data;
		$user_id = isset($obj->id) ? $obj->id : 0;
		$me      = JFactory::getUser();

		$canEdit = $this->helper->access->check('user.edit.own') && $user_id == $me->id;

		if ($canEdit)
		{
			$categories = array();

			if (!empty($obj->client->category_id))
			{
				$categories[] = $obj->client->category_id;

				$form->loadFile('profile/client');

				$client_id = isset($obj->client->id) ? $obj->client->id : 0;
				$form->setFieldAttribute('org_certificate', 'record_id', $client_id, 'client');

				if (!$this->helper->access->isSubscribed() || !$this->helper->config->get('allow_client_authorised_users'))
				{
					$form->removeField('authorised', 'client');
				}
			}
			else
			{
				$dCat         = $this->helper->category->getDefault('client', 'a.id');
				$categories[] = $dCat ? $dCat->id : 0;
			}

			if (!empty($obj->staff->category_id))
			{
				$categories[] = $obj->staff->category_id;

				$form->loadFile('profile/staff');
			}

			if (!empty($obj->seller->category_id))
			{
				$categories[] = $obj->seller->category_id;

				$form->loadFile('profile/seller');

				$seller_id = isset($obj->seller->id) ? $obj->seller->id : 0;
				$form->setFieldAttribute('logo', 'record_id', $seller_id, 'seller');

				if ($this->helper->config->get('shipped_by') != 'seller')
				{
					$form->removeField('ship_origin_group', 'seller');
					$form->removeField('ship_origin_country', 'seller');
					$form->removeField('ship_origin_state', 'seller');
					$form->removeField('ship_origin_district', 'seller');
					$form->removeField('ship_origin_zip', 'seller');
					// $form->removeGroup('seller.shipping_geo');
				}

				if (!$this->helper->config->get('listing_currency'))
				{
					$form->removeField('currency', 'seller');
				}

				if (!$this->helper->config->get('shippable_location_by_seller'))
				{
					$form->removeGroup('seller.shipping_geo');
				}
			}

			if (!empty($obj->manufacturer->category_id))
			{
				$categories[] = $obj->manufacturer->category_id;

				$form->loadFile('profile/manufacturer', true);

				$mfr_id = isset($obj->manufacturer->id) ? $obj->manufacturer->id : 0;
				$form->setFieldAttribute('logo', 'record_id', $mfr_id, 'manufacturer');
			}

			if (!$this->helper->config->get('user_currency'))
			{
				$form->removeField('currency', 'profile');
			}

			$form->loadFile('profile/address');

			$fieldIds    = $this->helper->category->getFields($categories, array('core'), true);
			$xmlElements = $this->helper->field->getFieldsXML($fieldIds, 'custom_profile');

			foreach ($xmlElements as $xmlElement)
			{
				$form->load($xmlElement);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to return a single record. Joomla model doesn't use caching, we use.
	 *
	 * @param  JObject $item The record item.
	 *
	 * @return  JObject
	 *
	 * @since   1.2.0
	 */
	public function processItem($item)
	{
		if ($user_id = $item->get('id'))
		{
			$item->set('addresses', $this->helper->user->getAddresses($user_id));
		}

		return $item;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  int
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	public function save($data)
	{
		// Extract variables
		$custom       = ArrayHelper::getValue($data, 'custom_profile', null);
		$profile      = ArrayHelper::getValue($data, 'profile', null);
		$manufacturer = ArrayHelper::getValue($data, 'manufacturer', null);
		$seller       = ArrayHelper::getValue($data, 'seller', null);
		$staff        = ArrayHelper::getValue($data, 'staff', null);
		$client       = ArrayHelper::getValue($data, 'client', null);

		unset($data['custom_profile'], $data['profile'], $data['manufacturer'], $data['seller'], $data['staff'], $data['client']);

		$user = $this->saveUser($data);

		if (!($user instanceof JUser))
		{
			return false;
		}

		// Set up profile and all for the user just saved
		$this->helper->user->saveProfile($profile, $user->id);
		$this->helper->user->saveCustomProfile($user->id, (array) $custom);

		// Save manufacturer
		if (!empty($manufacturer['category_id']))
		{
			$manufacturer['user_id'] = $user->id;

			$this->helper->user->addAccount($manufacturer, 'manufacturer');
		}
		else
		{
			// Remove from existing
			$this->helper->user->removeAccount($user->id, 'manufacturer');
		}

		// Save seller
		if (!empty($seller['category_id']))
		{
			$seller['user_id'] = $user->id;

			$locations = ArrayHelper::getValue($seller, 'shipping_geo', array(), 'array');

			foreach ($locations as &$location)
			{
				$location = strlen($location) ? explode(',', $location) : array();
			}

			$locations = array_reduce($locations, 'array_merge', array());

			$this->helper->user->addAccount($seller, 'seller');
			$this->helper->seller->setShipLocations($user->id, $locations);
		}
		else
		{
			// Remove from existing
			$this->helper->user->removeAccount($user->id, 'seller');
			$this->helper->seller->setShipLocations($user->id, array());
		}

		// Save staff
		if (!empty($staff['category_id']))
		{
			$staff['user_id'] = $user->id;

			$this->helper->user->addAccount($staff, 'staff');
		}
		else
		{
			// Remove from existing
			$this->helper->user->removeAccount($user->id, 'staff');
		}

		// Save client
		if (!empty($client['category_id']))
		{
			$client['user_id'] = $user->id;

			$this->helper->user->addAccount($client, 'client');
		}
		else
		{
			// Remove from existing
			$this->helper->user->removeAccount($user->id, 'client');
		}

		return $user->id;
	}

	/**
	 * @param   array  $data  The data to save for related Joomla user account.
	 *
	 * @return  JUser|bool  The user id of the user account on success, false otherwise
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	protected function saveUser($data)
	{
		$pk = !empty($data['id']) ? $data['id'] : (int) $this->getState($this->name . '.id');

		if ($pk == 0)
		{
			$app  = JFactory::getApplication();
			$user = $this->helper->user->autoRegister(new Registry($data));

			// Set global edit id in case rest of the process fails, page should load with new user id
			// Joomla bug in Registry, array key does not update. Fixed in later version of J! 3.4.x
			$state       = $app->getUserState("com_sellacious.edit.$this->name.data");
			$state['id'] = $user->id;

			$this->setState("$this->name.id", $user->id);
			$app->setUserState("com_sellacious.edit.$this->name.data", $state);
			$app->setUserState("com_sellacious.edit.$this->name.id", (int) $user->id);
		}
		else
		{
			$user = JUser::getInstance($data['id']);

			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('sellacious');

			// Bind the data.
			if (!$user->bind($data))
			{
				$this->setError($user->getError());

				return false;
			}

			// Trigger the onAfterSave event.
			$dispatcher->trigger('onBeforeSaveUser', array($this->option . '.' . $this->name, &$user, false));

			// Store the data.
			if (!$user->save())
			{
				$this->setError($user->getError());

				return false;
			}

			// Trigger the onAfterSave event.
			$dispatcher->trigger('onAfterSaveUser', array($this->option . '.' . $this->name, &$user, false));
		}

		return $user;
	}
}
