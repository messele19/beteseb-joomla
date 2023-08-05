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
 * Methods supporting a list of Sellacious user records.
 */
class SellaciousModelUsers extends SellaciousModelList
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
				'id', 'u.id',
				'name', 'u.name',
				'username', 'u.username',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'sc.title', 'vc.title', 'cc.title', 'mc.title',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$categoryClient   = $this->helper->category->getDefault('client', 'a.id');
		$categoryClientId = $categoryClient ? (int) $categoryClient->id : 0;

		$ccid = 'CASE COALESCE(client.category_id, 0) WHEN 0 THEN ' . $categoryClientId . ' ELSE client.category_id END';

		$query->select('u.id, u.name, u.username, u.email, u.block as state, u.activation')
			  ->from('#__users u')
			  ->group('u.id')

			  ->select('a.id as profile_id, a.mobile, a.website, a.currency, a.ordering')
			  ->select('a.bankinfo, a.taxinfo, a.state as profile_state, a.ordering')
			  ->join('LEFT', '#__sellacious_profiles a ON a.user_id = u.id')

			  ->select($ccid . ' AS client_category_id')
			  ->select('cc.title client_category_name')
			  ->join('LEFT', '#__sellacious_clients client ON client.user_id = u.id')
			  ->join('LEFT', '#__sellacious_categories cc ON cc.id = ' . $ccid)

			  ->select('mfr.category_id AS mfr_category_id, mfr.title AS mfr_company')
			  ->select('mc.title mfr_category_name')
			  ->join('LEFT', '#__sellacious_manufacturers mfr ON mfr.user_id = u.id')
			  ->join('LEFT', '#__sellacious_categories mc ON mc.id = mfr.category_id')

			  ->select('staff.category_id AS staff_category_id')
			  ->select('sc.title staff_category_name')
			  ->join('LEFT', '#__sellacious_staffs staff ON staff.user_id = u.id')
			  ->join('LEFT', '#__sellacious_categories sc ON sc.id = staff.category_id')

			  ->select('seller.category_id AS seller_category_id, seller.title AS seller_company')
			  ->select('vc.title seller_category_name')
			  ->join('LEFT', '#__sellacious_sellers seller ON seller.user_id = u.id')
			  ->join('LEFT', '#__sellacious_categories vc ON vc.id = seller.category_id');

		// Filter over the search string if set.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('u.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->q('%' . $db->escape($search, true) . '%');
				$query->where('(u.name LIKE ' . $search . ' OR u.username LIKE ' . $search . ')');
			}
		}

		// Filter by published state
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('u.block = ' . (int) ($state == 0));
		}

		// Filter by profile_type
		$profile_type = $this->getState('filter.profile_type');

		if (!empty($profile_type) && in_array($profile_type, explode(',', 'client,seller,staff,mfr')))
		{
			$query->where($db->qn($profile_type . '.category_id') . ' != ' . $db->q(''))->where($profile_type . '.state = 1');
		}

		// Filter by category(ies)
		if ($category = (int) $this->getState('filter.category'))
		{
			$cond = array(
				'client.category_id = ' . $category,
				'seller.category_id = ' . $category,
				'staff.category_id = ' . $category,
				'mfr.category_id = ' . $category,
			);

			$query->where('(' . implode(' OR ', $cond) . ')');
		}

		// Add the list ordering clause.
		$ordering = $this->state->get('list.fullordering', 'a.ordering ASC');

		if (trim($ordering))
		{
			$query->order($db->escape($ordering));
		}

		return $query;
	}

	/**
	 * Pre-process loaded list before returning if needed
	 *
	 * @param   stdClass[] $items The items loaded from the database using the list query
	 *
	 * @return  stdClass[]
	 *
	 * @since   1.2.0
	 */
	protected function processList($items)
	{
		foreach ($items as &$item)
		{
			$item->bankinfo = json_decode($item->bankinfo, true) ?: array();
			$item->taxinfo  = json_decode($item->taxinfo, true) ?: array();
		}

		return parent::processList($items);
	}
}
