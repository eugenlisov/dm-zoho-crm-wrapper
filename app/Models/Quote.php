<?php

namespace DM_ZCRM\Models;

class Quote extends ZohoCRMModules
{
	static $module = 'Quotes';


	protected static function extract_module_specific_details($record = '', $return = '', $args = [])
	{
		$return['products'] = static::extract_products($record);
		$return = static::calculateTotal($return); // Requires $return['products'] to be calculated by extract_products()
		$return['dm_account_id'] = static::extract_account_id($record); // NB: Need to extract this because searching for a Books Contact doesn't work just with the CRM Account ID.
		$return['dm_contact_id'] = static::extract_contact_id($record); // NB: Need to extract this because searching for a Books Contact doesn't work just with the CRM Account ID.
		$return['dm_potential_name'] = static::extract_potential_name($record); // NB: Need to extract this because to populate the value in the 'Notes' field on the Books Estimate.

		if (! empty($args['load_quote_owner']) && $args['load_quote_owner'] && (! empty($return['quoteownerid2']))) {
			$return['dm_owner'] = User::get($return['quoteownerid2']);
		}

		return $return;
	}


	protected static function extract_products($record)
	{

		$line_items = $record->getLineItems();

		$products = [];

		foreach ($line_items as $key => $item) {
			$product      = $item->getProduct();
			$product_id   = $product->getEntityId();
			$product_name = $product->getLookupLabel();


			$current_product = [
				'id'                  => $product_id,
				'name'                => $product_name,
				'list_price'          => $item->getListPrice(),
				'quantity'            => $item->getQuantity(),
				'description'         => $item->getDescription(),
				'discount'            => $item->getDiscount(),
				'discount_percentage' => $item->getDiscountPercentage(),
				'total'               => $item->getTotal(),
				'total_after_discount' => $item->getTotalAfterDiscount(),
			];

			$products[] = $current_product;
		}

		return $products;
	}

	protected static function calculateTotal($return)
	{
		$total = 0;
		$totalAfterDiscounts = 0;

		foreach ($return['products'] as $key => $lineItem) {
			$total += $lineItem['total'];
			$totalAfterDiscounts += $lineItem['total_after_discount'];
		}

		$return['total'] = $total;
		$return['total_after_discount'] = $totalAfterDiscounts;

		return $return;
	}



	/**
	 * Because the account name is found under many layers of abstracted classes, we need to dig for it.
	 * @param  string $record [description]
	 * @return string $account_name      [description]
	 */
	public static function extract_account_id($record = '')
	{

		$account = $record->getData();
		$account_name = $account['Account_Name'];
		$account_id = $account_name->getEntityId();
		return $account_id;
	}

	public static function extract_contact_id($record = '')
	{

		$quote = $record->getData();
		$contact_name = $quote['Contact_Name'];
		if (empty($contact_name)) return '';
		$contact_id = $contact_name->getEntityId();
		return $contact_id;
	}


	/**
	 * Works on quote Quote
	 * TODO: Move to Quote model
	 * NOTE: The Potential record is called 'Deal_Name' when it comes in the API response.
	 * @param  string $record [description]
	 * @return [type]         [description]
	 */
	public static function extract_potential_name($record = '')
	{

		$account = $record->getData();
		$potential = $account['Deal_Name'];
		$potential_name = $potential->getLookupLabel();
		return $potential_name;
	}
}
