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

/** @var  SellaciousViewProducts $this */
JHtml::_('behavior.framework');
JHtml::_('jquery.framework');

JHtml::_('stylesheet', 'com_sellacious/view.products.css', null, true);
JHtml::_('script', 'com_sellacious/view.products.js', false, true);

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$ordering   = ($listOrder == 'a.ordering');
$saveOrder  = ($listOrder == 'a.ordering' && strtolower($listDirn) == 'asc');
$c_currency = $this->helper->currency->current('code_3');
$g_currency = $this->helper->currency->getGlobal('code_3');

$filter        = array('list.select' => 'a.id, a.title', 'list.where' => array('a.state = 1', 'a.level > 0'), 'list.order' => 'a.lft');
$splCategories = $this->helper->splCategory->loadObjectList($filter);
$multi_seller  = $this->helper->config->get('multi_seller', 0);
$free_listing  = $this->helper->config->get('free_listing');

$stock_in_catalogue   = $this->helper->config->get('show_stock_in_catalogue', 1);
$ratings_in_catalogue = $this->helper->config->get('show_ratings_in_catalogue', 1);
$orders_in_catalogue  = $this->helper->config->get('show_orders_in_catalogue', 1);

$me    = JFactory::getUser();
$icons = array(
	'physical'   => 'fa fa-cube',
	'electronic' => 'fa fa-download',
	'package'    => 'fa fa-cubes',
);

