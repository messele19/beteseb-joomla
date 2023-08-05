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
?>
<div class="page-footer">
	<div class="row">
		<div class="col-xs-12 col-sm-8">
			<?php if (!$license->get('name') || !$license->get('sitekey')): ?>

				<i class="fa fa-warning txt-color-white"></i>
				<span class="txt-color-yellow"> &nbsp;<?php echo JText::_('MOD_FOOTER_LICENSED_NOT_REGISTERED') ?></span>

			<?php else: ?>

				<span class="txt-color-white"><?php echo JText::_('MOD_FOOTER_LICENSED_TO_LABEL') ?>: <?php echo $license->get('name') ?></span>

				<?php if (!$license->get('activated')): ?>

					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<i class="fa fa-warning txt-color-white"></i>
					<span class="txt-color-yellow"> &nbsp;<?php echo JText::_('MOD_FOOTER_LICENSED_NOT_ACTIVATED') ?></span>

				<?php endif; ?>

			<?php endif; ?>
		</div>
		<div class="app-version-footer"><?php echo $version ?></div>
	</div>
</div>
