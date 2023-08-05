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

$app     = JFactory::getApplication();
$doc     = JFactory::getDocument();
$user    = JFactory::getUser();
$ga_code = $this->params->get('ga_code');

$this->language  = $doc->language;
$this->direction = $doc->direction;

$body_class = $this->params->get('menu_on_top') ? 'menu-on-top ' : '';
$body_class .= $this->params->get('fixed_header') ? 'fixed-header ' : '';
$body_class .= $this->params->get('fixed_navigation') ? 'fixed-navigation ' : '';
$body_class .= $this->params->get('fixed_ribbon') ? 'fixed-ribbon ' : '';
$body_class .= $this->params->get('fixed_footer') ? 'fixed-page-footer ' : '';
$body_class .= $this->params->get('minified') ? 'minified ' : '';

$doc->addStyleSheet('//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700');
$doc->addStyleSheetVersion('templates/sellacious/css/bootstrap.min.css');
$doc->addStyleSheetVersion('templates/sellacious/css/font-awesome.min.css');
$doc->addStyleSheetVersion('templates/sellacious/css/smartadmin-production.css');
$doc->addStyleSheetVersion('templates/sellacious/css/smartadmin-skins.css');
$doc->addStyleSheetVersion('templates/sellacious/css/joomla-icons.css');
$doc->addStyleSheetVersion('templates/sellacious/css/custom-style.css');

JHtml::_('jquery.framework');
// jQuery UI full, not from joomla core's minimised one
$doc->addScriptVersion('templates/sellacious/js/libs/jquery-ui-1.10.3.min.js');
// FastClick: For mobile devices
$doc->addScriptVersion('templates/sellacious/js/plugin/fastclick/fastclick.min.js');
// JS TOUCH plugin for mobile drag-drop touch events
$doc->addScriptVersion('templates/sellacious/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js');
// browser msie issue fix
$doc->addScriptVersion('templates/sellacious/js/plugin/msie-fix/jquery.mb.browser.min.js');
// Bootstrap JS
$doc->addScriptVersion('templates/sellacious/js/bootstrap/bootstrap.min.js');
// Custom notification
$doc->addScriptVersion('templates/sellacious/js/notification/SmartNotification.min.js');
// Include Sparklines and charts
$doc->addScriptVersion('templates/sellacious/js/plugin/sparkline/jquery.sparkline.min.js');
// Select2 behaviour
$doc->addScriptVersion('templates/sellacious/js/plugin/select2/select2.min.js');

$sitename = $app->get('sitename');
?>
<!DOCTYPE html>
	<html class="bg-transparent" xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<!-- iOS web-app meta : hides Safari UI Components and Changes Status Bar Appearance -->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">

		<link rel="shortcut icon" href="templates/sellacious/images/favicon/favicon.ico" type="image/x-icon">
		<link rel="icon" href="templates/sellacious/images/favicon/favicon.ico" type="image/x-icon">

		<!-- #APP SCREEN / ICONS -->
		<!-- Specifying a Web page Icon for Web Clip
			 Ref: https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
		<link rel="apple-touch-icon" href="templates/sellacious/images/splash/sptouch-icon-iphone.png">
		<link rel="apple-touch-icon" sizes="76x76" href="templates/sellacious/images/splash/touch-icon-ipad.png">
		<link rel="apple-touch-icon" sizes="120x120" href="templates/sellacious/images/splash/touch-icon-iphone-retina.png">
		<link rel="apple-touch-icon" sizes="152x152" href="templates/sellacious/images/splash/touch-icon-ipad-retina.png">

		<!-- Startup image for web apps -->
		<link rel="apple-touch-startup-image" href="templates/sellacious/images/splash/ipad-landscape.png"
			  media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
		<link rel="apple-touch-startup-image" href="templates/sellacious/images/splash/ipad-portrait.png"
			  media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
		<link rel="apple-touch-startup-image" href="templates/sellacious/images/splash/iphone.png"
			  media="screen and (max-device-width: 320px)">

		<jdoc:include type="head"/>

		<script data-pace-options='{ "restartOnRequestAfter": true }'
				src="templates/sellacious/js/plugin/pace/pace.min.js"></script>

		<!--[if IE 7]>
		<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
		<![endif]-->
	</head>

	<body class="hidden-menu overflow-visible bg-transparent">
		<!-- MAIN PANEL -->
			<!-- MAIN CONTENT -->
			<div id="content">

				<jdoc:include type="message" style="xhtml"/>

				<!-- widget grid -->
				<section id="widget-grid">

					<!-- row -->
					<div class="row">

						<!-- a blank row to get started -->
						<div class="col-sm-12" style="padding: 0 15px">

							<div class="component"> <!-- class: content-wrap -->
								<!-- component body contents here -->
								<jdoc:include type="component" style="xhtml"/>
							</div>
						</div>

					</div>
					<!-- end row -->

				</section>
				<!-- end widget grid -->
			</div>
			<!-- END MAIN CONTENT -->

		<!-- END MAIN PANEL -->
	</body>
</html>
