<?php
namespace DM_ZCRM\Models;

class Account extends ZohoCRMModules {


    // public function __construct( $data = [] ) {
    //     parent::__construct();
    // }

    // This instantiates the model so we can go ahead and create a Zoho CRM Record with it.
    public static function new( $data = [] ) {

        self::$module = 'Accounts';
        $instance = new static( $data );

        return $instance;
    }

}