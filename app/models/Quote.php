<?php
namespace DM_ZCRM\Models;

class Quote extends ZohoCRMModules {

    // This instantiates the model so we can go ahead and create a Zoho CRM Record with it.
    public static function new( $data = [] ) {

        self::$module = 'Quotes';
        $instance = new static( $data );

        return $instance;
    }


}