foreach ($this->items as $i => $item)
{
	$isOwn = $item->owned_by == $me->id || $item->seller_uid == $me->id;
	$e4all = $this->helper->access->checkAny(array('basic', 'seller', 'pricing', 'shipping', 'related', 'seo'), 'product.edit.', $item->id);
	$e4own = $this->helper->access->checkAny(array('basic.own', 'seller.own', 'pricing.own', 'shipping.own', 'related.own', 'seo.own'), 'product.edit.', $item->id);

	$canEdit   = $e4all || ($e4own && $isOwn);
	$canChange = $this->helper->access->check('product.edit.state', $item->id);

	$code      = $this->helper->product->getCode($item->id, 0, $item->seller_uid);
	$image_url = $this->helper->media->getImage('products', $item->id, true);
	$edit_url  = JRoute::_('index.php?option=com_sellacious&task=product.edit&id=' . $item->id);

	/** @note  JRoute::link() is not available until Joomla 3.9 or later */
	$site_url  = 'index.php?option=com_sellacious&view=product&p=' . $code;

	// Site route will be available if we could use JRoute::link, use 'isset' to test if we have it.
	if (is_callable(array('JRoute', 'link'))):
		$siteRoute = call_user_func_array(array('JRoute', 'link'), array('site', $site_url));
	else:
		$site_url  = trim(JUri::root(), '/') . '/' . $site_url;
	endif;
	?>
	<tr role="row">
		<td class="nowrap center hidden-phone">
			<?php /* Any method using product_id:seller_uid can use this value. This is temporary workaround and should be improved */ ?>
			<label><input type="checkbox" name="cid[]" id="cb<?php echo $i ?>" class="checkbox style-0"
						  value="<?php echo $item->id ?>:<?php echo $item->seller_uid ?>" onclick="Joomla.isChecked(this.checked);"
					<?php echo ($canEdit || $canChange) ? '' : ' disabled="disabled"' ?>/>
				<span></span></label>
		</td>
		<td class="nowrap center">
			<span class="btn-round"><?php
				echo JHtml::_('jgrid.published', $item->state, $i, 'products.', $canChange); ?></span>
		</td>
		<td style="width:50px; padding:1px;" class="image-box">
			<img class="image-large" src="<?php echo $image_url; ?>"/>
			<img class="image-small" src="<?php echo $image_url; ?>"/>
		</td>
		<td>
			<span class="txt-color-red">&nbsp;<i class="<?php echo ArrayHelper::getValue($icons, $item->type, 'fa fa-cube') ?>"></i>&nbsp;</span>
			<?php echo $canEdit ? JHtml::link($edit_url, $this->escape($item->title)) : $this->escape($item->title); ?> (<?php echo $code; ?>)
			<span class="txt-color-red">&nbsp;<a target="_blank" class="hasTooltip" data-placement="right"
				 	title="<?php echo JText::_('COM_SELLACIOUS_PRODUCT_LINK_FRONTEND_TIP'); ?>"
				 	href="<?php echo isset($siteRoute) ? $siteRoute : $site_url; ?>"><i class="fa fa-external-link-square"></i></a>&nbsp;</span>
			<?php if (!empty($item->variant_count)): ?>
				<span class="badge badge-xs badge-important hasTooltip" data-placement="right" style="margin-top: -2px;"
					  title="<?php echo JText::plural('COM_SELLACIOUS_PRODUCT_VARIANT_COUNT_TIP', $item->variant_count); ?>"><?php echo $item->variant_count ?></span>
			<?php endif; ?>
			<br/>
			<span>
				<?php
				if ($sku = trim($item->local_sku)):
					echo JText::_('COM_SELLACIOUS_PRODUCT_HEADING_SKU');
					echo JText::sprintf(': <b>%s</b>', $this->escape($sku));
					echo '<br>';
				endif;
				?>
				<?php echo JText::_('COM_SELLACIOUS_PRODUCT_HEADING_CATEGORY') ?>:
				<?php foreach ($item->category_titles as $categoryTitle): ?>
					<label class="label capsule text-normal"><?php echo $categoryTitle ?> </label>
				<?php endforeach; ?>
			</span>
		</td>
		<?php if (count($splCategories)): ?>
			<td class="text-center nowrap" style="width: 60px; white-space: nowrap;">
				<div class="btn-group btn-spl-listing" style="width: 60px; white-space: nowrap;">
					<?php
					foreach ($splCategories as $splCategory)
					{
						$active = in_array($splCategory->id, $item->spl_categories);
						$enable = $item->id && $splCategory->id && $item->seller_uid ? '' : ' disabled ';
						?>
						<button type="button" class="btn btn-xs hasTooltip <?php echo $active ? 'active btn-danger' : 'btn-default' ?>"
								title="<?php echo $this->escape($splCategory->title) ?>"
								data-id="<?php echo $item->id ?>"
								data-catid="<?php echo $splCategory->id ?>"
								data-seller_uid="<?php echo $item->seller_uid ?>" <?php echo $enable ?>><?php
							echo strtoupper(substr($splCategory->title, 0, 1)) ?></button><?php
					}
					?>
				</div>
			</td>
		<?php endif; ?>
		<?php if ($multi_seller): ?>
			<td class="nowrap">
				<span>
				<?php if ($item->seller_active): ?>
					<i class="fa fa-check txt-color-blue hasTooltip" title="<?php echo JText::sprintf('COM_SELLACIOUS_PRODUCT_SELLER_ACTIVE_TIP') ?>"></i>
				<?php else: ?>
					<i class="fa fa-times txt-color-orange hasTooltip" title="<?php echo JText::sprintf('COM_SELLACIOUS_PRODUCT_SELLER_INACTIVE_TIP') ?>"></i>
				<?php endif; ?>
				</span>
				<?php
					$sold_by = $item->seller_store ? : ($item->seller_name ? : ($item->seller_company ? : $item->seller_username));
					echo $this->escape($sold_by);
				?>
			</td>
		<?php endif; ?>
		<?php
		if ($item->price_display == SellaciousHelperPrice::PRICE_DISPLAY_DEFINED)
		{
			?>
			<td class="nowrap text-right">
				<?php if ($item->multi_price): ?>
					<span class="badge badge-xs badge-info pull-left"><?php echo $item->multi_price ?></span>
				<?php endif; ?>
				<?php echo $this->helper->currency->display($item->sales_price, $item->seller_currency, null, false); ?><br>
				<?php if ($item->seller_currency != $c_currency): ?>
				<small><?php echo $this->helper->currency->display($item->sales_price, (string) $item->seller_currency, $c_currency, false); ?></small>
				<?php endif; ?>
			</td>
			<?php
		}
		else
		{
			?>
			<td class="nowrap text-center"><?php
			if ($item->price_display == SellaciousHelperPrice::PRICE_DISPLAY_CALL)
			{
				?><label class="label label-info hasTooltip" title="<?php echo $this->escape($item->seller_mobile) ?>">
					<i class="fa fa-phone"></i> <?php echo JText::_('COM_SELLACIOUS_PRODUCT_PRICE_DISPLAY_CALL_US'); ?></label><?php
			}
			elseif ($item->price_display == SellaciousHelperPrice::PRICE_DISPLAY_EMAIL)
			{
				?><label class="label label-info hasTooltip" title="<?php echo $this->escape($item->seller_email) ?>">
					<i class="fa fa-envelope"></i> <?php echo JText::_('COM_SELLACIOUS_PRODUCT_PRICE_DISPLAY_EMAIL_US'); ?></label><?php
			}
			elseif ($item->price_display == SellaciousHelperPrice::PRICE_DISPLAY_FORM)
			{
				?><label class="label label-info"><i class="fa fa-file-text"></i> <?php echo JText::_('COM_SELLACIOUS_PRODUCT_PRICE_DISPLAY_QUERY_FORM'); ?></label><?php
			}
			?></td><?php
		}
		?>
		<td class="nowrap text-center">
			<?php
			$nullDt = JFactory::getDbo()->getNullDate();

			if ($item->listing_publish_up == $nullDt || empty($item->listing_publish_up))
			{
				echo JText::_('COM_SELLACIOUS_PRODUCT_LISTING_INACTIVE_LABEL');
			}
			else
			{
				echo JHtml::_('date', $item->listing_publish_up, 'M d, Y H:i');
			}
			?>
		</td>
		<?php if ($multi_seller && !$free_listing): ?>
			<td class="nowrap text-center">
				<?php
				$nullDt = JFactory::getDbo()->getNullDate();

				if ($item->listing_publish_down == $nullDt || empty($item->listing_publish_down))
				{
					echo JText::_('COM_SELLACIOUS_PRODUCT_LISTING_INACTIVE_LABEL');
				}
				elseif (strtotime($item->listing_publish_down) < strtotime('last midnight'))
				{
					echo JText::_('COM_SELLACIOUS_PRODUCT_LISTING_INACTIVE_LABEL');
				}
				else
				{
					echo JHtml::_('date', $item->listing_publish_down, 'M d, Y H:i');
				}
				?>
			</td>
		<?php endif; ?>
		<?php if ($stock_in_catalogue): ?>
		<td class="nowrap text-center">
			<?php
			if ($item->stock_capacity > 0)
			{
				echo sprintf($item->over_stock ? '%d (%d + %d)' : '%d', $item->stock_capacity, $item->stock, $item->over_stock);
			}
			else
			{
				echo '<span class="red">' . JText::_('COM_SELLACIOUS_PRODUCT_OUT_OF_STOCK') . '</span>';
			}
			?>
		</td>
		<?php endif; ?>
		<?php
		$rate_url = JRoute::_('index.php?option=com_sellacious&view=ratings&filter[search]=' . $code);
		$rating   = $this->helper->rating->getProductRating($item->id);
		?>
		<?php if ($ratings_in_catalogue): ?>
		<td class="center">
			<a class="rating-stars" target="_blank" href="<?php echo $rate_url; ?>">
				<?php echo $rating ? $this->helper->core->getStars($rating->rating) : 'NA' ?>
			</a>
		</td>
		<?php endif; ?>
		<?php if ($orders_in_catalogue): ?>
		<td class="center">
			<?php echo $item->order_count ? sprintf('%d (%d)', $item->order_count, $item->order_units) : '&mdash;'; ?>
		</td>
		<?php endif; ?>
		<td>
			<span class="input-group">
				<span class="onoffswitch onoffswitch-selling">
					<input type="checkbox" class="onoffswitch-checkbox" id="st<?php echo $i ?>"
						<?php echo $item->is_selling ? ' checked ' : ' ' ?> <?php echo $item->seller_uid ? '' : ' disabled ' ?>
						   onclick="return listItemTask2('<?php echo $i ?>', 'products.<?php echo $item->is_selling ? 'setNotSelling' : 'setSelling' ?>', 'cb', this.form);">
					<label class="onoffswitch-label" for="st<?php echo $i ?>">
						<span class="onoffswitch-inner " data-swchon-text="SELLING" data-swchoff-text="SELLING"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</span>
			</span>
		</td>
		<td class="center hidden-phone">
			<span><?php echo (int) $item->id; ?></span>
		</td>
	</tr>
	<?php
}
