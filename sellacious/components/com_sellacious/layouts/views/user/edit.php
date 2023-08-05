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

/** @var SellaciousViewUser $this */
JHtml::_('behavior.framework');
JHtml::_('jquery.framework');

JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');

JHtml::_('script', 'media/com_sellacious/js/plugin/select2/select2.min.js', false, false);
JHtml::_('script', 'media/com_sellacious/js/plugin/cookie/jquery.cookie.js', false, false);
JHtml::_('script', 'com_sellacious/util.tabstate.js', false, true);

JText::script('COM_SELLACIOUS_VALIDATION_FORM_FAILED');

JHtml::_('script', 'com_sellacious/view.user.js', false, true);
JHtml::_('stylesheet', 'com_sellacious/view.user.css', null, true);
?>
<script type="text/javascript">
	(function ($) {
		$(document).ready(function () {
			$('select').css('width', '100%').select2();
		});
	})(jQuery);

	Joomla.submitbutton = function (task) {
		var form = document.getElementById('adminForm');
		var task2 = task.split('.')[1] || '';
		if (task2 == 'setType' || task2 == 'cancel' || document.formvalidator.isValid(form)) {
			Joomla.submitform(task, form);
		} else {
			alert(Joomla.JText._('COM_SELLACIOUS_VALIDATION_FORM_FAILED'));
		}
	}
</script>
<div class="row">
	<form action="<?php echo JUri::getInstance()->toString(); ?>"
		method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
		<!-- NEW WIDGET START -->
		<article class="col-sm-12 col-md-12 col-lg-12">

			<!-- Widget ID (each widget will need unique ID)-->
			<div class="tabsedit-container-off">

				<!-- widget content -->
				<div class="widget-body edittabs">
					<?php
					$fieldsets = $this->form->getFieldsets();
					$visible   = array();

					// Find visible tabs
					foreach ($fieldsets as $fs_key => $fieldset)
					{
						$fields = $this->form->getFieldset($fieldset->name);

						$visible[$fs_key] = 0;

						// Skip if fieldset is empty.
						if (count($fields) == 0)
						{
							continue;
						}

						foreach ($fields as $field)
						{
							if (!$field->hidden)
							{
								$visible[$fs_key]++;
							}
						}
					}

					// Add links for visible tabs
					?>
					<ul id="myTab3" class="nav nav-tabs tabs-pull-left bordered">
						<?php
						$this->counter = 0;

						foreach ($fieldsets as $fs_key => $fieldset)
						{
							if ($visible[$fs_key])
							{
								$css = (isset($fieldset->align) && $fieldset->align == 'right') ? 'pull-right' : '';
								?>
								<li class="<?php echo ($this->counter++) ? '' : ' active' ?> hidden-xs hidden-sm <?php echo $css ?>">
									<a href="#tab-<?php echo $fs_key; ?>" data-toggle="tab">
										<i class="fa fa-tasks"></i>&nbsp;&nbsp;&nbsp;<?php echo JText::_($fieldset->label, true) ?>
									</a>
								</li>
								<?php
							}
						}
						?>
						<li class="dropdown pull-left visible-xs visible-sm">
							<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i
									class="fa fa-lg fa-gear"></i> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<?php
								$this->counter = 0;
								foreach ($fieldsets as $fs_key => $fieldset)
								{
									if ($visible[$fs_key])
									{
										?>
										<li>
											<a href="#tab-<?php echo $fs_key; ?>"
												data-toggle="tab"><?php echo JText::_($fieldset->label, true) ?></a>
										</li>
										<?php
									}
								}
								?>
							</ul>
						</li>
					</ul>

					<div id="myTabContent3" class="tab-content padding-10"><?php
						$this->counter = 0;

						foreach ($fieldsets as $fs_key => $fieldset)
						{
							if ($visible[$fs_key])
							{
								// Special treatment for some tabs
								if ($fieldset->name == 'addresses')
								{
									echo $this->loadTemplate($fieldset->name);
								}
								else
								{
									$fields = $this->form->getFieldset($fieldset->name); ?>
									<div id="tab-<?php echo $fs_key; ?>"
										class="tab-pane fade <?php echo ($this->counter++) ? '' : 'in active' ?>">
										<fieldset>
											<?php
											foreach ($fields as $field)
											{
												if ($field->hidden):
													echo $field->input;
												else:
													?>
													<div class="row <?php echo $field->label ? 'input-row' : '' ?>">
														<?php
														if ($field->label && (!isset($fieldset->width) || $fieldset->width < 12))
														{
															echo '<div class="form-label col-sm-3 col-md-3 col-lg-2">' . $field->label . '</div>';
															echo '<div class="controls col-sm-9 col-md-9 col-lg-10">' . $field->input . '</div>';
														}
														else
														{
															echo '<div class="controls col-md-12">' . $field->input . '</div>';
														}
														?>
													</div>
													<?php
												endif;
											}
											?>
										</fieldset>
									</div>
									<?php
								}
							}
						}
					?></div><?php

					// Add (remaining) content for invisible tabs
					foreach ($fieldsets as $fs_key => $fieldset)
					{
						if (!$visible[$fs_key])
						{
							$fields = $this->form->getFieldset($fieldset->name);

							foreach ($fields as $field)
							{
								echo $field->input;
							}
						}
					}

					?>
					<input type="hidden" name="task" value="" />
					<?php echo JHtml::_('form.token'); ?>
				</div>
				<!-- end widget content -->
			</div>
			<!-- end widget -->

		</article>
		<!-- WIDGET END -->
	</form>
</div>
