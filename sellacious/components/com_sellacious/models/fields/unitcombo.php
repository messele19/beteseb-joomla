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

/**
 * Form Field class for textbox and units of measurement combo input.
 *
 */
class JFormFieldUnitCombo extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var   string
	 */
	protected $type = 'unitcombo';

	/**
	 * Method to get the field options.
	 *
	 * @return  string  The field html markup.
	 * @since   1.6
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$hint         = $this->translateHint ? JText::_($this->hint) : $this->hint;
		$size         = !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$maxLength    = !empty($this->maxLength) ? ' maxlength="' . $this->maxLength . '"' : '';
		$class        = !empty($this->class) ? $this->class : '';
		$readonly     = $this->readonly ? ' readonly' : '';
		$disabled     = $this->disabled ? ' disabled' : '';
		$required     = $this->required ? ' required aria-required="true"' : '';
		$hint         = $hint ? ' placeholder="' . $hint . '"' : '';
		$autocomplete = !$this->autocomplete ? ' autocomplete="off"' : ' autocomplete="' . $this->autocomplete . '"';
		$autocomplete = $autocomplete == ' autocomplete="on"' ? '' : $autocomplete;
		$autofocus    = $this->autofocus ? ' autofocus' : '';
		$spellcheck   = $this->spellcheck ? '' : ' spellcheck="false"';
		$pattern      = !empty($this->pattern) ? ' pattern="' . $this->pattern . '"' : '';
		$inputmode    = !empty($this->inputmode) ? ' inputmode="' . $this->inputmode . '"' : '';
		$dirname      = !empty($this->dirname) ? ' dirname="' . $this->dirname . '"' : '';

		// Initialize JavaScript field attributes.
		$onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		// Including fallback code for HTML5 non supported browsers.
		JHtml::_('jquery.framework');
		JHtml::_('script', 'system/html5fallback.js', false, true);

		$name    = $this->name;
		$id      = $this->id;
		$options = $this->getOptions();

		$b_value = array('m' => '', 'u' => '');
		$value   = array_merge($b_value, array_intersect_key((array) $this->value, $b_value));

		$layoutData = compact('hint', 'size', 'maxLength', 'class', 'readonly', 'disabled', 'required',
			'autocomplete', 'autofocus', 'spellcheck', 'pattern', 'inputmode', 'dirname', 'onchange',
			'name', 'id', 'options', 'value');

		return JLayoutHelper::render('com_sellacious.formfield.' . $this->type, $layoutData, '', array('client' => 2, 'debug' => 0));
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  stdClass[]  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$helper = SellaciousHelper::getInstance();
		$groups = (string) $this->element['unit_group'];

		return $helper->unit->loadObjectList(array('list.select' => 'a.id, a.title', 'unit_group' => $groups, 'state' => 1));
	}
}
