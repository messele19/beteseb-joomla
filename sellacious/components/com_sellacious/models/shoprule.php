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
 * Sellacious Shop rule model
 */
class SellaciousModelShoprule extends SellaciousModelAdmin
{
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object $record A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 * @since   12.2
	 */
	protected function canDelete($record)
	{
		return $this->helper->access->check('shoprule.delete');
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object $record A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   12.2
	 */
	protected function canEditState($record)
	{
		return $this->helper->access->check('shoprule.edit.state');
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array $data  The form data.
	 *
	 * @return  boolean  True on success.
	 * @since   1.6
	 */
	public function save($data)
	{
		$app        = JFactory::getApplication();
		$dispatcher = JEventDispatcher::getInstance();

		/** @var SellaciousTableShopRule $table */
		$table = $this->getTable();
		$pk    = (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the plugins for the on save events.
		JPluginHelper::importPlugin('sellacious');

		// Load the row if saving an existing category.
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

		// Bind the data.
		$table->bind($data);

		// Check the data.
		$table->check();

		// Trigger the onBeforeSave event.
		$context = $this->option . '.' . $this->name;
		$dispatcher->trigger($this->event_before_save, array($context, &$table, $isNew));

		// Store the data.
		$table->store();

		// Trigger the onAfterSave event.
		$dispatcher->trigger($this->event_after_save, array($context, &$table, $isNew));

		// Rebuild the path for the shoprule:
		$table->rebuildPath($table->get('id'));

		// Rebuild the paths of the shop-rule's children
		$table->rebuild($table->get('id'), $table->lft, $table->level, $table->get('path'));

		$this->setState($this->getName() . '.id', $table->get('id'));

		return true;
	}

	/**
	 * Override preprocessForm to load the sellacious plugin group instead of content.
	 *
	 * @param  JForm $form   A form object.
	 * @param  mixed $data   The data expected for the form.
	 * @param string $group  Plugin group to load
	 *
	 * @since   1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		$this->helper->core->loadPlugins('sellaciousrules');

		$obj = is_array($data) ? ArrayHelper::toObject($data) : $data;

		if (isset($obj->parent_id) && $obj->parent_id == 0 && $obj->id > 0)
		{
			$form->setFieldAttribute('parent_id', 'type', 'hidden');
			$form->setFieldAttribute('parent_id', 'hidden', 'true');
		}

		if (!empty($obj->id))
		{
			$form->setFieldAttribute('type', 'readonly', 'true');
		}

		parent::preprocessForm($form, $data, $group);
	}
}
