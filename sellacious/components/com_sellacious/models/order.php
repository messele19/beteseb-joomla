<?php
/**
 * @version     1.5.3
 * @package     sellacious
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
// no direct access.
defined('_JEXEC') or die;

/**
 * Sellacious model.
 */
class SellaciousModelOrder extends SellaciousModelAdmin
{
	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array  $data      Data for the form.
	 * @param   bool   $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   12.2
	 */
	public function getForm($data = array(), $loadData = true)
	{
		return false;
	}

	/**
	 * Method to return a single record. Joomla model does not use caching, we use.
	 *
	 * @param   int  $pk  (optional) The record id of desired item.
	 *
	 * @return  JObject
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		if ($item)
		{
			$oid   = $item->get('id');
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('a.*')
				  ->from($db->qn('#__sellacious_order_items', 'a'))
				  ->where('a.order_id = ' . $db->q($oid));

			try
			{
				$db->setQuery($query);
				$products = $db->loadObjectList();

				/** @var  SellaciousTableOrderItem  $table */
				$table = $this->getTable('OrderItem');

				foreach ($products as $product)
				{
					$table->parseJson($product);
				}

				$item->set('items', $products);
			}
			catch (Exception $e)
			{
				$item->set('items', array());

				JLog::add($e->getMessage(), JLog::WARNING, 'jerror');
			}

			$coupon = $this->helper->order->getCoupon($item->get('id'));
			$item->set('coupon', $coupon);

			$keys = array(
				'context'    => 'order',
				'order_id'   => $oid,
				'list.where' => 'a.state > 0',
			);
			$payment = $this->helper->payment->loadObject($keys);

			$item->set('payment', $payment);
		}

		return $item;
	}

	/**
	 * Get status history of the order/items.
	 *
	 * @return  stdClass[]
	 *
	 * @throws  Exception
	 */
	public function getHistory()
	{
		$order_id = (int) $this->getState('order.id');
		$log      = $this->helper->order->getStatusLog($order_id);

		// $html = JLayoutHelper::render('com_sellacious/order/item/status_log', $data);
		return $log;
	}
}
