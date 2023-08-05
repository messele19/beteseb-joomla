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
 * Sellacious model.
 *
 * @since   1.1.0
 */
class SellaciousModelSplCategory extends SellaciousModelAdmin
{
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 * @since   12.2
	 */
	protected function canDelete($record)
	{
		return $this->helper->access->check('splcategory.delete');
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   12.2
	 */
	protected function canEditState($record)
	{
		return $this->helper->access->check('splcategory.edit.state');
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		// Initialise variables
		$app        = JFactory::getApplication();
		$dispatcher = JEventDispatcher::getInstance();

		/** @var SellaciousTableSplCategory $table */
		$table = $this->getTable();
		$pk    = (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('sellacious');

		// Load the row if saving an existing splcategory.
		if ($pk > 0)
		{
			$table->load($pk);
			$isNew = false;
		}

		// Set the new parent id if parent id not matched OR while New/Save as Copy .
		if ($table->parent_id != $data['parent_id'] || $data['id'] == 0)
		{
			$table->setLocation($data['parent_id'], 'last-child');
		}

		// Alter the title for save as copy
		if ($app->input->get('task') == 'save2copy')
		{
			list($title, $alias) = $this->generateNewTitle($data['parent_id'], $data['alias'], $data['title']);
			$data['title'] = $title;
			$data['alias'] = $alias;

			// User might attempt to clone core items
			$data['is_core'] = 0;
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

			$old_ims        = is_array($table->get('images')) ? $table->get('images') : json_decode($table->get('images'));
			$data['images'] = array_values(array_diff(array_merge((array)$old_ims, $add_im), $remove_im));

			$this->helper->media->remove($remove_im);
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

		// Trigger the onAfterSave event.
		$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));

		// Rebuild the path for the splcategory:
		if (!$table->rebuildPath($table->get('id')))
		{
			$this->setError($table->getError());

			return false;
		}

		// Rebuild the paths of the splcategory's children:
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
	 * @param   string  $context The context identifier.
	 * @param   mixed   &$data   The data to be processed. It gets altered directly.
	 * @param   string  $group   The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	protected function preprocessData($context, &$data, $group = 'content')
	{
		if (is_object($data) && !empty($data->params) && !isset($data->params['styles']))
		{
			$data->params = array('styles' => $data->params);
		}

		parent::preprocessData($context, $data, $group);
	}

	/**
	 * Override preprocessForm to load the sellacious plugin group instead of content.
	 *
	 * @param   JForm   $form   A form object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  Load plugin group
	 *
	 * @return  void
	 *
	 * @throws  Exception if there is an error in the form event.
	 *
	 * @since   1.6
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
			$form->setFieldAttribute('images', 'record_id', $obj->id);
			$form->setFieldAttribute('icon', 'record_id', $obj->id, 'params.badge');
		}

		parent::preprocessForm($form, $data, $group);
	}
}
