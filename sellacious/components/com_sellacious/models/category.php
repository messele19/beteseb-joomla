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

use Joomla\Utilities\ArrayHelper;

/**
 * Sellacious Category model.
 *
 * @since   1.0.0
 */
class SellaciousModelCategory extends SellaciousModelAdmin
{
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object $record A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 * @since   1.0.0
	 */
	protected function canDelete($record)
	{
		if ($count = $this->helper->category->countItems($record->id, false))
		{
			$this->setError(JText::sprintf('COM_SELLACIOUS_CATEGORY_HAS_ITEMS_DELETE_NOT_ALLOWED', $record->title, $count));

			return false;
		}

		return $this->helper->access->check('category.delete');
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object $record A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   1.0.0
	 */
	protected function canEditState($record)
	{
		return $this->helper->access->check('category.edit.state');
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0.0
	 */
	public function save($data)
	{
		// Initialise variables
		$app        = JFactory::getApplication();
		$dispatcher = JEventDispatcher::getInstance();

		/** @var SellaciousTableCategory $table */
		$table = $this->getTable();
		$pk    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('sellacious');

		// Load the row if saving an existing category.
		if ($pk > 0)
		{
			$table->load($pk);
			$isNew = false;
		}

		if (isset($data['seller_commission']))
		{
			$sellerCommission = $data['seller_commission'];

			unset($data['seller_commission']);
		}

		// Set the new parent id if parent id not matched OR while New/Save as Copy .
		if ($table->get('parent_id') != $data['parent_id'] || $data['id'] == 0)
		{
			$table->setLocation($data['parent_id'], 'last-child');
		}

		// Alter the title for save as copy
		if ($app->input->get('task') == 'save2copy')
		{
			list($title, $alias) = $this->generateNewTitle($data['parent_id'], $data['alias'], $data['title']);
			$data['title']      = $title;
			$data['alias']      = $alias;
			$data['is_default'] = false;
		}

		// Process images to remove and add
		if (!empty($data['images']))
		{
			$images    = $data['images'];
			$remove_im = array();
			$add_im    = array();

			if (!empty($images['remove']))
			{
				$remove_im = $images['remove'];
			}

			if (!empty($images['add']))
			{
				$add_im = $images['add'];
			}

			$o_images = $table->get('images');
			$o_images = is_array($o_images) ? $o_images : json_decode($o_images);

			$data['images'] = array_values(array_diff(array_merge((array) $o_images, $add_im), $remove_im));

			$this->helper->media->remove($remove_im);
		}

		if (!isset($data['core_fields']))
		{
			$data['core_fields'] = array();
		}

		if (!isset($data['variant_fields']))
		{
			$data['variant_fields'] = array();
		}

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Trigger the onBeforeSave event.
		$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());

			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}

		if (isset($sellerCommission))
		{
			if ($table->get('type') == 'seller')
			{
				$this->helper->category->setSellerCommissionBySellerCategory($table->get('id'), $sellerCommission);
			}
			if (strpos($table->get('type'), 'product/') !== false)
			{
				$this->helper->category->setSellerCommissionByProductCategory($table->get('id'), $sellerCommission);
			}
		}

		// Trigger the onAfterSave event.
		$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));

		// now update concerned users' usergroups according to this category
		$this->helper->user->updateUsersGroupsByCategory($table->get('id'));

		// Rebuild the path for the category
		if (!$table->rebuildPath($table->get('id')))
		{
			$this->setError($table->getError());

			return false;
		}

		// Rebuild the paths of the category's children
		if (!$table->rebuild($table->get('id'), $table->lft, $table->level, $table->get('path')))
		{
			$this->setError($table->getError());

			return false;
		}

		$this->setState($this->getName() . '.id', $table->get('id'));

		return true;
	}

	/**
	 * Method to allow derived classes to preprocess the data.
	 *
	 * @param   string  $context  The context identifier.
	 * @param   mixed   &$data    The data to be processed. It gets altered directly.
	 * @param   string  $group    The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function preprocessData($context, &$data, $group = 'content')
	{
		$app = JFactory::getApplication();

		// Get the dispatcher and load the plugins.
		$dispatcher = $this->helper->core->loadPlugins();

		if (is_array($data))
		{
			$cType = &$data['type'];
		}
		elseif (is_object($data))
		{
			$cType = &$data->type;
		}

		if (empty($cType))
		{
			$filterState = $app->getUserState('com_sellacious.categories.filter', array());
			$cType       = ArrayHelper::getValue($filterState, 'type');
		}

		// Trigger the data preparation event.
		$results = $dispatcher->trigger('onContentPrepareData', array($context, $data));

		// Check for errors encountered while preparing the data.
		if (count($results) > 0 && in_array(false, $results, true))
		{
			$this->setError($dispatcher->getError());
		}

		if (is_object($data))
		{
			// Only modify if object
			if ($cType == 'seller')
			{
				$rates = $this->helper->category->getSellerCommissionsBySellerCategory($data->id);

				$data->seller_commission = $rates;
			}
			elseif (strpos($cType, 'product/') !== false)
			{
				$rates = $this->helper->category->getSellerCommissionsByProductCategory($data->id);

				$data->seller_commission = $rates;
			}

			$data = ArrayHelper::fromObject($data);
		}
	}

	/**
	 * Override preprocessForm to load the sellacious plugin group instead of content.
	 *
	 * @param   JForm   $form   A form object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  Plugin Group
	 *
	 * @return  void
	 * @throws  Exception  If there is an error in the form event.
	 *
	 * @since   1.0.0
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'sellacious')
	{
		$obj = is_array($data) ? ArrayHelper::toObject($data) : $data;

		// prevent root item's parent change
		if (isset($obj->parent_id) && $obj->parent_id == 0 && $obj->id > 0)
		{
			$form->setFieldAttribute('parent_id', 'type', 'hidden');
			$form->setFieldAttribute('parent_id', 'hidden', 'true');
		}

		if (!empty($obj->id))
		{
			$form->setFieldAttribute('type', 'readonly', 'true');
		}

		// Check if a valid category type is selected, and extend form.
		$types = $this->helper->category->getTypes(true);

		if (!empty($obj->type) && array_key_exists($obj->type, $types))
		{
			$form->loadFile('category/' . $obj->type, false);

			if (strpos($obj->type, 'product/') === false || !$this->helper->config->get('product_compare'))
			{
				$form->removeField('compare');

				$form->loadFile('category/params', false);
			}

			if (strpos($obj->type, 'product/') !== false)
			{
				$form->loadFile('category/product');
			}

			if (!$this->helper->config->get('multi_seller'))
			{
				if ($obj->type == 'seller')
				{
					$form->removeField('seller_commission');
				}

				if (strpos($obj->type, 'product/') !== false)
				{
					$form->removeField('seller_commission');
				}

				if ($obj->type == 'staff')
				{
					$form->removeGroup('commission');
				}
			}
		}

		$form->setFieldAttribute('images', 'record_id', $obj->id);

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to set a category as default for its type.
	 *
	 * @param   int  $id  The primary key ID for the category.
	 *
	 * @return  boolean  True if successful.
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	public function setDefault($id)
	{
		// Initialise variables.
		$db = $this->getDbo();

		$category = $this->getTable();

		if (!$category->load((int) $id))
		{
			throw new Exception(JText::_('COM_SELLACIOUS_CATEGORY_NOT_FOUND'));
		}

		// Detect disabled category
		if ($category->get('state') != 1)
		{
			throw new Exception(JText::_('COM_SELLACIOUS_CATEGORY_DISABLED_CANNOT_SET_DEFAULT'));
		}

		// Reset the default fields for the category type.
		$query = $db->getQuery(true);

		$query->update('#__sellacious_categories')
			->set('is_default = 0')
			->where('type = ' . $db->q($category->get('type')));

		$db->setQuery($query);
		$db->execute();

		// Set the new default category.
		$category->set('is_default', 1);
		$category->store();
		$category->store();

		// Clean the cache.
		$this->cleanCache();

		return true;
	}
}
