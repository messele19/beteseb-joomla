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
 * View class for a list of Sellacious users.
 */
class SellaciousViewUsers extends SellaciousViewList
{
	/** @var  string */
	protected $action_prefix = 'user';

	/** @var  string */
	protected $view_item = 'user';

	/** @var  string */
	protected $view_list = 'users';

	/**
	 * Add the page title and toolbar.
	 *
	 * @since  1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');

		if ($this->helper->access->check($this->action_prefix . '.create'))
		{
			JToolBarHelper::addNew($this->view_item . '.add', 'JTOOLBAR_NEW');
		}

		if (count($this->items))
		{
			if ($this->helper->access->check($this->action_prefix . '.edit') || $this->helper->access->check($this->action_prefix . '.edit.own'))
			{
				JToolBarHelper::editList($this->view_item . '.edit', 'JTOOLBAR_EDIT');
			}

			$filter_state = $state->get('filter.state');

			if ($this->helper->access->check($this->action_prefix . '.edit.state'))
			{
				if (!is_numeric($filter_state) || $filter_state != '1')
				{
					JToolBarHelper::custom($this->view_list . '.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				}

				if (!is_numeric($filter_state) || $filter_state != '0')
				{
					JToolBarHelper::custom($this->view_list . '.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
				}
			}

			// We can allow direct 'delete' implicitly for if so permitted, *warning* User table does not support trash.
			if ($this->helper->access->check($this->action_prefix . '.delete'))
			{
				JToolBarHelper::custom($this->view_list . '.delete', 'delete.png', 'delete.png', 'JTOOLBAR_DELETE', true);
			}
		}

		if ($this->is_nested && $this->helper->access->check('user.list'))
		{
			JToolBarHelper::custom($this->view_list . '.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
		}

		// Let toolbar title use filter
		$uri     = JUri::getInstance();
		$filters = $uri->getVar('filter');

		$filters['profile_type'] = $this->state->get('filter.profile_type');

		$uri->setVar('filter', $filters);

		$this->setPageTitle();

		// As we have utilised the URL parameter of type-filter lets remove it
		unset($filters['profile_type']);

		$uri->setVar('filter', $filters);
	}
}
