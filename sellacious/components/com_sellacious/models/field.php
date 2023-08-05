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
 */
class SellaciousModelField extends SellaciousModelAdmin
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
		return $this->helper->access->check('field.delete');
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
		return $this->helper->access->check('field.edit.state');
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		// Initialise variables
		$app        = JFactory::getApplication();
		$dispatcher = JEventDispatcher::getInstance();
		$context    = $this->option . '.' . $this->name;

		/** @var SellaciousTableField $table */
		$table = $this->getTable();
		$pk    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the plugins for the save events.
		JPluginHelper::importPlugin('sellacious');

		// Load the row if saving an existing record.
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
		}

		// Extract tags
		$tags = isset($data['tags']) ? $data['tags'] : array();

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

		// Trigger the before save event.
		$result = $dispatcher->trigger($this->event_before_save, array($context, $table, $isNew));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());

			return false;
		}

		// Reset xml cache if the record is modified
		$table->set('xml_cache', '');

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}

		$this->helper->field->setTags($table->get('id'), $tags);

		// If a group's context is changed we need to update all fields downward.
		$this->inheritFrom($table);

		// Clean the cache.
		$this->cleanCache();

		// Trigger the after save event.
		$dispatcher->trigger($this->event_after_save, array($context, $table, $isNew));

		// Rebuild the path for the category:
		if (!$table->rebuildPath($table->get('id')))
		{
			$this->setError($table->getError());

			return false;
		}

		// Rebuild the paths of the children:
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
	 * @since   3.1
	 */
	protected function preprocessData($context, &$data, $group = 'content')
	{
		// Modify only initial data and not form submitted data
		if (is_object($data) && isset($data->id))
		{
			$data->tags = $this->helper->field->getTags($data->id, true);
		}

		parent::preprocessData($context, $data, $group);
	}

	/**
	 * Method to preprocess the form
	 *
	 * @param   JForm   $form   A form object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws  Exception if there is an error loading the form.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'sellacious')
	{
		$obj = is_array($data) ? ArrayHelper::toObject($data) : $data;

		$form->setFieldAttribute('tags', 'group', 'product/physical;product/electronic');

		// Extend form for non-group type items.
		if (empty($obj->type) || $obj->type == 'fieldgroup')
		{
			$form->loadFile('field_group', false);

			if (empty($obj->context) || $obj->context != 'product')
			{
				$form->removeField('tags');
			}
		}
		else
		{
			$form->loadFile('field_fields', false);

			if (!empty($obj->parent_id))
			{
				$parent = $this->helper->field->getItem($obj->parent_id);

				if ($parent->context != 'product')
				{
					$form->removeField('filterable');
					$form->removeField('tags');
				}
			}
		}

		// Prevent root item's parent change.
		if (isset($obj->parent_id) && $obj->parent_id == 0 && $obj->id > 0)
		{
			$form->setFieldAttribute('parent_id', 'type', 'hidden');
			$form->setFieldAttribute('parent_id', 'hidden', 'true');
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function batch($commands, $pks, $contexts)
	{
		$pks = array_unique($pks);
		$pks = ArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if ($null = array_search(0, $pks, true))
		{
			unset($pks[$null]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));

			return false;
		}

		if (!empty($commands['fieldgroup']) && $group_id = intval($commands['fieldgroup']))
		{
			// Check that the item  exists and is a group.
			/** @var SellaciousTableField $table */
			$table = $this->getTable();

			if (!$table->load($group_id))
			{
				if ($error = $table->getError())
				{
					$this->setError($error);
				}
				else
				{
					$this->setError(JText::_('COM_SELLACIOUS_FIELD_ERROR_BATCH_MOVE_GROUP_NOT_FOUND'));
				}

				return false;
			}
			elseif ($table->get('type') != 'fieldgroup')
			{
				$this->setError(JText::_('COM_SELLACIOUS_FIELD_ERROR_BATCH_MOVE_GROUP_NOT_VALID'));

				return false;
			}

			$cmd = ArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c')
			{
				if (!$this->helper->access->check('field.create'))
				{
					$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));

					return false;
				}

				$result = $this->batchCopy($group_id, $pks, $contexts);

				if (is_array($result))
				{
					$pks = $result;
				}
				else
				{
					return false;
				}
			}
			elseif ($cmd == 'm')
			{

				if (!$this->helper->access->check('field.edit'))
				{
					$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

					return false;
				}

				return $this->batchMove($group_id, $pks, $contexts);
			}
		}
		else
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));

			return false;
		}

		// Rebuild the nested structure.
		$table->rebuild();

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Batch move fields to a new group or current.
	 *
	 * @param   integer  $value     The new category.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since   11.1
	 */
	protected function batchMove($value, $pks, $contexts)
	{
		while (!empty($pks))
		{
			/** @var SellaciousTableField $table */
			$table = $this->getTable();

			// Pop the next ID off the stack
			$pk = array_shift($pks);

			// Check that the row actually exists
			if (!$table->load($pk))
			{
				if ($error = $table->getError())
				{
					$this->setError($error);

					return false;
				}
				else
				{
					$this->setError(JText::sprintf('COM_SELLACIOUS_FIELD_ERROR_BATCH_MOVE_FIELD_NOT_FOUND', $pk));
					continue;
				}
			}
			elseif ($table->get('type') == 'fieldgroup')
			{
				$this->setError(JText::_('COM_SELLACIOUS_FIELD_ERROR_BATCH_MOVE_CANT_MOVE_GROUP'));
				continue;
			}

			// Alter the title & alias
			$data         = $this->generateNewTitle($value, $table->alias, $table->get('name'));
			$table->title = $data[0];
			$table->alias = $data[1];

			// New category ID
			$table->parent_id = $value;

			// Set the new location in the tree for the node.
			$table->setLocation($value, 'last-child');

			// Check the row.
			if (!$table->check())
			{
				$this->setError($table->getError());

				return false;
			}

			// Store the row.
			if (!$table->store())
			{
				$this->setError($table->getError());

				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Batch copy items to a new category or current.
	 *
	 * @param   integer  $value     The new category.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since   11.1
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		if (!$this->helper->access->check('field.create'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));

			return false;
		}

		$newIds = array();

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			/** @var SellaciousTableField $table */
			$table = $this->getTable();

			// Pop the first ID off the stack
			$pk = array_shift($pks);

			// Check that the row actually exists
			if (!$table->load($pk))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('COM_SELLACIOUS_FIELD_ERROR_BATCH_MOVE_FIELD_NOT_FOUND', $pk));

					continue;
				}
			}
			elseif ($table->get('type') == 'fieldgroup')
			{
				$this->setError(JText::_('COM_SELLACIOUS_FIELD_ERROR_BATCH_MOVE_CANT_MOVE_GROUP'));

				continue;
			}

			// Alter the title & alias
			$data         = $this->generateNewTitle($value, $table->alias, $table->get('title'));
			$table->title = $data[0];
			$table->alias = $data[1];

			// Reset the ID because we are making a copy
			$table->id = 0;

			// New category ID
			$table->parent_id = $value;

			// Set the new location in the tree for the node.
			$table->setLocation($value, 'last-child');

			// Check the row.
			if (!$table->check())
			{
				$this->setError($table->getError());

				return false;
			}

			// Store the row.
			if (!$table->store())
			{
				$this->setError($table->getError());

				return false;
			}

			// Get the new item ID
			$newId = $table->get('id');

			// Add the new ID to the array
			$newIds[] = $newId;
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Inherit field context value from the selected group
	 *
	 * @param  SellaciousTableField $table
	 *
	 * @return  void
	 */
	private function inheritFrom($table)
	{
		if ($table->get('type') == 'fieldgroup')
		{
			$db    = $this->_db;
			$query = $db->getQuery(true);

			$query->update($table->getTableName())
				->set('context = ' . $db->q($table->get('context')))
				->where('parent_id = ' . $db->q($table->get('id')));

			$db->setQuery($query);
			$db->execute();
		}
	}
}
