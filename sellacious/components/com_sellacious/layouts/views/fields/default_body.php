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

$i    = $this->current_item;
$item = $this->items[$i];

$canCreate = $this->helper->access->check('field.create');
$canEdit   = $this->helper->access->check('field.edit', $item->id);
$canChange = $this->helper->access->check('field.edit.state', $item->id);
?>
<td class="nowrap center">
	<span class="btn-round"><?php echo JHtml::_('jgrid.published', $item->state, $i, 'fields.', $canChange); ?></span>
</td>
<td class="nowrap left">
	<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level - 1) ?>
	<?php if ($canEdit) : ?>
		<a href="<?php echo JRoute::_('index.php?option=com_sellacious&task=field.edit&id=' . $item->id); ?>">
			<?php echo $this->escape($item->title); ?></a>
	<?php else : ?>
		<?php echo $this->escape($item->title); ?>
	<?php endif; ?>
	<span class="small hasTooltip tooltip-left" title="<?php echo $this->escape($item->path); ?>" data-placement="right">
	<?php if (empty($item->note)) : ?>
		<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
	<?php else : ?>
		<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note)); ?>
	<?php endif; ?>
	</span>
</td>
<td class="center">
	<?php
	if (is_array($item->tags))
	{
		foreach ($item->tags as $tag)
		{
			echo ' <label class="label label-info">' . $this->escape($tag->tag_title) . '</label> ';
		}
	}
	?>
</td>
<td class="nowrap center">
	<?php
	$context = JText::_('COM_SELLACIOUS_FIELD_FIELD_CONTEXT_' . strtoupper($item->context));
	echo $this->escape($context);
	?>
</td>
<td class="nowrap center">
	<?php echo $this->escape(ucwords($item->type)); ?>
</td>
