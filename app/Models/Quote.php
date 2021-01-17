<?php
namespace DM_ZCRM\Models;

use com\zoho\crm\api\record\InventoryLineItems;

class Quote extends BaseRecord {

	public $module = 'Quotes';

	public static function extractProducts(Array $record) {

		$return = [];

		foreach ($record['Product_Details'] as $key => $item) {
			$return[] = static::extractLineItem($item);
		}

		return $return;
	}

	public static function extractLineItem(InventoryLineItems $item) {
		$product = $item->getProduct();

		$return = [
			'id'                  => $product->getId(),
			'name'                => $product->getName(),
			'list_price'          => $item->getListPrice(),
			'quantity'            => $item->getQuantity(),
			'description'         => $item->getProductDescription(),
			'discount'            => (int) $item->getDiscount(),
			'discount_percentage' => 0, // NB: There is no method to get this.
		];

		return $return;
	}
	
	

	// Everything below is deprecated
	

    protected static function extract_module_specific_details( $record = '', $return = '', $args = []) {
        $return['products'] = static::extract_products( $record );
		$return['dm_account_id'] = static::extract_account_id( $record ); // NB: Need to extract this because searching for a Books Contact doesn't work just with the CRM Account ID.
		$return['dm_contact_id'] = static::extract_contact_id( $record ); // NB: Need to extract this because searching for a Books Contact doesn't work just with the CRM Account ID.
        $return['dm_potential_name'] = static::extract_potential_name( $record ); // NB: Need to extract this because to populate the value in the 'Notes' field on the Books Estimate.

        if ( ! empty( $args['load_quote_owner'] ) && $args['load_quote_owner'] && ( ! empty( $return['quoteownerid2'] ) ) ) {
            $return['dm_owner'] = User::get( $return['quoteownerid2'] );
        }

        return $return;

    }


    protected static function extract_products( $record ) {

        $line_items = $record->getLineItems();

        $products = [];

        foreach ($line_items as $key => $item) {
            $product      = $item -> getProduct();
            $product_id   = $product -> getEntityId();
            $product_name = $product -> getLookupLabel();


            $current_product = [
                'id'                  => $product_id,
                'name'                => $product_name,
                'list_price'          => $item->getListPrice(),
                'quantity'            => $item->getQuantity(),
                'description'         => $item->getDescription(),
                'discount'            => $item->getDiscount(),
                'discount_percentage' => $item->getDiscountPercentage(),
            ];

            $products[] = $current_product;

        }

        return $products;

    }



    /**
     * Because the account name is found under many layers of abstracted classes, we need to dig for it.
     * @param  string $record [description]
     * @return string $account_name      [description]
     */
    public static function extract_account_id( $record = '' ) {

        $account = $record->getData();
        $account_name = $account['Account_Name'];
        $account_id = $account_name -> getEntityId();
        return $account_id;

	}
	
	public static function extract_contact_id( $record = '' ) {

        $quote = $record->getData();
		$contact_name = $quote['Contact_Name'];
		if (empty($contact_name)) return '';
        $contact_id = $contact_name -> getEntityId();
        return $contact_id;

    }


    /**
     * Works on quote Quote
     * TODO: Move to Quote model
     * NOTE: The Potential record is called 'Deal_Name' when it comes in the API response.
     * @param  string $record [description]
     * @return [type]         [description]
     */
    public static function extract_potential_name( $record = '' ) {

        $account = $record->getData();
        $potential = $account['Deal_Name'];
        $potential_name = $potential -> getLookupLabel();
        return $potential_name;

    }

}
