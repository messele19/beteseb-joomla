<?php
/**
 * @version     1.5.3
 * @package     sellacious
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     SPL Sellacious Private License; see http://www.sellacious.com/spl.html
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
defined('_JEXEC') or die;

JHtml::_('behavior.framework');
JHtml::_('jquery.framework');

$link = JRoute::_('index.php?option=com_sellacious&view=activation');

JHtml::_('script', 'com_sellacious/view.activation.readonly.js', false, true);
JHtml::_('stylesheet', 'com_sellacious/view.activation.footer.css', null, true);

JText::script('COM_SELLACIOUS_ACTIVATION_CONFIRM_TRIAL_MESSAGE');

$helper = SellaciousHelper::getInstance();
?>
<div class="page-footer">
	<div class="row">
		<div class="col-xs-12 col-sm-8">

			<?php if ($license->get('name') && $license->get('sitekey')): ?>
				<span class="txt-color-white pull-left"><?php echo JText::_('MOD_FOOTER_LICENSED_TO_LABEL') ?>: <?php echo $license->get('name') ?></span>
			<?php endif; ?>

			<span class="license-verify pull-left" style="margin-left: 10px">
				<span class="checking">
					<i class="fa fa-spinner fa-pulse"></i>
					<label class="text-info"><?php echo JText::_('COM_SELLACIOUS_LICENSE_WAIT_VERIFY_SPINNER_LABEL') ?></label>
				</span>
				<span class="active">
					<i class="fa fa-thumbs-up text-success"></i>
					<a href="<?php echo $link ?>">
					<label class="text-success"><?php echo JText::_('COM_SELLACIOUS_LICENSE_ACTIVE_LABEL') ?></label></a>
				</span>
				<span class="inactive">
					<i class="fa fa-warning text-danger"></i>
					<label class="text-danger"><?php echo JText::sprintf('COM_SELLACIOUS_LICENSE_INACTIVE_LABEL_LINK', $link . '&layout=register') ?></label>
				</span>
				<span class="void">
					<i class="fa fa-warning text-danger"></i>
					<a href="<?php echo $link ?>">
					<label class="text-danger"><?php echo JText::_('COM_SELLACIOUS_LICENSE_VOID_LABEL') ?></label></a>
				</span>
				<span class="unregistered">
					<i class="fa fa-warning text-danger"></i>
					<a href="<?php echo $link ?>">
					<label class="text-danger"><?php echo JText::_('COM_SELLACIOUS_LICENSE_UNREGISTERED_LABEL') ?></label></a>
				</span>
				<span class="error">
					<i class="fa fa-times text-danger"></i>
					<a href="<?php echo $link ?>">
					<label class="text-danger"><?php echo JText::_('COM_SELLACIOUS_LICENSE_ERROR_LABEL') ?></label></a>
				</span>
			</span>

			<span style="white-space: nowrap;">
				<span class="license-subscription hidden txt-color-lighten text-normal">&nbsp;&nbsp;&nbsp;<i class="fa fa-star txt-color-gold"></i>
					<?php echo JText::_('COM_SELLACIOUS_LICENSE_PREMIUM_EXPIRY_LABEL') ?></span>
				<span class="license-expiry_date hidden"><span class="txt-color-lighten text-normal"></span></span>
			</span>

			<span style="white-space: nowrap; padding-left: 10px;" class="premium-prompt hidden text-success">
				<a href="http://sellacious.com/pricing.html" target="_blank"><span class="txt-color-lighten"><?php
						echo JText::_('COM_SELLACIOUS_LICENSE_UPGRADE_MESSAGE') ?></span></a>
				<?php if (!$helper->config->get('free_forever', 0, 'sellacious', 'application')): ?>
				| <a href="#" class="request-trial cursor-pointer"><span class="txt-color-lighten"><?php
						echo JText::_('COM_SELLACIOUS_LICENSE_UPGRADE_TRIAL_MESSAGE') ?></span></a>
				<?php endif; ?>
			</span>

			<div id="sellacious-license" class="hidden"
				 data-name="<?php echo htmlspecialchars($license->get('name')) ?>"
				 data-sitekey="<?php echo htmlspecialchars($license->get('sitekey')) ?>"
				 data-sitename="<?php echo htmlspecialchars(JFactory::getConfig()->get('sitename')) ?>"
				 data-siteurl="<?php echo htmlspecialchars(rtrim(JUri::root(), '/')) ?>"
				 data-version="<?php echo htmlspecialchars($version) ?>"
				 data-template="<?php echo htmlspecialchars($license->get('template')) ?>"></div>

		</div>
		<div class="app-version-footer">Sellacious <?php echo $version ?></div>
	</div>
</div>
