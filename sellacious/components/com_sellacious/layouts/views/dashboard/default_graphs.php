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

/** @var  $this SellaciousViewDashboard */
JHtml::_('jquery.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.keepalive');

$doc = JFactory::getDocument();
$doc->addScriptVersion('templates/sellacious/js/plugin/flot/jquery.flot.cust.js');
$doc->addScriptVersion('templates/sellacious/js/plugin/flot/jquery.flot.resize.min.js');
$doc->addScriptVersion('templates/sellacious/js/plugin/flot/jquery.flot.fillbetween.min.js');
$doc->addScriptVersion('templates/sellacious/js/plugin/flot/jquery.flot.orderBar.min.js');
$doc->addScriptVersion('templates/sellacious/js/plugin/flot/jquery.flot.pie.min.js');
$doc->addScriptVersion('templates/sellacious/js/plugin/flot/jquery.flot.tooltip.js');
$doc->addScriptVersion('templates/sellacious/js/plugin/moment/moment.js');
$doc->addScriptVersion('templates/sellacious/js/plugin/moment/moment-timezone-with-data.min.js');
$doc->addScriptVersion('templates/sellacious/js/plugin/ion-slider/ion.rangeSlider.js');

JHtml::_('stylesheet', 'com_sellacious/view.dashboard.graph.css', null, true);

/** @var  JDate  $start */
/** @var  JDate  $end */
$start = $this->helper->core->fixDate('now', 'UTC', null)->setTime(0, 0, 0)->modify('-29 day');
$end   = $this->helper->core->fixDate('now', 'UTC', null)->setTime(23, 59, 59);
$min   = $start->toUnix() + $start->getOffsetFromGmt();
$max   = $end->toUnix() + $end->getOffsetFromGmt();

$chartC = array();
$chartA = array();
$chartV = array();

$stats = array_slice($this->orderStats, 1);

foreach ($stats as $row)
{
	$chartC[] = array($row->ts * 1000, (int) $row->count, $row->date);
	$chartA[] = array($row->ts * 1000, (float) $row->value, $row->date);
}

if ($this->helper->access->check('statistics.visitor'))
{
	$stats = $this->helper->report->getPageViewStats(30, 'now', false);

	foreach ($stats as $row)
	{
		$chartV[] = array($row->ts * 1000, (int) $row->count, $row->date);
	}
}
?>
<script>
	jQuery(function ($) {
		$(document).ready(function () {
			var dashboardC = new SellaciousDashboard;
			dashboardC.init({
					min: <?php echo $min; ?>,
					max: <?php echo $max; ?>,
					range: 'm'
				},
				'#stats-chart0',
				'#range-slider0',
				<?php echo json_encode($chartC); ?>
			);

			var dashboardA = new SellaciousDashboard;
			dashboardA.init({
					min: <?php echo $min; ?>,
					max: <?php echo $max; ?>,
					range: 'm'
				},
				'#stats-chart1',
				'#range-slider1',
				<?php echo json_encode($chartA); ?>
			);

			<?php if (count($chartV)): ?>
			var dashboardV = new SellaciousDashboard;
			dashboardV.init({
					min: <?php echo $min; ?>,
					max: <?php echo $max; ?>,
					range: 'm'
				},
				'#stats-chart2',
				'#range-slider2',
				<?php echo json_encode($chartV); ?>
			);
			<?php endif; ?>
		});
	});
