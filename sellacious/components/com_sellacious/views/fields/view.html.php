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
 * View class for a list of Sellacious.
 */
class SellaciousViewFields extends SellaciousViewList
{
	/** @var  string */
	protected $action_prefix = 'field';

	/** @var  string */
	protected $view_item = 'field';

	/** @var  string */
	protected $view_list = 'fields';

	/** @var  bool */
	protected $is_nested = true;

	/**
	 * Method to preprocess data before rendering the display.
	 *
	 * @return  void
	 */
	protected function prepareDisplay()
	{
		if ($this->filterForm instanceof JForm)
		{
			// Update context for filter group but not for batch group.
			$context = $this->state->get('filter.context', '');
			$this->filterForm->setFieldAttribute('fieldgroup', 'context', $context, 'filter');
		}

		foreach ($this->items as $item)
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}

		parent::prepareDisplay();
	}
}
