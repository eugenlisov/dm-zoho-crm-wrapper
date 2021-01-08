<?php

namespace Tests\Unit\Models;

use com\zoho\crm\api\record\InventoryLineItems;
use com\zoho\crm\api\record\LineItemProduct;
use DM_ZCRM\Models\Quote;
use Tests\CustomTestCase;

class QuoteTest extends CustomTestCase
{
	// User both to build the expected value, as well as the input models that get processed
	private $rawItem = [
		'id'                  => 200327000129184021,
		'name'                => 'Some Product Name',
		'list_price'          => 66,
		'quantity'            => 3,
		'description'         => 'PSM module connection for Mercedes Sprinters (907 and 910 Chassis) for Global markets (requires HRN-CM24Y1).',
		'discount'            => 0,
		'discount_percentage' => 0,
	];

    public function testItExtractsLineItem()
    {
		$inventoryLineItem = $this->buildDummyInventoryLineItem();

		$this->assertEquals($this->expectedLineItem(), Quote::extractLineItem($inventoryLineItem));
	}

	public function testGivenMultipleProducts_ItExtractsAndArrayOfExtractedProducts()
    {
		$inventoryLineItem = $this->buildDummyInventoryLineItem();
		$productDetails = [
			$inventoryLineItem, $inventoryLineItem, $inventoryLineItem
		];
		
		$extractedLineItem = Quote::extractLineItem($inventoryLineItem);
		$expectedResult = [$extractedLineItem, $extractedLineItem, $extractedLineItem];

		$this->assertEquals($expectedResult, Quote::extractProducts(['Product_Details' => $productDetails]));
	}

	

	private function buildDummyInventoryLineItem() {

		$product = $this->buildDummyProduct();
		
		$inventoryLineItem = new InventoryLineItems;
		$inventoryLineItem->setProduct($product);
		$inventoryLineItem->setListPrice($this->rawItem['list_price']);
		$inventoryLineItem->setQuantity($this->rawItem['quantity']);
		$inventoryLineItem->setDiscount($this->rawItem['discount']);
		$inventoryLineItem->setProductDescription($this->rawItem['description']);

		return $inventoryLineItem;
	}

	private function buildDummyProduct() {
		$product = new LineItemProduct;
		$product->setId($this->rawItem['id']);
		$product->setName($this->rawItem['name']);

		return $product;
	}
	

	private function expectedLineItem() {
		return $this->rawItem;
	}
}
