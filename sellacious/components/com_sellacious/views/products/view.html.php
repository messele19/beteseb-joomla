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
 * View class for a list of products.
 *
 * @since  1.0.0
 */
class SellaciousViewProducts extends SellaciousViewList
{
	/**
	 * @var  string
	 *
	 * @since   1.4.7
	 */
	protected $action_prefix = 'product';

	/**
	 * @var  string
	 *
	 * @since   1.4.7
	 */
	protected $view_item = 'product';

	/**
	 * @var  string
	 *
	 * @since   1.4.7
	 */
	protected $view_list = 'products';

	/**
	 * Add the page title and the toolbar.
	 *
	 * @since   1.0.0
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');

		$this->setPageTitle();

		if ($this->_layout != 'bulk')
		{
			if ($this->helper->access->check('product.edit'))
			{
				JToolbarHelper::custom('products.refreshCache', 'refresh', 'refresh', 'JTOOLBAR_REFRESH_CACHE', false);
			}

			if (file_exists(JPATH_BASE . '/components/com_importer/importer.php'))
			{
				// Todo: Task needs to be adjusted to call com_importer
				JToolBarHelper::custom($this->view_list . '.export', 'export', 'export', 'COM_SELLACIOUS_PRODUCTS_EXPORT_PRODUCTS', false);
			}

			if ($this->helper->access->check('product.create'))
			{
				if (file_exists(JPATH_BASE . '/components/com_importer/importer.php'))
				{
					// Todo: Task needs to be adjusted to call com_importer
					JToolBarHelper::custom($this->view_list . '.csvTemplate', 'download', 'download', 'COM_SELLACIOUS_IMPORTER_IMPORT_CSV_SAMPLE_DOWNLOAD', false);
				}

				JToolBarHelper::addNew('product.add', 'JTOOLBAR_NEW');
				JToolBarHelper::addNew('products.duplicate', 'COM_SELLACIOUS_PRODUCT_DUPLICATE_BUTTON');
			}
		}

		if (count($this->items))
		{
			if ($this->_layout == 'bulk')
			{
				if (!$this->helper->access->isSubscribed())
				{
					$app = JFactory::getApplication();

					if ($this->helper->config->get('free_forever', 0, 'sellacious', 'application'))
					{
						$app->enqueueMessage(JText::_('COM_SELLACIOUS_PREMIUM_FEATURE_NOTICE_INVENTORY_MANAGER'), 'premium');
					}
					else
					{
						$app->enqueueMessage(JText::_('COM_SELLACIOUS_PREMIUM_FEATURE_NOTICE_INVENTORY_MANAGER_TRIAL'), 'premium');
					}
				}
				elseif ($this->helper->access->checkAny(array('pricing', 'seller', 'pricing.own', 'seller.own'), 'product.edit.'))
				{
					JToolBarHelper::addNew('products.save', 'JTOOLBAR_APPLY', true);

					if ($this->helper->config->get('multi_variant'))
					{
						JToolBarHelper::custom('variants.manage', 'edit', 'edit', 'COM_SELLACIOUS_PRODUCTS_VARIANTS_BUTTON', true);
					}
				}
			}
			else
			{
				$actions  = array('basic', 'seller', 'pricing', 'shipping', 'related', 'seo',
					'basic.own', 'seller.own', 'pricing.own', 'shipping.own', 'related.own', 'seo.own');
				$editable = $this->helper->access->checkAny($actions, 'product.edit.');

				if ($editable)
				{
					JToolBarHelper::editList('product.edit', 'JTOOLBAR_EDIT');
				}

				if ($this->helper->access->checkAny(array('seller', 'seller.own'), 'product.edit.'))
				{
					JToolBarHelper::custom('products.setSelling', 'publish.png', 'publish_f2.png', 'COM_SELLACIOUS_PRODUCTS_SELLING_ENABLE', true);
					JToolBarHelper::custom('products.setNotSelling', 'unpublish.png', 'unpublish_f2.png', 'COM_SELLACIOUS_PRODUCTS_SELLING_DISABLE', true);
				}

				if ($this->helper->access->check('product.edit.state'))
				{
					$filter_state = $state->get('filter.state');

					if (!is_numeric($filter_state) || $filter_state != 1)
					{
						JToolBarHelper::custom('products.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
					}

					if (!is_numeric($filter_state) || $filter_state != 0)
					{
						JToolBarHelper::custom('products.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
					}

					if (!is_numeric($filter_state) || $filter_state != -2)
					{
						JToolBarHelper::trash('products.trash', 'JTOOLBAR_TRASH');
					}
					// If 'edit.state' is granted, then show 'delete' only if filtered on 'trashed' items
					elseif ($this->helper->access->checkAny(array('product.delete', 'product.delete.own')))
					{
						JToolBarHelper::deleteList('', 'products.delete', 'JTOOLBAR_DELETE');
					}
				}
				// We can allow direct 'delete' implicitly for his (seller) own items if so permitted.
				elseif ($this->helper->access->checkAny(array('product.delete', 'product.delete.own')))
				{
					JToolBarHelper::trash('products.delete', 'JTOOLBAR_DELETE');
				}

				// Todo: verify permissions usage for this
				if ($this->helper->listing->isApplicable())
				{
					JToolBarHelper::custom('products.listing', 'edit.png', 'edit.png', 'COM_SELLACIOUS_PRODUCTS_LISTING_BUTTON', true);
				}
			}
		}
	}

	/**
	 * Get a form object for the bulk editor view
	 *
	 * @param   int  $index  The repeat index
	 *
	 * @return  JForm
	 *
	 * @since   1.0.0
	 */
	public function getRepeatableForm($index)
	{
		$form = JForm::getInstance('com_sellacious.products.row', 'product_bulk_price', array('control' => 'jform'));

		if ($form instanceof JForm)
		{
			$me   = JFactory::getUser();
			$item = Joomla\Utilities\ArrayHelper::getValue($this->items, $index);

			$form->repeatCounter = $index;

			if ($this->helper->config->get('pricing_model') == 'flat')
			{
				$form->setFieldAttribute('price', 'mode', 'flat');
			}

			if (!($this->helper->access->check('product.edit.pricing') ||
				($this->helper->access->check('product.edit.pricing.own') && $item->seller_uid == $me->id)))
			{
				$form->setFieldAttribute('price', 'readonly', 'true');
			}

			if (!($this->helper->access->check('product.edit.seller') ||
				($this->helper->access->check('product.edit.seller.own') && $item->seller_uid == $me->id)))
			{
				$form->setFieldAttribute('stock', 'readonly', 'true');
			}

			if (!$this->helper->access->isSubscribed())
			{
				$form->setFieldAttribute('price', 'readonly', 'true');
				$form->setFieldAttribute('stock', 'readonly', 'true');
			}

			$data             = new stdClass;
			$data->seller_uid = $item->seller_uid;
			$data->stock      = $item->stock;
			$data->price      = array(
				'id'               => $item->price_id,
				'cost_price'       => $item->cost_price,
				'margin'           => $item->margin,
				'margin_type'      => $item->margin_type,
				'list_price'       => $item->list_price,
				'calculated_price' => $item->calculated_price,
				'ovr_price'        => $item->ovr_price,
			);

			$form->bind($data);
		}

		return $form;
	}
}
