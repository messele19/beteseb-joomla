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

use Joomla\Registry\Registry;

/** @var  SellaciousViewMessages  $this */
$me        = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

JHtml::_('bootstrap.popover', '.hasPopover', array('trigger' => 'hover'));
JHtml::_('stylesheet', 'com_sellacious/component.css', null, true);
JHtml::_('stylesheet', 'com_sellacious/view.messages.css', null, true);

foreach ($this->items as $i => $item)
{
	$canEdit   = $this->helper->access->check('message.reply') ||
		($this->helper->access->check('message.reply.own') && ($item->sender == $me->id || $item->recipient == $me->id));
	$canDelete = $this->helper->access->check('message.delete');
	?>
	<tr role="row">
		<td class="center hidden-phone" style="width:40px">
			<label>
				<input type="checkbox" name="cid[]" id="cb<?php echo $i ?>" class="checkbox style-0"
					   value="<?php echo $item->id ?>" onclick="Joomla.isChecked(this.checked);"
					<?php echo ($canEdit || $canDelete) ? '' : ' disabled="disabled"' ?>/>
				<span></span>
			</label>
		</td>
		<td>
			<?php
			if ($item->sender == $me->id)
			{
				echo '<i class="fa fa-arrow-right txt-color-teal"></i>';
			}
			elseif ($item->recipient == $me->id)
			{
				echo '<i class="fa fa-arrow-left txt-color-red"></i>';
			}
			else
			{
				echo '<i class="fa fa-exchange txt-color-blueDark"></i>';
			}
			?>
		</td>
		<td class="has-hover-zoom">
			<?php
			if ($item->sender == $me->id || $item->sender == 0)
			{
				if ($item->recipient == -1)
				{
					// Todo: expand user ids
					$params     = new Registry($item->params);
					$recipients = (array) $params->get('recipients');
					?>
					<a href="#" class="hover-zoom"><?php echo JText::_('COM_SELLACIOUS_MESSAGE_MULTIPLE_RECIPIENT_LABEL') ?></a>
					<div class="hover-zoom-content recipients-tip">
						<ul>
						<?php
						if (count($recipients))
						{
							?><li><?php echo implode('</li><li>', $recipients) ?></li><?php
						}
						else
						{
							?><li><?php echo JText::_('COM_SELLACIOUS_MESSAGE_MULTIPLE_RECIPIENT_UNKNOWN_LABEL') ?></li><?php
						}
						?>
						</ul>
					</div>
					<?php
				}
				else
				{
					echo $this->escape($item->recipient_name);
				}
			}
			else
			{
				echo $this->escape($item->sender_name);
			}
			?>
		</td>
		<td>
			<?php
			if ($item->context)
			{
				?><label class="label label-success label-tag"><?php echo $item->context ?></label><?php
			}

			if ($canEdit)
			{
				$url = JRoute::_('index.php?option=com_sellacious&task=message.reply&id=' . $item->id);

				?> <a href="<?php echo $url ?>"><?php echo $this->escape($item->title) ?></a><?php
			}
			else
			{
				?><strong> <?php echo $this->escape($item->title); ?></strong><?php
			}

			echo $item->children > 1 ? ' (' . $item->children . ') -' : ' -';

			$html_min = JHtml::_('string.truncate', strip_tags($item->body), 100);
			?>
			<label class="hasPopover" title="Message" data-placement="bottom"
				data-content="<div><?php echo htmlspecialchars($item->body) ?></div>"><?php echo $html_min ?></label>
		</td>
		<td class="nowrap center">
			<?php echo JHtml::_('date', $item->date_sent, 'M dS, Y'); ?>
			<small style="opacity:0.8;"><?php echo JHtml::_('date', $item->date_sent, 'h:i A'); ?></small>
		</td>
		<td class="center hidden-phone">
			<span><?php echo (int) $item->id; ?></span>
		</td>
	</tr>
	<?php
}
