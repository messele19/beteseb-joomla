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

JHtml::_('stylesheet', 'com_sellacious/field.fileplus.css', null, true);
JHtml::_('stylesheet', 'com_sellacious/view.locations.import.css', null, true);
JHtml::_('script', 'com_sellacious/view.locations.import.js', false, true);
?>
<div class="uploadform overlay"></div>
<form action="<?php echo  JRoute::_('index.php?option=com_sellacious') ?>" id="import-form" method="post" enctype="multipart/form-data">
<div class="uploadform content">
	<a class="uploadform-close txt-color-red pull-right cursor-pointer"><i class="fa fa-times"></i></a>
	<br/>
	<div class="jff-fileplus-wrapper" id="upload_wrapper">
		<div class="jff-fileplus-active center">
			<div class="alert"><?php echo JText::_('COM_SELLACIOUS_LOCATIONS_IMPORT_NOTICE') ?></div>
			<div class="bg-color-white upload-input w100p">
				<a class="btn btn-sm btn-primary jff-fileplus-add" style="float:none;"><i
					class="fa fa-upload"></i>&nbsp;<?php echo JText::_('COM_SELLACIOUS_LOCATIONS_IMPORT_SELECT_FILE'); ?></a>
				<input type="file" name="jform[import_file]" id="jform_import_file" class="hidden"/>
			</div>
			<div class="upload-process hidden" style="margin: auto; width: 10px;">
				<i class="jff-fileplus-progress"></i>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
	<input type="hidden" name="task" value="locations.analyzeImport"/>
	<?php echo JHtml::_('form.token'); ?>
</form>

