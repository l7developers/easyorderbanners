<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
       'admin/products/delete_image',
       'admin/products/addOption',
       'admin/products/deleteData',
       'admin/products/deleteTab',
       'admin/order/get_product_by_category/{id}',
       'admin/order/get_product_options/{id}',
       'admin/order/formvalidate',
       'admin/order/add_to_cart',
       'admin/order/cart_products',
       'admin/order/useraddress',
       'admin/order/productaddress',
       'admin/order/assign_agent',
       'admin/order/assign_designer',
       'admin/order/assign_vendor',
       'admin/order/order_edit',
       'admin/order/order_option_edit',
       'admin/order/order_values_edit',
       'admin/order/order_changes',
       'admin/order/savecomment',
       'admin/order/notes',
       'admin/order/events',
       'admin/order/messages',
       'admin/order/status',
       'admin/order/set_value',
       'admin/order/shipping',
       'admin/menu/sorting',
	   'admin/users/notes',
	   'admin/users/events',
	   'admin/home/events',
	   'admin/home/messages',
	   'admin/order/bookshipping',
	   'admin/actions/delete',
	   'admin/actions/update',
	   'admin/order/po/change_vendor',
	   'admin/order/po/option_edit',
	   'admin/order/po/address_edit',
	   'admin/order/po/delete_option',
	   'subscriber',
	   'cart/add',
	   'cart/delete',
	   'saveAddress',
	   'saveOrder',
	   'payment/authorize',
	   'savecomment',
	   'multiple/addresses',
	   'reviews/get',
	   'admin/coupon/mail',
	   '/order/updatePayment',
	   '/applycoupon',
	   '/payment/link/applycoupon',
		'/send-quote',
		'/get-shipping',
	   '/tflow/job_approve',
		'pending-order/multiple/addresses',
    ];
}
