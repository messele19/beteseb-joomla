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

/**
 * Status controller class.
 */
class SellaciousControllerStatus extends SellaciousControllerForm
{
	/**
	 * Constructor.
	 *
	 * @param  array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerForm
	 * @since   3.0
	 */
	public function __construct(array $config)
	{
		parent::__construct($config);

		$this->registerTask('setStockHandlingA', 'setStockHandling');
		$this->registerTask('setStockHandlingR', 'setStockHandling');
		$this->registerTask('setStockHandlingO', 'setStockHandling');
	}

	/**
	 * @var string
	 */
	protected $view_list = 'statuses';

	/**
	 * @var  string  The prefix to use with controller messages.
	 *
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_SELLACIOUS_STATUS';

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array $data An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	protected function allowAdd($data = array())
	{
		return $this->helper->access->check('status.create');
	}

	/**
	 * Method to check if you can edit an existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data An array of input data.
	 * @param   string $key  The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return $this->helper->access->check('status.edit');
	}

	/**
	 * Just to update session state with selected context so that form is rebuilt accordingly
	 *
	 * @return  bool
	 */
	public function setContext()
	{
		JSession::checkToken() or die('JINVALID_TOKEN');

		$app  = JFactory::getApplication();
		$data = $app->input->post->get('jform', array(), 'array');

		$app->setUserState('com_sellacious.edit.status.data', $data);

		$this->setRedirect(JRoute::_('index.php?option=com_sellacious&view=status&layout=edit', false));

		return true;
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function setStockHandling()
	{
		JSession::checkToken() or die('JINVALID_TOKEN');

		$app = JFactory::getApplication();
		$cid = $app->input->post->get('cid', array(), 'array');
		$pk  = (int) reset($cid);

		$value = str_replace('SET' . 'STOCK' . 'HANDLING', '', strtoupper($this->getTask()));

		$this->setRedirect(JRoute::_('index.php?option=com_sellacious&view=statuses', false));

		if (!in_array($value, array('', 'A', 'R', 'O')))
		{
			$this->setMessage(JText::_($this->text_prefix . '_INVALID_HANDLING'), 'warning');
		}

		try
		{
			$model = $this->getModel();
			$model->setStockHandling($pk, $value);

		}
		catch (Exception $e)
		{
			$this->setMessage($e->getMessage(), 'error');

			return false;
		}

		$this->setMessage(JText::_($this->text_prefix . '_STOCK_HANDLING_UPDATE_SUCCESS'), 'message');

		return true;
	}
}
