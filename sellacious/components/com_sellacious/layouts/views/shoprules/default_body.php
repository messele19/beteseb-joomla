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

use Joomla\Utilities\ArrayHelper;

$i		= $this->current_item;
$item	= $this->items[$i];

$canCreate	= $this->helper->access->check('shoprule.create');
$canEdit	= $this->helper->access->check('shoprule.edit', $item->id);
$canChange	= $this->helper->access->check('shoprule.edit.state', $item->id);
?>
	<td class="nowrap center">
		<span class="btn-round"><?php echo JHtml::_('jgrid.published', $item->state, $i, 'shoprules.', $canChange, 'cb', $item->publish_up, $item->publish_down);?></span>
	</td>
	<td class="nowrap left">
		<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level - 1) ?>
		<?php if ($canEdit) : ?>
			<a href="<?php echo JRoute::_('index.php?option=com_sellacious&task=shoprule.edit&id='.$item->id);?>">
				<?php echo $this->escape($item->title); ?></a>
		<?php else : ?>
			<?php echo $this->escape($item->title); ?>
		<?php endif; ?>
		<span class="small" title="<?php echo $this->escape($item->path); ?>">
		<?php if (empty($item->note)) : ?>
			<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
		<?php else : ?>
			<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note)); ?>
		<?php endif; ?>
		</span>
	</td>
	<td class="nowrap center">
		<?php echo $this->escape(ArrayHelper::getValue($this->types, $item->type)); ?>
	</td>
