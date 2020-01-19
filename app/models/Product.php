<?php
namespace DM_ZCRM\Models;

class Product extends ZohoCRMModules {

    static $module = 'Products';


    
    // $args = [
    //     'page'       => 2,
    //     'per_page'   => 20,
    //     'fields'     => 'Product_Name,Product_Category,Description,Unit_Price,Setup_Process,Product_Active',
    //     'sort_by'    => 'Product_Active',
    //     'sort_order' => 'desc',
    // ];
    // See documentation here: https://www.zoho.com/crm/developer/docs/api/get-records.html
    public static function listProducts( $args = [] ) {

        $page       = ( ! empty( $args['page'] ) ) ? $args['page'] : 1;
        $per_page   = ( ! empty( $args['per_page'] ) ) ? $args['per_page'] : 20;
        $fields     = ( ! empty( $args['fields'] ) ) ? $args['fields'] : 'Product_Name,Product_Category,Description,Unit_Price,Setup_Process,Product_Active';
        $sort_by    = ( ! empty( $args['sort_by'] ) ) ? $args['sort_by'] : 'Product_Active';
        $sort_order = ( ! empty( $args['sort_order'] ) ) ? $args['sort_order'] : 'desc';
        
        $full_args = [
            'page'       => $page,
            'per_page'   => $per_page,
            'fields'     => $fields,
            'sort_by'    => $sort_by,
            'sort_order' => $sort_order,
        ];

        return static::list( $full_args );
        
    }


    /**
     * Returns a list with all the Active Products in the ZohoCRM
     * It only returns the detauls fields from the listProducts() method above.
     */
    public static function listAllProducts() {

        $products = [];
        for ($page=1; $page < 20; $page++) { 

            $full_args = [
                'page'       => $page,
                'per_page'   => 200,
            ];

            $current_batch = static::listProducts( $full_args );
            if ( empty( $current_batch ) ) break;


            foreach ($current_batch as $key => $current_product) {
                if ( $current_product['Product_Active'] != 1 ) unset ( $current_batch[$key] );
            }

            if ( empty( $current_batch ) ) break;

            $products = array_merge( $products, $current_batch );


        }

        return $products;
        
    }


}
