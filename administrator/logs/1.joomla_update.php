#
#<?php die('Forbidden.'); ?>
#Date: 2023-06-06 00:19:45 UTC
#Software: Joomla! 4.2.9 Stable [ Uaminifu ] 14-March-2023 15:00 GMT

#Fields: datetime	priority clientip	category	message
2023-06-06T00:19:45+00:00	INFO 98.204.88.148	update	Update started by user admin (918). Old version is 4.2.9.
2023-06-06T00:19:47+00:00	INFO 98.204.88.148	update	Downloading update file from https://s3-us-west-2.amazonaws.com/joomla-official-downloads/joomladownloads/joomla4/Joomla_4.3.2-Stable-Update_Package.zip?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIA6LXDJLNUINX2AVMH%2F20230606%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20230606T001930Z&X-Amz-Expires=60&X-Amz-SignedHeaders=host&X-Amz-Signature=f0d0e9cbca824913a2c5247ff87f94180e478ebdf751a44652fea021b0cd70b8.
2023-06-06T00:19:54+00:00	INFO 98.204.88.148	update	File Joomla_4.3.2-Stable-Update_Package.zip downloaded.
2023-06-06T00:19:58+00:00	INFO 98.204.88.148	update	Starting installation of new version.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Finalising installation.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Start of SQL updates.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	The current database version (schema) is 4.2.9-2023-03-07.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2022-11-06. Query text: DELETE FROM `#__assets` WHERE `name` LIKE '#__ucm_content.%';.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-01-30. Query text: UPDATE `#__extensions`    SET `params` = REPLACE(`params`, '"negotiate_tls":1', .
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-01-30. Query text: UPDATE `#__extensions`    SET `params` = REPLACE(`params`, '"negotiate_tls":0', .
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-01-30. Query text: UPDATE `#__extensions`    SET `params` = REPLACE(`params`, '"encryption":"none"'.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-01-30. Query text: UPDATE `#__extensions`    SET `params` = REPLACE(`params`, '"host":"ldaps:\\/\\/.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-02-15. Query text: CREATE TABLE IF NOT EXISTS `#__guidedtours` (   `id` int NOT NULL AUTO_INCREMENT.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-02-15. Query text: INSERT IGNORE INTO `#__guidedtours` (`id`, `title`, `description`, `ordering`, `.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-02-15. Query text: CREATE TABLE IF NOT EXISTS `#__guidedtour_steps` (   `id` int NOT NULL AUTO_INCR.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-02-15. Query text: INSERT IGNORE INTO `#__guidedtour_steps` (`id`, `tour_id`, `title`, `published`,.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-02-15. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-02-15. Query text: INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `c.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-02-15. Query text: INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (LAST_INSERT_ID(), 0.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-02-25. Query text: ALTER TABLE `#__banners` MODIFY `clickurl` VARCHAR(2048) NOT NULL DEFAULT '';.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-07. Query text: UPDATE `#__guidedtour_steps` SET `target` = '#jform_description,#jform_descripti.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-07. Query text: UPDATE `#__guidedtour_steps` SET `target` = '#jform_articletext,#jform_articlete.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-09. Query text: UPDATE `#__guidedtour_steps` SET `target` = '#jform_published' WHERE `target` = .
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-09. Query text: UPDATE `#__guidedtour_steps` SET `target` = '#jform_sendEmail0' WHERE `target` =.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-09. Query text: UPDATE `#__guidedtour_steps` SET `target` = '#jform_block0' WHERE `target` = '#j.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-09. Query text: UPDATE `#__guidedtour_steps` SET `target` = '#jform_requireReset0' WHERE `target.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-10. Query text: UPDATE `#__guidedtour_steps` SET `type` = 2, `interactive_type` = 2 WHERE `id` I.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-10. Query text: UPDATE `#__guidedtour_steps` SET `type` = 2, `interactive_type` = 3 WHERE `id` I.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-10. Query text: UPDATE `#__guidedtour_steps` SET `target` = 'joomla-field-fancy-select .choices .
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-10. Query text: UPDATE `#__guidedtour_steps` SET `target` = 'joomla-field-fancy-select .choices[.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-10. Query text: UPDATE `#__guidedtour_steps` SET `target` = 'joomla-field-fancy-select .choices[.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-28. Query text: ALTER TABLE `#__guidedtours` DROP COLUMN `asset_id` ;.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-28. Query text: DELETE FROM `#__assets` WHERE `name` LIKE 'com_guidedtours.tour.%';.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-29. Query text: UPDATE `#__guidedtour_steps` SET `target` = 'joomla-field-fancy-select .choices'.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.0-2023-03-29. Query text: UPDATE `#__guidedtour_steps` SET `target` = 'joomla-field-fancy-select .choices[.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.2-2023-03-31. Query text: UPDATE `#__guidedtour_steps` SET `title` = 'COM_GUIDEDTOURS_TOUR_BANNERS_STEP_DE.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.2-2023-05-03. Query text: UPDATE `#__extensions`    SET `params` = '{"template_positions_display":"0","upl.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.2-2023-05-20. Query text: ALTER TABLE `#__user_mfa` ADD COLUMN `tries` int NOT NULL DEFAULT 0 ;.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Ran query from file 4.3.2-2023-05-20. Query text: ALTER TABLE `#__user_mfa` ADD COLUMN `last_try` datetime ;.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	End of SQL updates.
2023-06-06T00:21:37+00:00	INFO 98.204.88.148	update	Deleting removed files and folders.
2023-06-06T00:21:38+00:00	INFO 98.204.88.148	update	Cleaning up after installation.
2023-06-06T00:21:38+00:00	INFO 98.204.88.148	update	Update to version 4.3.2 is complete.
