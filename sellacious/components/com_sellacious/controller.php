<?php
/**
 * @version     1.5.3
 * @package     sellacious
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     SPL Sellacious Private License; see http://www.sellacious.com/spl.html
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
// No direct access
defined('_JEXEC') or die;

class SellaciousController extends SellaciousControllerBase
{
	/**
	 * Method to display a view.
	 *
	 * @param   bool   $cacheable  if true, the view output will be cached
	 * @param   mixed  $urlparams  An array of safe url parameters and their variable types,
	 *                             for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  This object to support chaining.
	 * @since   1.5
	 */
	public function display($cacheable = false, $urlparams = false)
	{
		$view = $this->input->get('view', 'dashboard');
		$this->input->set('view', $view);

		if (!$this->helper->core->isRegistered())
		{
			$this->input->set('view', 'activation');
		}
		elseif (!$this->helper->core->isConfigured())
		{
			$this->input->set('view', 'setup');
		}
		elseif (!$this->canView())
		{
			$tmpl   = $this->input->get('tmpl', null);
			$suffix = !empty($tmpl) ? '&tmpl=' . $tmpl : '';
			$return = JRoute::_('index.php?option=com_sellacious' . $suffix, false);

			if ($tmpl != 'raw')
			{
				$this->setRedirect($return);

				JLog::add(JText::_('COM_SELLACIOUS_ACCESS_NOT_ALLOWED'), JLog::WARNING, 'jerror');
			}

			return $this;
		}

		return parent::display($cacheable, $urlparams);
	}

	/**
	 * Checks whether a user can see this view.
	 *
	 * @return  bool
	 *
	 * @since   1.6
	 */
	protected function canView()
	{
		$app    = JFactory::getApplication();
		$view   = $app->input->get('view', 'dashboard');
		$layout = $app->input->get('layout');
		$format = $app->input->get('format');
		$id     = $app->input->getInt('id', null);
		$basic  = array(
			'option' => 'com_sellacious',
			'view'   => $view,
			'layout' => $layout,
			'format' => $format,
		);
		$query  = $app->input->get->getArray();
		$query  = array_merge($basic, $query);

		if ($layout == 'edit')
		{
			$editId = $app->getUserState('com_sellacious.edit.' . $view . '.id');

			if ($id === null && $editId)
			{
				$query['id'] = $editId;

				// We should allow default edit id if not set in Request URI
				$app->redirect(JRoute::_('index.php?' . http_build_query($query), false));
			}

			if ($editId != $id)
			{
				/**
				 * Somehow the person just went to the form - we don't allow that.
				 * But instead of stopping him just switch the context
				 * if already editing something else, clear it from session to prevent data in new form
				 */
				$query['id'] = $id;
				$app->setUserState('com_sellacious.edit.' . $view . '.id', $id);
				$app->setUserState('com_sellacious.edit.' . $view . '.data', null);
				$app->redirect(JRoute::_('index.php?' . http_build_query($query), false));
			}
		}

		/**
		 * Todo: Below we assume all (backend) singular views to be edit layout only.
		 * Todo: This may not be true. 'create' etc permissions need to be checked too.
		 */
		switch ($view)
		{
			case 'dashboard':
				$allow = true;
				break;
			case 'coupons':
				$allow = $this->canList('coupon');
				break;
			case 'coupon':
				$allow = $this->canEdit('coupon');
				break;
			case 'orders':
				$allow = $this->canList('order');
				break;
			case 'products':
				$allow = $this->canList('product');
				break;
			case 'product':
				$actions = array('basic', 'seller', 'pricing', 'shipping', 'related', 'seo',
					'basic.own', 'seller.own', 'pricing.own', 'shipping.own', 'related.own', 'seo.own');
				$allow   = $this->helper->access->checkAny($actions, 'product.edit.');
				break;
			case 'ratings':
				$allow = $this->canList('rating');
				break;
			case 'rating':
				$allow = false;
				break;
			case 'mailqueue':
				$allow = $this->canList('mailqueue');
				break;
			case 'activation':
				$allow = $this->helper->access->check('config.edit');
				break;
			case 'setup':
			case 'config':
				$allow = $this->helper->access->check('config.edit');
				break;
			case 'emailtemplates':
				$allow = $this->helper->access->checkAny(array('list', 'edit'), 'emailtemplate.');
				break;
			case 'emailtemplate':
				$allow = $this->helper->access->checkAny(array('list', 'edit'), 'emailtemplate.');
				break;
			case 'location':
				$allow = $this->helper->access->checkAny(array('edit', 'edit.own'), 'location.');
				break;
			case 'locations':
				$allow = $this->helper->access->checkAny(array('list', 'list.own'), 'location.');
				break;
			case 'licenses':
				$allow = $this->helper->access->check('license.list');
				break;
			case 'license':
				$allow = $this->helper->access->check('license.edit');
				break;
			case 'permissions':
				$allow = $this->helper->access->check('permissions.edit');
				break;
			case 'categories':
				$allow = $this->helper->access->check('category.list');
				break;
			case 'category':
				$allow = $this->helper->access->check('category.edit');
				break;
			case 'shoprules':
				$allow = $this->helper->access->check('shoprule.list');
				break;
			case 'shoprule':
				$allow = $this->helper->access->check('category.edit');
				break;
			case 'splcategories':
				$allow = $this->helper->access->check('splcategory.list');
				break;
			case 'splcategory':
				$allow = $this->helper->access->check('splcategory.edit');
				break;
			case 'fields':
				$allow = $this->helper->access->check('field.list');
				break;
			case 'field':
				$allow = $this->helper->access->check('field.edit');
				break;
			case 'units':
				$allow = $this->helper->access->check('unit.list');
				break;
			case 'unit':
				$allow = $this->helper->access->check('unit.edit');
				break;
			case 'currencies':
				$allow = $this->helper->access->check('currency.list');
				break;
			case 'currency':
				$allow = $this->helper->access->check('currency.edit');
				break;
			case 'users':
				$allow = $this->helper->access->check('user.list');
				break;
			case 'user':
				$allow = $this->helper->access->check('user.edit');
				break;
			case 'profile':
				$allow = $this->helper->access->check('user.edit.own');
				break;
			case 'productlisting':
				// fixme: check appropriate rule
				$allow = true; // $this->helper->access->check('product.edit');
				break;
			case 'variants':
				// fixme: check appropriate rule
				$allow = true; // $this->helper->access->check('product.edit');
				break;
			case 'shippingrules':
				$allow = $this->helper->access->check('shippingrule.list');
				break;
			case 'shippingrule':
				$allow = $this->helper->access->checkAny(array('edit', 'edit.own'), 'shippingrule.');
				break;
			case 'statuses':
				$allow = $this->helper->access->check('status.list');
				break;
			case 'status':
				$allow = $this->helper->access->check('status.edit');
				break;
			case 'order':
				$allow = true;
				break;
			case 'relatedproducts':
				$allow = $this->helper->access->check('product.edit.related');
				break;
			case 'transactions':
				$allow = $this->helper->access->checkAny(array('list', 'list.own'), 'transaction.');
				break;
			case 'transaction':
				$allow = $this->helper->access->checkAny(array('direct', 'direct.own', 'gateway', 'gateway.own'), 'transaction.addfund.')
						|| $this->helper->access->checkAny(array('withdraw', 'withdraw.own'), 'transaction.');
				break;
			case 'messages':
				$allow = $this->helper->access->checkAny(array('list', 'list.own'), 'message.');
				break;
			case 'message':
				$allow = $this->helper->access->checkAny(array('create', 'reply', 'reply.own'), 'message.');
				break;
			case 'downloads':
				$allow = $this->helper->access->checkAny(array('list', 'list.own'), 'download.');
				break;
			case 'paymentmethods':
				$allow = $this->helper->access->check('paymentmethod.list');
				break;
			case 'paymentmethod':
				$allow = $this->helper->access->check('paymentmethod.edit');
				break;
			default:
				$allow = false;
		}

		return $allow;
	}

	/**
	 * Check whether the user can view the singular/edit view or not
	 *
	 * @param   string  $asset
	 *
	 * @return  bool
	 *
	 * @since   1.0.0
	 */
	protected function canEdit($asset)
	{
		$b = $this->helper->access->check($asset . '.create') ||
			$this->helper->access->check($asset . '.edit') ||
			$this->helper->access->check($asset . '.edit.own');

		return $b;
	}

	/**
	 * Check whether the user can view the plural/list view or not
	 *
	 * @param   string  $asset
	 *
	 * @return  bool
	 *
	 * @since   1.0.0
	 */
	protected function canList($asset)
	{
		$b = $this->helper->access->check($asset . '.list') ||
			$this->helper->access->check($asset . '.list.own');

		return $b;
	}

	/**
	 * Rebuild backoffice menu from the xml
	 *
	 * @since   1.5.0
	 */
	public function rebuildMenu()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_sellacious'));

		if ($this->helper->access->check('config.edit'))
		{
			$this->helper->core->rebuildMenu(true);
		}
	}
}
