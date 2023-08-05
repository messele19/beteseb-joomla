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
 * Product controller class.
 */
class SellaciousControllerLocation extends SellaciousControllerForm
{
	/**
	 * @var  string  The prefix to use with controller messages.
	 *
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_SELLACIOUS_LOCATION';

	/**
	 * Provides autocomplete interface to javascript functions
	 *
	 * @return  void
	 */
	public function autoComplete()
	{
		$app      = JFactory::getApplication();
		$query    = $app->input->get('query');
		$parent   = $app->input->getInt('parent_id');
		$types    = $app->input->get('types', array(), 'array');
		$add_type = $app->input->getString('address_type');

		try
		{
			/** @var  SellaciousModelLocation $model */
			$model    = $this->getModel('Location');
			$data     = $model->suggest(trim($query), $types, $parent, $add_type);
			$response = array('status' => 1, 'message' => '', 'data' => $data);
		}
		catch (Exception $e)
		{
			$response = array('status' => 0, 'message' => $e->getMessage(), 'data' => array());
		}

		header('content-type: application/json');
		echo json_encode($response);
		jexit();
	}

	/**
	 * Get details of given item
	 *
	 * @throws  Exception
	 */
	public function getInfoAjax()
	{
		$app   = JFactory::getApplication();
		$keys  = $app->input->get('id', array(), 'array');
		$types = $app->input->get('types', array(), 'array');

		$keys = Joomla\Utilities\ArrayHelper::toInteger($keys);

		try
		{
			/** @var  SellaciousModelLocation $model */
			$model    = $this->getModel('Location');
			$items    = $model->getInfo($keys, $types);
			$response = array('status' => 1, 'message' => '', 'data' => $items);
		}
		catch (Exception $e)
		{
			$response = array('status' => 0, 'message' => $e->getMessage(), 'data' => array());
		}

		header('content-type: application/json');
		echo json_encode($response);
		jexit();
	}
}
