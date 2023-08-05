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
 * List controller class
 */
class SellaciousControllerActivation extends SellaciousControllerBase
{
	/**
	 * @var   string  The prefix to use with controller messages.
	 *
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_SELLACIOUS_ACTIVATION';

	/**
	 * Constructor.
	 *
	 * @param  array $config An optional associative array of configuration settings.
	 *
	 * @see    JController
	 * @since  3.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('unpublishAjax', 'publishAjax');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name
	 * @param   string  $prefix
	 * @param   array   $config
	 *
	 * @return  JModelLegacy
	 */
	public function getModel($name = 'Activation', $prefix = 'SellaciousModel', $config = null)
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

	/**
	 * Activate the software
	 *
	 * @return  void
	 */
	public function activateAjax()
	{
		$app  = JFactory::getApplication();
		$data = $app->input->get('data', array(), 'array');

		try
		{
			/** @var  SellaciousModelActivation $model */
			$model = $this->getModel();
			$model->register($data, true);

			$response = array(
				'status'  => 1,
				'message' => JText::_($this->text_prefix . '_ACTIVATION_SUCCESS'),
				'data'    => $data,
			);
		}
		catch (Exception $e)
		{
			$response = array(
				'status'  => 0,
				'message' => $e->getMessage(),
				'data'    => null,
			);
		}

		echo json_encode($response);

		jexit();
	}

	/**
	 * Update the license subscription information for this software, always syncs from the license server
	 *
	 * @return  void
	 */
	public function upgradeAjax()
	{
		$app  = JFactory::getApplication();
		$data = $app->input->get('data', array(), 'array');

		try
		{
			/** @var  SellaciousModelActivation $model */
			$model = $this->getModel();
			$model->subscription($data);

			$response = array(
				'status'  => 1,
				'message' => '',
				'data'    => $data,
			);
		}
		catch (Exception $e)
		{
			$response = array(
				'status'  => 0,
				'message' => $e->getMessage(),
				'data'    => null,
			);
		}

		echo json_encode($response);

		jexit();
	}

	/**
	 * Activate the software
	 *
	 * @return  void
	 */
	public function registerAjax()
	{
		$app = JFactory::getApplication();

		$data['name']     = $app->input->getString('name');
		$data['email']    = $app->input->getString('email');
		$data['sitekey']  = $app->input->getString('sitekey');
		$data['version']  = $app->input->getString('version');
		$data['sitename'] = $app->input->getString('sitename');
		$data['siteurl']  = $app->input->getString('siteurl');

		try
		{
			$data = array_filter($data, 'trim');

			if (count($data) < 6)
			{
				throw new Exception(JText::_($this->text_prefix . '_INVALID_REGISTRATION_DATA'));
			}

			/** @var  SellaciousModelActivation $model */
			$model = $this->getModel();
			$model->register($data);

			$response = array(
				'status'  => 1,
				'message' => JText::_($this->text_prefix . '_REGISTRATION_SUCCESS'),
				'data'    => $data,
			);
		}
		catch (Exception $e)
		{
			$response = array(
				'status'  => 0,
				'message' => $e->getMessage(),
				'data'    => null,
			);
		}

		echo json_encode($response);

		jexit();
	}
}
