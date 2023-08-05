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

JHtml::_('stylesheet', 'com_sellacious/view.products.modal.css', null, true);
?>
<div class="modalform overlay"></div>
<form action="<?php echo  JRoute::_('index.php?option=com_sellacious') ?>" id="import-form" method="post" enctype="multipart/form-data">
<div class="modalform content">
	<a class="modalform-close txt-color-red pull-right cursor-pointer"><i class="fa fa-times"></i></a>
	<br/>
	<div class="center">
		<button type="button" class="btn btn-primary" onclick="this.form.specs.value=0; this.form.submit();">
			<i class="fa fa-download"></i> &nbsp;<?php echo JText::_('COM_SELLACIOUS_IMPORTER_PRODUCT_IMPORT_CSV_SAMPLE_DOWNLOAD_SPEC_NONE') ?></button>
		<br>
		<br>
		<button type="button" class="btn btn-primary" onclick="this.form.specs.value=1; this.form.submit();">
			<i class="fa fa-download"></i> &nbsp;<?php echo JText::_('COM_SELLACIOUS_IMPORTER_PRODUCT_IMPORT_CSV_SAMPLE_DOWNLOAD_SPEC_ALL') ?></button>
	</div>
</div>
	<input type="hidden" name="task" value="products.csvTemplate"/>
	<input type="hidden" name="specs" value=""/>
	<?php echo JHtml::_('form.token'); ?>
</form>

