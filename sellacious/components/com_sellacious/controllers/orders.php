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
 * Orders list controller class
 *
 * @since  1.0.0
 */
class SellaciousControllerOrders extends SellaciousControllerBase
{
	/**
	 * @var	 string  The prefix to use with controller messages.
	 *
	 * @since   1.0.0
	 */
	protected $text_prefix = 'COM_SELLACIOUS_ORDERS';

	/**
	 * Load form for the status update of an item in orders list
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function getItemStatusFormAjax()
	{
		$app = JFactory::getApplication();
		$me  = JFactory::getUser();

		try
		{
			if (!JSession::checkToken())
			{
				throw new Exception(JText::_('JINVALID_TOKEN'));
			}

			if ($me->guest)
			{
				throw new Exception(JText::_('COM_SELLACIOUS_ACCESS_NOT_ALLOWED'));
			}

			$post = $app->input->get('jform', array(), 'array');

			$order_id  = ArrayHelper::getValue($post, 'order_id');
			$item_uid  = ArrayHelper::getValue($post, 'item_uid');
			$status_id = ArrayHelper::getValue($post, 'status');

			$status    = $this->helper->order->getStatus($order_id, $item_uid);
			$status_id = $status_id ? $status_id : $status->s_id;

			// We don't want notes to be auto-populated
			$status->notes          = null;
			$status->customer_notes = null;

			$form = $this->helper->order->getStatusForm($status_id, $status);
			$args = array('form' => $form, 'full' => false);
			$html = JLayoutHelper::render('com_sellacious.order.item.status_form', $args);

			$data = array(
				'message' => '',
				'data'    => preg_replace('|[\r\n\t]+|', '', $html),
				'status'  => 1,
			);
		}
		catch (Exception $e)
		{
			$data = array(
				'message' => $e->getMessage(),
				'data'    => null,
				'status'  => 0,
			);
		}

		echo json_encode($data);

		$app->close();
	}

	/**
	 * Load a log of the status updates of an item in orders list
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function getItemStatusLogAjax()
	{
		$app = JFactory::getApplication();
		$me  = JFactory::getUser();

		try
		{
			if (!JSession::checkToken())
			{
				throw new Exception(JText::_('JINVALID_TOKEN'));
			}

			if ($me->guest)
			{
				throw new Exception(JText::_('COM_SELLACIOUS_ACCESS_NOT_ALLOWED'));
			}

			$post = $app->input->get('jform', array(), 'array');

			$order_id = ArrayHelper::getValue($post, 'order_id');
			$item_uid = ArrayHelper::getValue($post, 'item_uid');

			$table = $this->helper->order->getTable('OrderItem');
			$table->load(array('order_id' => $order_id, 'item_uid' => $item_uid));

			$order = $this->helper->order->getItem($order_id);
			$item  = $table->getProperties();
			$log   = $this->helper->order->getStatusLog($order_id, $item_uid);

			$data = array('order' => $order, 'item' => $item, 'log' => $log);
			$html = JLayoutHelper::render('com_sellacious.order.item.status_log', $data);

			$data = array(
				'message' => '',
				'data'    => preg_replace('|[\r\n\t]+|', '', $html),
				'status'  => 1,
			);
		}
		catch (Exception $e)
		{
			ob_end_clean();
			$data = array(
				'message' => $e->getMessage(),
				'data'    => null,
				'status'  => 0,
			);
		}

		echo json_encode($data);

		$app->close();
	}

	/**
	 * Update status information for an order item
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function setItemStatusAjax()
	{
		$app = JFactory::getApplication();
		$me  = JFactory::getUser();

		try
		{
			if (!JSession::checkToken())
			{
				throw new Exception(JText::_('JINVALID_TOKEN'));
			}

			if ($me->guest)
			{
				throw new Exception(JText::_('COM_SELLACIOUS_ACCESS_NOT_ALLOWED'));
			}

			$post = $app->input->post->get('jform', array(), 'array');

			$order_id = ArrayHelper::getValue($post, 'order_id');
			$item_uid = ArrayHelper::getValue($post, 'item_uid');

			$table = $this->helper->order->getTable('OrderItem');
			$table->load(array('order_id' => $order_id, 'item_uid' => $item_uid));

			if (!$order_id || !$item_uid || !$table->get('id'))
			{
				throw new Exception(JText::_($this->text_prefix . '_ORDER_ITEM_INVALID'));
			}

			if ($this->helper->access->check('order.item.edit.status') ||
				($this->helper->access->check('order.item.edit.status.own') && $table->get('seller_uid') == $me->id))
			{
				$this->helper->order->setStatus($post);
				$status = $this->helper->order->getStatus($order_id, $item_uid);

				try
				{
					$dispatcher = JEventDispatcher::getInstance();
					JPluginHelper::importPlugin('sellacious');
					$dispatcher->trigger('onAfterOrderChange', array('com_sellacious.order', $order_id));
				}
				catch (Exception $e)
				{
					// Email sending failed. Ignore for now
				}
			}
			else
			{
				throw new Exception(JText::_('COM_SELLACIOUS_ACCESS_NOT_ALLOWED'));
			}

			$status->next_status = $this->helper->order->getStatuses(null, $status->status, false, true);

			$data = array(
				'message' => JText::_($this->text_prefix . '_SHIPMENT_ORDER_ITEM_UPDATE_SUCCESS'),
				'data'    => $status,
				'status'  => 1,
			);
		}
		catch (Exception $e)
		{
			$data = array(
				'message' => $e->getMessage(),
				'data'    => null,
				'status'  => 0,
			);
		}

		echo json_encode($data);

		$app->close();
	}

	/**
	 * Download the shipment label for the selected order or the items in the order.
	 *
	 * @return  bool
	 *
	 * @since   1.0.0
	 */
	public function getShipmentLabel()
	{
		$app = JFactory::getApplication();
		$me  = JFactory::getUser();

		try
		{
			$this->setRedirect(JRoute::_('index.php?option=com_sellacious&view=orders', false));

			if (!JSession::checkToken('request'))
			{
				throw new Exception(JText::_('JINVALID_TOKEN'));
			}

			if ($me->guest)
			{
				throw new Exception(JText::_('COM_SELLACIOUS_ACCESS_NOT_ALLOWED'));
			}

			$cid = $app->input->post->get('cid', array(), 'array');
			$cid = array_filter(array_map('intval', $cid));
			$cid = reset($cid);

			$oId = $this->helper->order->loadResult(array('list.select' => 'a.id', 'id' => $cid));

			if (!$oId)
			{
				throw new Exception(JText::_($this->text_prefix . '_ORDER_INVALID'));
			}

			// Todo: check more suitable access
			if (!$this->helper->access->checkAny(array('edit', 'edit.own'), 'order.item.'))
			{
				throw new Exception(JText::_('COM_SELLACIOUS_ACCESS_NOT_ALLOWED'));
			}

			$allowAll   = $this->helper->access->check('order.item.edit');
			$labelsPath = $this->helper->shipping->getLabels($oId, $allowAll ? null : $me->id);

			if (!$labelsPath || headers_sent())
			{
				throw new Exception(JText::_($this->text_prefix . '_SHIPMENT_ORDER_LABEL_DOWNLOAD_FAILED'));
			}

			header('Content-type: application/octet-stream');
			header('Content-disposition: attachment; filename="' . basename($labelsPath) . '"');
			readfile($labelsPath);
			$app->close();

			$this->setMessage('Generated ' . $labelsPath, 'success');

			return true;
		}
		catch (Exception $e)
		{
			$this->setMessage($e->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Create export of products
	 *
	 * @return  bool
	 *
	 * @since   1.5.0
	 */
	public function export()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		try
		{
			$app      = JFactory::getApplication();
			$tmpPath  = $app->get('tmp_path');
			$filename = $tmpPath . '/orders-' . JHtml::_('date', 'now', 'Y-m-d-H-i-s-T') . '.csv';

			$dispatcher = $this->helper->core->loadPlugins();
			$dispatcher->trigger('onRequestExport', array('com_sellacious.orders', $filename));

			if (!is_file($filename) || filesize($filename) <= 10)
			{
				throw new Exception(JText::_('COM_SELLACIOUS_EXPORT_NO_HANDLER_FOUND'));
			}

			header('content-type: application/csv');
			header('content-disposition: attachment;filename="' . basename($filename) . '"');
			readfile($filename);

			JLoader::import('joomla.filesytem.file');
			JFile::delete($filename);
			jexit();
		}
		catch (Exception $e)
		{
			$this->setMessage($e->getMessage(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_sellacious&view=orders', false));

			return false;
		}

		return true;
	}
}
