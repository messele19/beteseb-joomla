<?php
/**
 * @version     1.5.3
 * @package     sellacious
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     SPL Sellacious Private License; see http://www.sellacious.com/spl.html
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
// no direct access.
defined('_JEXEC') or die;

jimport('sellacious.loader');

if (class_exists('SellaciousHelper'))
{
	$helper = SellaciousHelper::getInstance();
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<!-- favicon -->
		<link rel="shortcut icon" href="templates/sellacious/images/favicon/favicon.ico" type="image/x-icon" />
		<link rel="icon" href="templates/sellacious/images/favicon/favicon.ico" type="image/x-icon" />

		<?php
		$user     = JFactory::getUser();
		$doc      = JFactory::getDocument();
		$app      = JFactory::getApplication();
		$sitename = $app->get('sitename');

		JHtml::_('script', 'media/com_sellacious/js/plugin/messagebox/jquery.messagebox.min.js', false, false);
		JHtml::_('stylesheet', 'media/com_sellacious/js/plugin/messagebox/jquery.messagebox.css', null, false);

		$doc->addStyleSheet('//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700');
		$doc->addStyleSheet('templates/sellacious/css/bootstrap.min.css', 'text/css', 'screen');
		$doc->addStyleSheet('templates/sellacious/css/font-awesome.min.css', 'text/css', 'screen');
		$doc->addStyleSheet('templates/sellacious/css/joomla-icons.css', 'text/css', 'screen');

		$doc->addStyleSheet('templates/sellacious/css/smartadmin-production.css', 'text/css', 'screen');
		$doc->addStyleSheet('templates/sellacious/css/smartadmin-skins.css', 'text/css', 'screen');
		$doc->addStyleSheet('templates/sellacious/css/custom-style.css', 'text/css', 'screen');

		if ($this->direction == 'rtl')
		{
			$doc->addStyleSheet('templates/sellacious/css/smartadmin-rtl.css', 'text/css', 'screen');
		}

		JHtml::_('jquery.framework');
		JHtml::_('jquery.ui');
		JHtml::_('bootstrap.tooltip');

		$doc->addScript('templates/sellacious/js/plugin/fastclick/fastclick.js');                    // FastClick: For mobile devices
		$doc->addScript('templates/sellacious/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js'); // JS TOUCH plugin for mobile drag-drop touch events
		$doc->addScript('templates/sellacious/js/plugin/msie-fix/jquery.mb.browser.min.js');         // browser msie issue fix
		$doc->addScript('templates/sellacious/js/notification/SmartNotification.min.js');            // Custom notification
		$doc->addScript('templates/sellacious/js/plugin/cookie/jquery.cookie.min.js');               // cookie
		$doc->addScript('templates/sellacious/js/sellacious-core.js');                               // Sellacious core functions to work template wide
		$doc->addScript('templates/sellacious/js/sellacious-notifier.js');                           // Sellacious notification per view page
		?>

		<script data-pace-options='{"restartOnRequestAfter": true}' src="templates/sellacious/js/plugin/pace/pace.min.js"></script>

		<jdoc:include type="head"/>

		<!--[if IE 7]>
		<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
		<![endif]-->
	</head>

	<?php $collapse = $app->input->cookie->get('collapsedmenu'); ?>
	<body class="fixed-page-footer <?php echo $app->input->get('hidemainmenu') || $collapse ? 'minified' : '' ?>"><!--
	 The possible classes: smart-style-3, minified, fixed-ribbon, fixed-header, fixed-width -->

		<!-- HEADER -->
		<header id="header" class="btn-group-justified">
			<div id="logo-group">
				<?php
				$logo = 'templates/sellacious/images/logo.png';

				if (isset($helper) && $helper->access->isSubscribed()):
					$altLogo = $helper->media->getImage('config.backoffice_logo', 1, false);
					$logo    = $altLogo ?: $logo;
				endif;
				?>
				<span id="logo"><a class="pull-left" href="<?php echo JRoute::_('index.php') ?>">
					<img src="<?php echo $logo ?>" alt="<?php echo htmlspecialchars($sitename) ?>"></a></span>

				<?php if ($this->countModules('logo-group')) { ?>
					<!-- OPTIMAL PLACE FOR NOTIFICATION MODULE -->
					<jdoc:include type="modules" name="logo-group" style="none"/>
				<?php } ?>
			</div>

			<?php if ($this->countModules('header-left')) { ?>
				<!-- Project Context -->
				<jdoc:include type="modules" name="header-left" style="none"/>
			<?php } ?>

			<!-- pulled right: nav area -->
			<div class="pull-right" style="margin-right: 1px;">
				<!-- logout button -->
				<div id="logout" class="btn-header transparent pull-right cursor-pointer">
					<?php $logout_url = 'index.php?option=com_login&task=logout&' . JSession::getFormToken() . '=1'; ?>
					<span> <a href="<?php echo $logout_url ?>" title="Sign Out" data-action="userLogout" class="hasTooltip" data-placement="bottom"
							data-logout-msg="You can improve your security further after logging out by closing this opened browser"><i
								class="fa fa-sign-out"></i></a> </span>
				</div>
				<!-- end logout button -->

				<!-- fullscreen button -->
				<div id="fullscreen" class="btn-header transparent pull-right">
					<span> <a href="javascript:void(0);" data-action="launchFullscreen" class="hasTooltip" data-placement="bottom"
								title="Full Screen"><i class="fa fa-arrows-alt"></i></a> </span>
				</div>
				<!-- end fullscreen button -->

				<!-- back to Joomla administrator button -->
				<div id="joomla-admin" class="btn-header transparent pull-right cursor-pointer">
					<span> <a href="../<?php echo basename(JPATH_ADMINISTRATOR); ?>/index.php" class="hasTooltip" data-placement="bottom"
							  title="Back to Joomla Administrator"><i class="fa fa-joomla"></i></a> </span>
				</div>
				<!-- end back to Joomla administrator button -->

				<!-- Go to Joomla frontend button -->
				<div id="joomla" class="btn-header transparent pull-right cursor-pointer">
					<span> <a href="../index.php" target="_blank" class="hasTooltip" data-placement="bottom"
								title="View Site"><i class="fa fa-external-link"></i></a> </span>
				</div>
				<!-- end Go to Joomla frontend button -->

				<!-- Go to Joomla frontend button -->
				<div id="hide-menu" class="btn-header transparent pull-right cursor-pointer">
					<span> <a href="javascript:void(0)" class="hasTooltip" data-placement="bottom" data-menu="hidemenu"
								title="Menu"><i class="fa fa-reorder"></i></a> </span>
				</div>
				<!-- end Go to Joomla frontend button -->

				<!-- Sync media button -->
				<div id="sync-media" class="btn-header transparent pull-right cursor-pointer">
					<span> <a href="javascript:void(0)" class="hasTooltip" data-placement="bottom" data-action="sync-media" style="width: 135px;"
								title="Refresh Media Uploads"><i class="fa fa-refresh"></i> <span class="text-normal"> <?php
								echo JText::_('COM_SELLACIOUS_MEDIA_SYNC_BUTTON_LABEL') ?> </span></a> </span>
				</div>
				<!-- end Sync media button -->

				<?php if ($this->countModules('header-right')): ?>
					<jdoc:include type="modules" name="header-right" style="none"/>
				<?php endif; ?>
			</div>
			<!-- end pulled right: nav area -->

			<?php if (isset($helper) && ($helper->access->check('config.edit') || !$helper->access->isSubscribed())): ?>
				<div id="context-news" class="pull-right padding-5"><!-- dynamic news --></div>
			<?php endif; ?>
		</header>
		<!-- END HEADER -->

		<!-- Left panel : Navigation area -->
		<?php if ($this->countModules('left-panel') || $this->countModules('menu')) { ?>
		<!-- Note: This width of the aside area can be adjusted through LESS variables -->
		<aside id="left-panel">
			<div class="login-info">
				<span> <!-- User image size is adjusted inside CSS, it should stay as it -->
					<a style="cursor:auto;" id="show-shortcut" data-action="toggleShortcut" href="index.php?option=com_sellacious&view=profile">
						<!--<img src="templates/sellacious/images/avatars/male.png" alt="me" class="online"/>-->
						<i class="fa fa-user"></i>
						<span><?php echo $user->get('name'); ?></span>
					</a>
				</span>
			</div>

			<!-- User info -->
			<?php if ($this->countModules('left-panel')) { ?>
				<jdoc:include type="modules" name="left-panel" style="none"/>
			<?php } ?>
			<!-- end user info -->

			<!-- NAVIGATION : This navigation is also responsive
			To make this navigation dynamic please make sure to link the node
			(the reference to the nav > ul) after page load. Or the navigation will not initialize.
			-->

			<!-- User info -->
			<?php if ($this->countModules('menu')) { ?>
				<jdoc:include type="modules" name="menu" style="none"/>
			<?php } ?>
			<!-- end user info -->

		</aside>
		<!-- END NAVIGATION -->
		<?php } ?>
		<!-- End Left panel : Navigation area -->

		<!-- MAIN PANEL -->
		<div id="main" role="main">

			<!-- RIBBON -->
			<div id="ribbon">
				<span class="minifyme"> <i class="fa fa-arrow-circle-left hit"></i> </span>
				<!-- breadcrumb -->
				<?php if ($this->countModules('ribbon-left')) { ?>
					<jdoc:include type="modules" name="ribbon-left" style="none"/>
				<?php } ?>
				<!-- end breadcrumb -->

				<?php if ($this->countModules('ribbon-right')) { ?>
				<span class="ribbon-button-alignment pull-right">
					<jdoc:include type="modules" name="ribbon-right" style="none"/>
				</span>
				<?php } ?>
				<div class="btn-headactions pull-right">
					<a href="http://sellacious.com/documentation.html" target="_blank" title=<?php echo JText::_('TPL_SELLACIOUS_DOCUMENT_TITLE'); ?> class="primary"><i class="fa fa-book"></i> <?php echo JText::_('TPL_SELLACIOUS_DOCUMENTATION'); ?> </a>
					<a href="http://sellacious.com/community-support.html" target="_blank" title="Forum"><i class="fa fa-phone"></i><?php echo JText::_('TPL_SELLACIOUS_SUPPORT'); ?> </a>
					<a href="https://extensions.joomla.org/write-review/review/add?extension_id=11448" target="_blank" title="More info"><i class="fa fa-star"></i><?php echo JText::_('TPL_SELLACIOUS_RATE_US_ON_JED'); ?>  </a>
				</div>

			</div>
			<!-- END RIBBON -->

			<?php if ($this->countModules('toolbar') || $this->countModules('title')) : ?>
				<div class="box-toolbar">
					<div class="">
						<!-- col -->
						<div class="pull-left">
							<!-- PAGE HEADER -->
							<jdoc:include type="modules" name="title"/>
						</div>
						<!-- end col -->

						<!-- right side of the page with the sparkline graphs -->
						<!-- col -->
						<div class="pull-right">
							<?php if ($this->countModules('toolbar')) : ?>
								<span class="pull-right">
									<jdoc:include type="modules" name="toolbar" style="none"/>
								</span>
							<?php endif; ?>
						</div>
						<!-- end col -->
					</div>
				</div>
			<?php endif; ?>

			<?php if ($this->countModules('top')) : ?>
				<div class="row">
					<div class="col-sm-12">
						<jdoc:include type="modules" name="content-top" style="xhtml"/>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($this->countModules('submenu')) : ?>
				<div class="row">
					<div class="col-sm-12">
						<jdoc:include type="modules" name="submenu" style="none"/>
					</div>
				</div>
			<?php endif; ?>

			<div class="clearfix"></div>

			<!-- MAIN CONTENT -->
			<div id="content">

				<?php if ($this->countModules('content-top')) { ?>
					<div class="row">
						<jdoc:include type="modules" name="content-top" style="none"/>
					</div>
				<?php } ?>

				<div class="clearfix"></div>

				<div class="component content-wrap">

					<div id="system-message-container"><jdoc:include type="message" style="xhtml"/></div>
					<div class="clearfix"></div>

					<jdoc:include type="component" style="xhtml"/>
					<div class="clearfix"></div>
				</div>

				<?php if ($this->countModules('content-bottom')) { ?>
					<div class="row">
						<jdoc:include type="modules" name="content-bottom" style="none"/>
					</div>
				<?php } ?>

			</div>

			<div class="clearfix"></div>

			<!-- END MAIN CONTENT -->

		</div>
		<!-- END MAIN PANEL -->

	<?php if ($this->countModules('footer')) { ?>
		<jdoc:include type="modules" name="footer" style="none"/>
	<?php } ?>

		<jdoc:include type="modules" name="dynamic" style="xhtml"/>

		<!-- Google Analytics code below -->
		<?php if ($ga_code = $this->params->get('ga_code')) { ?>
			<script type="text/javascript">
				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', '<?php echo htmlspecialchars($ga_code) ?>']);
				_gaq.push(['_trackPageview']);

				(function() {
					var ga = document.createElement('script');
					ga.type = 'text/javascript';
					ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
					var s = document.getElementsByTagName('script')[0];
					s.parentNode.insertBefore(ga, s);
				})();
			</script>
		<?php } ?>

	</body>

</html>
