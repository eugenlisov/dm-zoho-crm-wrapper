<?php
namespace DM_ZCRM\Models;

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\crud\ZCRMModule;
use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\oauth\ZohoOAuth;
use zcrmsdk\crm\setup\users\ZCRMUser;
use zcrmsdk\crm\setup\org\ZCRMOrganization;

// NOTE: This is copied from the RMA Plugin and adapted
// Uses the PHP SDK library provided by Zoho

class ZohoCRMUsers extends ZohoCRM { // Not an abstract because we instantiate it at one point.

    // static $module; // NOTE: Plural. Ex: Accounts / Contacts / Potentials / Quotes / Leads/ Must be defined in child, before the __construct runs.
    // static $module_instance;

    static $organization_instance;

    /**
     * Initializez the new Zoho v.2.0 API.
     */
    public function __construct() {
        parent::__construct();

        self::$organization_instance = ZCRMOrganization::getInstance();

    }


    /**
     * Likely the most direct method to retrieve a particular record from the CRM by its ID.
     * Ex:
     * Quote::get($quote_id);
     * Product::get($product_id);
     * Account::get($account_id);
     *
     * @param  string $quote_id [description]
     * @return [type]           [description]
     */
    public static function get( $record_id = '' ) {

        static::new();

        try {
            $bulkAPIResponse = self::$organization_instance -> getUser($record_id);
        } catch (\ZCRMException $e) {
            // return [];
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        $user = $bulkAPIResponse -> getData();
        $user = static::extract_user_details( $user );

        return $user;

    }


    /**
     * Extracts only the fields we actually need, not all.
     * @param  string $user [description]
     * @return Array       [description]
     */
    public static function extract_user_details( $user = '' ) {

        $return = [
            'id'        => $user -> getId(),
            'full_name' => $user -> getFullName(),
            'email'     => $user -> getEmail(),
            'created'   => $user -> getCreatedTime(),
        ];

        return $return;

    }


    // public static function list( $batch_size = 20, $page = 1) {
    //
    //     $instance = static::new();
    //
    //
    //     // Markup: getRecords($cvId = null, $sortByField = null, $sortOrder = null, $startIndex = 1, $endIndex = 200, $headers = null)
    //     // $startIndex = ( $page - 1 ) * $batch_size + 1;
    //     // $endIndex = $page * $batch_size;
    //     // echo '<br />Start index: ' . $startIndex;
    //     // echo '<br />End index: ' . $endIndex;
    //     // return;
    //     $bulkAPIResponse = static::$module_instance->getRecords( null, null, null, $page, $batch_size );
    //
    //     $records = $bulkAPIResponse->getData(); // $records - array of ZCRMRecord instances.
    //
    //     // Transform the array of ZCRMRecord into a simple "array of array" with the record ID as index.
    //     $return = [];
    //     foreach ($records as $key => $record) {
    //         $id      = $record -> getEntityId();
    //         $created = $record -> getCreatedTime();
    //
    //         $return[$id] = $record->getData();
    //
    //         // For products we need to extract the products in a particular format.
    //         if ( self::$module == 'Quotes' ) {
    //             $return[$id]['products'] = self::extract_products( $record );
    //         }
    //
    //         // Attach the Records ID and Created times.
    //         $return[$id]['id'] = $id;
    //         $return[$id]['created'] = $created;
    //     }
    //
    //     return $return;
    //
    //     echo '<pre>';
    //     print_r( count( $return ) );
    //     print_r( $return );
    //     echo '</pre>';
    //
    // }



}
