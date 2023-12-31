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

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$saveOrder = ($listOrder == 'a.ordering' && strtolower($listDirn) == 'asc');
?>
<tr role="row">
	<th class="center" style="width:10px;">
		<label class="checkbox style-0">
			<input type="checkbox" name="checkall-toggle" value="" class="hasTooltip checkbox style-3"
				   title="<?php echo JHtml::tooltipText('JGLOBAL_CHECK_ALL') ?>" onclick="Joomla.checkAll(this);"/>
			<span></span>
		</label>
	</th>
	<th class="nowrap center" width="5%">
		<?php echo JText::_('JSTATUS'); ?>
	</th>
	<th class="nowrap">
		<?php echo JText::_('COM_SELLACIOUS_EMAILTEMPLATES_HEADING_COTEXT'); ?>
	</th>
	<th class="nowrap" width="40%">
		<?php echo JText::_('COM_SELLACIOUS_EMAILTEMPLATES_HEADING_SUBJECT'); ?>
	</th>
	<th class="nowrap hidden-phone" style="width:1%;">
		<?php echo JText::_('JGRID_HEADING_ID'); ?>
	</th>
</tr>
