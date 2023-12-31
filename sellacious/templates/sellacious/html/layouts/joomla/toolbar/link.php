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

$btnClass = 'btn';
$doTask = $displayData['doTask'];
$class  = $displayData['class'];
$text   = $displayData['text'];
$tip    = $text;

if (strpos($text, '::') !== false)
{
	list($text, $tip) = explode('::', $text, 2);
}
?>
<button type="button" class="<?php echo $btnClass; ?> btn-default hasTooltip"
		onclick="location.href='<?php echo $doTask; ?>';" title="<?php echo htmlspecialchars($tip); ?>">
	<i class="<?php echo trim($class); ?>"></i>
	<?php echo $text; ?>
</button>
