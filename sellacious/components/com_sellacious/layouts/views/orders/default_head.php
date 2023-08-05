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

$prefix    = 'COM_SELLACIOUS_ORDERS_HEADING';
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$itemisedShip = $this->helper->config->get('itemised_shipping');
?>
<tr role="row">
	<th class="nowrap hidden-phone" style="width:40px;">
		<?php echo JHtml::_('searchtools.sort', $prefix . '_ID', 'a.id', $listDirn, $listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" style="width:40px;">
		<?php echo JHtml::_('searchtools.sort', $prefix . '_DATE', 'a.created', $listDirn, $listOrder); ?>
	</th>

	<th class="nowrap text-left">
		<?php echo JText::_('COM_SELLACIOUS_ORDER_HEADING_CUSTOMER'); ?>
	</th>

	<th class="nowrap text-left">
		<?php echo JHtml::_('searchtools.sort', $prefix . '_ORDER_NUMBER', 'a.order_number', $listDirn, $listOrder); ?>
	</th>

	<th class="nowrap text-center" style="width:90px;">
		<?php echo JHtml::_('searchtools.sort', $prefix . '_STATUS', 'ss.title', $listDirn, $listOrder); ?>
	</th>

	<th class="nowrap text-center" style="width:90px;">
			<?php echo JHtml::_('searchtools.sort', $prefix . '_PAYMENT_METHOD', 'payment_method', $listDirn, $listOrder); ?>
	</th>

	<?php if (!$itemisedShip): ?>
	<th class="nowrap text-center" style="width:90px;">
		<?php echo JHtml::_('searchtools.sort', $prefix . '_SHIPMENT_METHOD', 'a.shipping_rule', $listDirn, $listOrder); ?>
	</th>

	<th class="nowrap text-center" style="width:90px;">
				<?php echo JHtml::_('searchtools.sort', $prefix . '_SHIPMENT_COST', 'a.product_shipping', $listDirn, $listOrder); ?>
	</th>
	<?php endif; ?>

	<th class="nowrap text-left" style="width:90px;">
		<?php echo JHtml::_('searchtools.sort', $prefix . '_COUPON_AMOUNT', 'cu.amount', $listDirn, $listOrder); ?>
	</th>

	<th class="nowrap text-right" style="width:90px;">
		<?php echo JHtml::_('searchtools.sort', $prefix . '_TAXES', 'a.cart_taxes', $listDirn, $listOrder); ?>
	</th>

	<th class="nowrap text-right" style="width:90px;">
		<?php echo JHtml::_('searchtools.sort', $prefix . '_DISCOUNTS', 'a.cart_discounts', $listDirn, $listOrder); ?>
	</th>

	<th class="nowrap text-right" style="width:90px;">
		<?php echo JHtml::_('searchtools.sort', $prefix . '_TOTAL', 'a.grand_total', $listDirn, $listOrder); ?>
	</th>

	<th style="width: 120px;" class="nowrap">
		<?php echo JText::_('COM_SELLACIOUS_ORDER_HEADING_ACTIONS'); ?>
	</th>
</tr>
