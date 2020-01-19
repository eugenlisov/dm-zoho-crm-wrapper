<?php
namespace DM_ZCRM\Models;

class User extends ZohoCRMUsers {


    // public function __construct( $data = [] ) {
    //     parent::__construct();
    // }

    // This instantiates the model so we can go ahead and create a Zoho CRM Record with it.
    public static function new( $data = [] ) {

        // self::$module = 'user';
        $instance = new static( $data );

        return $instance;
    }


}
