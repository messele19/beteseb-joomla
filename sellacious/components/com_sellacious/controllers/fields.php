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
 * Fields list controller class.
 */
class SellaciousControllerFields extends SellaciousControllerAdmin
{
	/**
	 * @var string
	 */
	protected $text_prefix = 'COM_SELLACIOUS_FIELDS';

	/**
	 * Proxy for getModel.
	 *
	 * @param  string  $name
	 * @param  string  $prefix
	 * @param  array   $config
	 *
	 * @return object
	 */
	public function getModel($name = 'Field', $prefix = 'SellaciousModel', $config = Array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return  bool  False on failure or error, true on success.
	 *
	 * @since   1.6
	 */
	public function rebuild()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$allowed = $this->helper->access->check('field.rebuild');

		$this->setRedirect(JRoute::_('index.php?option=com_sellacious&view=fields', false));

		if (!$allowed)
		{
			$this->setMessage(JText::_('COM_SELLACIOUS_ACCESS_NOT_ALLOWED'), 'error');

			return false;
		}

		$model = $this->getModel();

		if ($model->rebuild())
		{
			$this->setMessage(JText::_($this->text_prefix . '_REBUILD_SUCCESS'));

			return true;
		}
		else
		{
			$this->setMessage(JText::sprintf($this->text_prefix . '_REBUILD_FAILURE', $model->getError()), 'error');

			return false;
		}
	}
}
