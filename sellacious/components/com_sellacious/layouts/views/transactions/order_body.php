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

/** @var SellaciousViewTransactions $this */
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$me        = JFactory::getUser();

JHtml::_('behavior.framework');
JHtml::_('jquery.framework');

JHtml::_('script', $this->helper->core->getAppPath() . '/templates/sellacious/js/plugin/x-editable/x-editable.min.js', false, false);
JHtml::_('script', 'com_sellacious/view.transactions.js', false, true);
JHtml::_('stylesheet', 'com_sellacious/view.transactions.css', false, true);

JText::script('COM_SELLACIOUS_TRANSACTION_HEADING_STATE_X_', true);
JText::script('COM_SELLACIOUS_TRANSACTION_HEADING_STATE_X_0', true);
JText::script('COM_SELLACIOUS_TRANSACTION_HEADING_STATE_X_1', true);
JText::script('COM_SELLACIOUS_TRANSACTION_HEADING_STATE_X_2', true);
JText::script('COM_SELLACIOUS_TRANSACTION_HEADING_STATE_X_-1', true);
JText::script('COM_SELLACIOUS_TRANSACTION_HEADING_STATE_X_-2', true);

$canChange = $this->helper->access->check('transaction.withdraw.approve');

// For alternate row color fix
echo '<tr></tr>';

foreach ($this->items as $i => $item)
{
	?>
	<tr role="row">
		<td>
			<?php echo $this->escape($item->context_title); ?>
		</td>
		<td class="text-center" style="width:100px;">
			<?php echo $item->order_id ? $item->order_id : '-' ?>
		</td>
		<td class="text-center">
			<?php echo $item->reason ?>
		</td>
		<td class="text-right nowrap" style="width:100px;">
			<?php echo $item->cr_amount == 0 ? null : $this->helper->currency->display($item->cr_amount, $item->currency, null); ?>
		</td>
		<td class="text-right nowrap" style="width:100px;">
			<?php echo $item->dr_amount == 0 ? null : $this->helper->currency->display($item->dr_amount, $item->currency, null); ?>
		</td>
		<td class="text-right nowrap" style="width:100px;">
			<?php echo $this->helper->currency->display($item->balance, $item->currency, null); ?>
		</td>
		<td class="text-center" style="width:120px;">
			<?php echo JHtml::_('date', $item->txn_date, 'M d, Y'); ?>
		</td>
		<td class="text-center nowrap" style="width:100px;">
			<?php if ($canChange && $item->state == SellaciousHelperTransaction::STATE_APPROVAL_HOLD): ?>
				<span class="txn-state-<?php echo $item->state ?>" data-type="txn_status" data-pk="<?php echo $item->id ?>"><?php
					echo JText::plural('COM_SELLACIOUS_TRANSACTION_HEADING_STATE_X', $item->state); ?></span>
			<?php else: ?>
				<?php echo JText::plural('COM_SELLACIOUS_TRANSACTION_HEADING_STATE_X', $item->state); ?>
			<?php endif; ?>
		</td>
		<td>
			<?php echo $item->notes; ?>
		</td>
		<td class="center hidden-phone">
			<?php $invoice = JRoute::_('index.php?option=com_sellacious&view=transaction&layout=receipt&id=' . $item->id); ?>
			<a href="<?php echo $invoice ?>" class="btn btn-xs btn-primary"><?php echo JText::_('COM_SELLACIOUS_TRANSACTION_BUTTON_LABEL_RECEIPT'); ?></a>
		</td>
		<td class="center hidden-phone">
			<span><?php echo (int) $item->id; ?></span>
		</td>
	</tr>
	<?php
}