</script>
<section id="widget-grid">
	<!-- row -->
	<div class="row">
		<article class="col-sm-12">
			<!-- new widget -->
			<div class="jarviswidget">

				<header>
					<ul class="nav nav-tabs pull-right in" id="myTab">
						<li class="active">
							<a data-toggle="tab" href="#s3"><i class="fa fa-link"></i> <span
									class="hidden-mobile hidden-tablet"><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS'); ?></span></a>
						</li>
						<li>
							<a data-toggle="tab" href="#s0"><i class="fa fa-clock-o"></i> <span
									class="hidden-mobile hidden-tablet"><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_DAILY_ORDERS_COUNT'); ?></span></a>
						</li>
						<li>
							<a data-toggle="tab" href="#s1"><i class="fa fa-money"></i> <span
									class="hidden-mobile hidden-tablet"><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_DAILY_ORDERS_REVENUE'); ?></span></a>
						</li>
						<?php if (count($chartV)): ?>
						<li>
							<a data-toggle="tab" href="#s2"><i class="fa fa-globe"></i> <span
									class="hidden-mobile hidden-tablet"><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_DAILY_PAGE_VIEWS'); ?></span></a>
						</li>
						<?php endif; ?>
					</ul>
				</header>

				<!-- widget div-->
				<div class="widget-body">
					<!-- content -->
					<div id="myTabContent" class="tab-content">
						<div class="tab-pane fade in no-padding-bottom active" id="s3">
							<div class="quick-links-area w100p">
								<ul id="quick-links-ul" class="quick-links">
									<?php if ($this->helper->access->check('config.edit')) : ?>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&task=product.add'); ?>">
												<i class="fa fa-lg fa-fw fa-plus"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_CREATE_NEW_PRODUCT'); ?></span>
											</a>
										</li>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&view=products'); ?>">
												<i class="fa fa-lg fa-fw fa-book"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_VIEW_PRODUCT_CATALOGUE'); ?></span>
											</a>
										</li>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&view=orders'); ?>">
												<i class="fa fa-lg fa-fw fa-paperclip"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_VIEW_ORDERS'); ?></span>
											</a>
										</li>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&view=shippingrules'); ?>">
												<i class="fa fa-lg fa-fw fa-wrench"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_VIEW_SHIPPING_RULES'); ?></span>
											</a>
										</li>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&view=coupons'); ?>">
												<i class="fa fa-lg fa-fw fa-ticket"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_VIEW_COUPONS'); ?></span>
											</a>
										</li>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&view=shoprules'); ?>">
												<i class="fa fa-lg fa-fw fa-exchange"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_VIEW_TAX_N_DISCOUNTS'); ?></span>
											</a>
										</li>

									<?php elseif ($this->helper->seller->is()) : ?>

										<?php if ($this->helper->access->check('product.create') ) : ?>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&task=product.add'); ?>">
												<i class="fa fa-lg fa-fw fa-plus"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_CREATE_NEW_PRODUCT'); ?></span>
											</a>
										</li>
										<?php endif; ?>
										<?php if ($this->helper->access->check('product.list') || $this->helper->access->check('product.list.own')) : ?>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&view=products'); ?>">
												<i class="fa fa-lg fa-fw fa-book"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_VIEW_PRODUCT_CATALOGUE'); ?></span>
											</a>
										</li>
										<?php endif; ?>
										<?php if ($this->helper->access->check('order.list') || $this->helper->access->check('order.list.own')) : ?>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&view=orders'); ?>">
												<i class="fa fa-lg fa-fw fa-paperclip"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_VIEW_ORDERS'); ?></span>
											</a>
										</li>
										<?php endif; ?>
										<?php if ($this->helper->access->check('shippingrule.list')) : ?>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&view=shippingrules'); ?>">
												<i class="fa fa-lg fa-fw fa-wrench"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_VIEW_SHIPPING_RULES'); ?></span>
											</a>
										</li>
										<?php endif; ?>
										<?php if ($this->helper->access->check('coupon.list') || $this->helper->access->check('coupon.list.own')) : ?>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&view=coupons'); ?>">
												<i class="fa fa-lg fa-fw fa-ticket"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_VIEW_COUPONS'); ?></span>
											</a>
										</li>
										<?php endif; ?>
										<?php if ($this->helper->access->check('shoprule.list')) : ?>
										<li>
											<a href="<?php echo JRoute::_('index.php?option=com_sellacious&view=shoprules'); ?>">
												<i class="fa fa-lg fa-fw fa-exchange"></i><span><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_QUICK_LINKS_VIEW_TAX_N_DISCOUNTS'); ?></span>
											</a>
										</li>
										<?php endif; ?>
									<?php endif; ?>

								</ul>
							</div>
						</div>

						<div class="tab-pane fade in no-padding-bottom" id="s0">
							<div class="chart-area w100p">
								<input id="range-slider0" type="hidden" title="">
								<div id="stats-chart0" class="chart-large txt-color-blue"><!-- Graph / chart would appear here --></div>
								<div id="axis-label-y0" class="axis-label-y"><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_ORDERS_COUNT'); ?></div>
							</div>
						</div>

						<div class="tab-pane fade in no-padding-bottom" id="s1">
							<div class="chart-area w100p">
								<input id="range-slider1" type="hidden" title="">
								<div id="stats-chart1" class="chart-large txt-color-blue"><!-- Graph / chart would appear here --></div>
								<div id="axis-label-y1" class="axis-label-y"><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_ORDERS_REVENUE'); ?>
									(<?php echo $this->helper->currency->forUser(null, 'code_3'); ?>)</div>
							</div>
						</div>

						<?php if (count($chartV)): ?>
						<div class="tab-pane fade in no-padding-bottom" id="s2">
							<div class="chart-area w100p">
								<input id="range-slider2" type="hidden" title="">
								<div id="stats-chart2" class="chart-large txt-color-blue"><!-- Graph / chart would be here --></div>
								<div id="axis-label-y2" class="axis-label-y"><?php echo JText::_('COM_SELLACIOUS_DASHBOARD_PAGE_VIEWS'); ?></div>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<!-- end content -->
				</div>
				<!-- end widget div -->
			</div>
			<!-- end widget -->
		</article>
	</div>
	<!-- end row -->
</section>

