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
 * Methods supporting a list of Shop rules.
 */
class SellaciousModelShopRules extends SellaciousModelList
{
	/**
	 * Constructor.
	 *
	 * @param  array  $config  An optional associative array of configuration settings.
	 *
	 * @see    JController
	 * @since  1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'parent_id', 'a.parent_id',
				'title', 'a.title',
				'lft', 'a.lft', 'rgt', 'a.rgt',
				'state', 'a.state', 'level', 'a.level',
				'path', 'a.path', 'type', 'a.type',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Pre-process loaded list before returning if needed
	 *
	 * @param   stdClass[]  $items
	 *
	 * @return  stdClass[]
	 */
	protected function processList($items)
	{
		foreach ($items as &$item)
		{
			$item->params = json_decode($item->params, true);
		}

		return $items;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		if (!$this->helper->access->check('shoprule.list'))
		{
			return $query->select('*')->from($db->qn('#__sellacious_shoprules'))->where('0');
		}

		$query->select($this->getState('list.select', 'a.*'))
				->from($db->qn('#__sellacious_shoprules', 'a'))
				->where('a.level > 0');

		$query->select('COUNT(DISTINCT c2.id) AS level')
				->join('LEFT OUTER', $db->qn('#__sellacious_shoprules', 'c2') . ' ON a.lft > c2.lft AND a.rgt < c2.rgt')
				->group('a.id, a.lft, a.rgt, a.parent_id, a.title');

		// Filter by search string
		if ($search = $this->getState('filter.search'))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->q('%' . $db->escape($search, true) . '%');
				$query->where('a.title LIKE ' . $search);
			}
		}

		// Filter on the level.
		if ($level = $this->getState('filter.level'))
		{
			$query->where('a.level <= ' . (int)$level);
		}

		// Filter by published state
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('a.state = ' . (int)$state);
		}
		elseif ($state == '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by shoprule type
		$type = $this->getState('filter.type');

		if ($type)
		{
			$query->where('a.type = ' . $db->q($type));
		}

		$ordering = $this->state->get('list.fullordering', 'a.lft ASC');

		if (trim($ordering))
		{
			$query->order($db->escape($ordering));
		}

		return $query;
	}
}
