<?php
namespace DM_ZCRM\Models;

class Lead extends ZohoCRMModules {

    // This instantiates the model so we can go ahead and create a Zoho CRM Record with it.
    public static function new( $data = [] ) {

        self::$module = 'Leads';
        $instance = new static( $data );

        return $instance;
    }


}
