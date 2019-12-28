<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;

/* Route::get('/', function () {
    return view('welcome');
}); */

Auth::routes();

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

//Clear route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return 'Routes cache cleared';
});

//Clear config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return 'Config cache cleared';
});

// Clear application cache:
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return 'Application cache cleared';
});

// Clear view cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return 'View cache cleared';
});


//------------------ Admin Login  Routes --------------------------

	
Route::group(['prefix' => 'admin','namespace'=>'Admin'], function () { 
	Route::any('/', function() {
		return redirect('/admin/login');
	} );
	Route::any('login', 'UserController@login');
	Route::any('logout', function(){ Auth::logout();return redirect('/admin/login');} );
});

Route::any('admin/order/pdf_view/{id}', 'Admin\OrdersController@pdf_view');
Route::any('admin/order/po/create_pdf/{id}/', 'Admin\OrderPOController@create_pdf' );

Route::group( [ 'prefix' => 'admin' ,'middleware' => ['admin'],'namespace'=>'Admin'], function()
{	
	Route::any('dashboard/{field?}/{sort?}', 'HomeController@index');
	Route::any('profile', 'HomeController@myaccount' );
	Route::any('ChangePassword', 'HomeController@changepassword');
	Route::any('chat', 'HomeController@chat_message');
	Route::any('home/events', 'HomeController@events' );
	Route::any('home/messages', 'HomeController@messages' );
	Route::any('events', 'HomeController@agent_events');
	Route::any('settings', 'SettingsController@index');
	
	Route::group(['prefix'=>'agents','as'=>'agents'], function(){
		Route::any('lists/{field?}/{sort?}', 'AgentController@lists');
		Route::any('add', 'AgentController@add');
		Route::any('action/{id}/{status}', 'AgentController@action');
		Route::any('edit/{id}', 'AgentController@edit');
		Route::any('view/{id}', 'AgentController@view');
		Route::any('delete/{id}', 'AgentController@delete_agent');
	});

    Route::group(['prefix'=>'designers','as'=>'agents'], function(){
		Route::any('lists/{field?}/{sort?}', 'DesignerController@lists');
		Route::any('add', 'DesignerController@add');
		Route::any('action/{id}/{status}', 'DesignerController@action');
		Route::any('edit/{id}', 'DesignerController@edit');
		Route::any('view/{id}', 'DesignerController@view');
		Route::any('delete/{id}', 'DesignerController@delete_designer');
	});	
	
	Route::group(['prefix'=>'vendors','as'=>'vendors'], function(){
		Route::any('lists/{field?}/{sort?}', 'VendorController@lists');
		//Route::any('add', function(){ echo "hello";die; });
		Route::any('add', 'VendorController@add');
		Route::any('action/{id}/{status}', 'VendorController@action');
		Route::any('edit/{id}', 'VendorController@edit');
		Route::any('view/{id}', 'VendorController@view');
		Route::any('delete/{id}', 'VendorController@delete_vendor');
	});	
	
	Route::group(['prefix'=>'users','as'=>'users'], function(){
		Route::any('lists/{field?}/{sort?}', 'UserController@lists' );
		Route::any('add', 'UserController@add' );
		Route::any('action/{id}/{status}', 'UserController@action' );
		Route::any('edit/{id}', 'UserController@edit' );
		Route::any('view/{id}', 'UserController@view' );
		Route::any('notes', 'UserController@notes' );
		Route::any('events', 'UserController@events' );
		Route::any('selectBox', 'UserController@events' );
		Route::any('excel', 'UserController@excel' );
	});	
	
	Route::group(['prefix'=>'staticpages','as'=>'staticpages'], function(){
		Route::any('add', 'StaticpagesController@add' );
		Route::any('lists/{field?}/{sort?}', 'StaticpagesController@lists' );
		Route::any('action/{id}/{status}', 'StaticpagesController@action' );
		Route::any('edit/{id}', 'StaticpagesController@edit' );
		Route::any('view/{id}', 'StaticpagesController@view' );
	});	
	
	Route::group(['prefix'=>'emails','as'=>'emails'], function(){
		Route::any('add', 'EmailsController@add' );
		Route::any('lists/{field?}/{sort?}', 'EmailsController@lists' );
		Route::any('action/{id}/{status}', 'EmailsController@action' );
		Route::any('edit/{id}', 'EmailsController@edit' );
		Route::any('view/{id}', 'EmailsController@view' );
	});	
			
	Route::group(['prefix'=>'products','as'=>'products'], function(){
		Route::any('add', 'ProductsController@add' );
		Route::any('lists/{field?}/{sort?}', 'ProductsController@lists' );
		Route::any('delete/{id}', 'ProductsController@delete_product' );
		Route::any('action/{id}/{status}', 'ProductsController@action' );
		Route::any('edit/{id}', 'ProductsController@edit' );
		Route::any('edit_basic/{id}', 'ProductsController@basic_edit' );
		Route::any('edit_price/{id}', 'ProductsController@price_edit' );
		Route::any('edit_variants/{id}', 'ProductsController@edit_variants' );
		Route::any('view/{id}', 'ProductsController@view' );
		Route::any('delete_image', 'ProductsController@delete_image');
		Route::any('addOption', 'ProductsController@add_option');
		Route::any('deleteData', 'ProductsController@delete_data');
		Route::any('deleteTab', 'ProductsController@delete_tab');
		
		/* Add Shipping*/
		Route::any('shipping/{id}', 'ProductsController@addShipping');
		
		/* Custom Option Route */
		
		Route::any('custom/option/add', 'ProductsController@custom_add');
		Route::any('custom/option/lists/{field?}/{sort?}', 'ProductsController@custom_lists');
		Route::any('custom/option/action/{id}/{status}', 'ProductsController@custom_action');
		Route::any('custom/option/edit/{id}', 'ProductsController@custom_edit');
		Route::any('custom/option/view/{id}', 'ProductsController@custom_view');
	});	
			
	Route::group(['prefix'=>'category','as'=>'category'], function(){
		Route::any('add', 'CategoriesController@add' );
		Route::any('lists/{field?}/{sort?}', 'CategoriesController@lists' );
		Route::any('action/{id}/{status}', 'CategoriesController@action' );
		Route::any('edit/{id}', 'CategoriesController@edit' );
		Route::any('view/{id}', 'CategoriesController@view' );
		
		Route::any('menucategory/add', 'CategoriesController@addmenu' );
		Route::any('menucategory/edit/{id}', 'CategoriesController@editmenu' );
		Route::any('menucategory/lists', 'CategoriesController@listsmenu' );
	});
			
	Route::group(['prefix'=>'order','as'=>'order'], function(){
		Route::any('add', 'OrdersController@add' );
		Route::any('lists/{field?}/{sort?}', 'OrdersController@lists' );
		Route::any('estimates/{field?}/{sort?}', 'OrdersController@estimates' );
		Route::any('archived/{field?}/{sort?}', 'OrdersController@archived' );
		Route::any('view/{id}', 'OrdersController@view' );
		Route::any('edit/applycoupon', 'OrdersController@editApplyCoupon' );
		Route::any('edit/{id}/{type?}', 'OrdersController@edit' );
		Route::any('order-mail/{id}/', 'OrdersController@orderMail' );		
		Route::any('delete/{order_id}', 'OrdersController@delete' );
		Route::any('get_product_by_category/{id}', 'OrdersController@get_product_by_category' );
		Route::any('get_product_options/{id}', 'OrdersController@get_product_options' );
		Route::any('formvalidate/', 'OrdersController@formvalidate' );
		Route::any('add_to_cart/', 'OrdersController@add_to_cart' );
		Route::any('cart_products/', 'OrdersController@cart_products' );
		Route::any('useraddress/', 'OrdersController@useraddress' );
		Route::any('assign_agent/', 'OrdersController@assign_agent' );
		Route::any('assign_designer/', 'OrdersController@assign_designer' );
		Route::any('assign_vendor/', 'OrdersController@assign_vendor' );
		Route::any('notes/', 'OrdersController@notes');
		Route::any('events/', 'OrdersController@events');
		Route::any('messages/', 'OrdersController@messages');
		Route::any('productaddress/', 'OrdersController@productaddress');
		Route::any('status/', 'OrdersController@status');
		Route::any('set_value/', 'OrdersController@set_value');
		Route::any('order_edit/', 'OrdersController@order_edit');
		Route::any('order_option_edit/', 'OrdersController@order_option_edit');
		Route::any('calculate_shipping/{orderId}/{shippingOption}', 'OrdersController@calculateShippingRate');
		Route::any('order_values_edit/', 'OrdersController@order_values_edit');
		Route::any('order_save/', 'OrdersController@order_save');
		Route::any('save_order_details/', 'OrdersController@save_order_details');
		Route::any('order_changes/', 'OrdersController@order_changes');
		Route::any('savecomment/', 'OrdersController@savecomment');
		Route::any('print_view/{id}', 'OrdersController@print_view');
		Route::any('createinvoice/{id}', 'OrdersController@createinvoice');		
		Route::any('bookshipping', 'OrdersController@bookShipping');		
		Route::any('tracking/{track_id}', 'OrdersController@tracking');	
		Route::any('shipping/', 'OrdersController@shipping');
		Route::any('applycoupon/', 'OrdersController@applyCoupon');
		Route::any('change-status/{id}/{status}/{action?}', 'OrdersController@change_status');
		Route::any('test/', 'OrdersController@test');
		Route::any('invoice_email/', 'OrdersController@invoice_email');
		
		Route::any('/product-clone', 'OrdersController@productClone');
		
		Route::any('/cart/products-edit', 'OrdersController@editProduct');
		
		Route::post('/product-delete', 'OrdersController@productDelete');
		
		Route::any('/cart/product-clone', 'OrdersController@cartProductClone');
		
		// Create PO //
		
		Route::any('po/create/{id}/{product_id}', 'OrderPOController@create_po');
		Route::post('po/option_edit/', 'OrderPOController@option_edit');
		Route::post('po/address_edit/', 'OrderPOController@address_edit');
		Route::post('po/address/save', 'OrderPOController@address_save');
		Route::post('po/option/save', 'OrderPOController@option_save');
		Route::any('po/save_po', 'OrderPOController@save_po' );
		Route::any('po/product_delete', 'OrderPOController@product_delete' );
		Route::any('po/send_po', 'OrderPOController@send_po' );
		Route::any('po/change_vendor', 'OrderPOController@change_vendor' );
		Route::any('po/delete_option', 'OrderPOController@delete_option' );
		Route::any('po/{id}', 'OrderPOController@po' );
		
		Route::any('po/mail/{id}/', 'OrderPOController@po_mail' );
		Route::any('po/print/{id}/', 'OrderPOController@print_pdf' );
		Route::any('po/pdf/{id}/{type}', 'OrderPOController@genrate_pdf' );
	});
			
	Route::group(['prefix'=>'menu','as'=>'menu'], function(){
		Route::any('add', 'MenusController@add' );
		Route::any('lists', 'MenusController@lists' );
		Route::any('delete/{id}', 'MenusController@delete' );
		Route::any('action/{id}/{status}', 'MenusController@action' );
		Route::any('edit/{id}', 'MenusController@edit' );
		Route::any('sorting', 'MenusController@sorting' );
	});
			
	Route::group(['prefix'=>'discount','as'=>'discount'], function(){
		Route::any('add', 'DiscountController@add' );
		Route::any('lists/{field?}/{sort?}', 'DiscountController@lists' );
		Route::any('action/{id}/{status}', 'DiscountController@action' );
		Route::any('edit/{id}', 'DiscountController@edit' );
	});
			
	Route::group(['prefix'=>'coupon','as'=>'coupon'], function(){
		Route::any('add', 'CouponController@add' );
		Route::any('lists/{field?}/{sort?}', 'CouponController@lists' );
		Route::any('action/{id}/{status}', 'CouponController@action' );
		Route::any('edit/{id}', 'CouponController@edit' );
		Route::any('mail', 'CouponController@mail_send');
	});
			
	Route::group(['prefix'=>'slider','as'=>'discount'], function(){
		Route::any('add', 'SlidersController@add' );
		Route::any('lists/{field?}/{sort?}', 'SlidersController@lists' );
		Route::any('action/{id}/{status}', 'SlidersController@action' );
		Route::any('view/{id}', 'SlidersController@view' );
		Route::any('edit/{id}', 'SlidersController@edit' );
		Route::any('delete/{id}', 'SlidersController@sliderDelete');
	});
			
	Route::group(['prefix'=>'testimonial','as'=>'discount'], function(){
		Route::any('add', 'TestimonialsController@add' );
		Route::any('lists/{field?}/{sort?}', 'TestimonialsController@lists' );
		Route::any('action/{id}/{status}', 'TestimonialsController@action' );
		Route::any('view/{id}', 'TestimonialsController@view' );
		Route::any('edit/{id}', 'TestimonialsController@edit' );
		Route::any('delete/{id}', 'TestimonialsController@testimonialDelete');
	});

	Route::group(['prefix'=>'reviews','as'=>'reviews'], function(){		
		Route::any('lists/{field?}/{sort?}', 'ReviewsController@lists' );
		Route::any('action/{id}/{status}', 'ReviewsController@action' );
		Route::any('view/{id}', 'ReviewsController@view' );		
		Route::any('edit/{id}', 'ReviewsController@edit' );		
		Route::any('delete/{id}', 'ReviewsController@reviewDelete');
	});
				
	Route::group(['prefix'=>'reports','as'=>'discount'], function(){
		Route::any('orders', 'ReportsController@Orders' );
		Route::any('orders/export', 'ReportsController@OrderExport' );
		Route::any('sales', 'ReportsController@Sales' );
		Route::any('sales/export', 'ReportsController@SalesExport' );
		Route::any('customers', 'ReportsController@Customers' );
		Route::any('customers/export', 'ReportsController@CustomerExport' );
		Route::any('po-exports', 'ReportsController@poExports' );
		Route::any('customers/export', 'ReportsController@CustomerExport' );
	});
					
	Route::group(['prefix'=>'home','as'=>'home_page'], function(){
		Route::any('top-blue', 'HomePageController@top_blue' );
		Route::any('customers-logo-add', 'HomePageController@customers_logos_add' );
		Route::any('customers-logo-edit/{id}', 'HomePageController@customers_logos_edit' );
		Route::any('customers-logo-list', 'HomePageController@customers_logos_list' );
		Route::any('logo-action/{id}/{status}', 'HomePageController@customer_logo_action' );
		
		Route::any('images', 'HomePageController@images' );
		
		Route::any('carousel1', 'HomePageController@carousel1' );
		Route::any('carousel2', 'HomePageController@carousel2' );
		Route::any('carousel3', 'HomePageController@carousel3' );
		Route::any('carousel4', 'HomePageController@carousel4' );
	});
			
	Route::group(['prefix'=>'actions','as'=>'action'], function(){
		Route::any('delete', 'ActionsController@delete' );
		Route::any('update', 'ActionsController@update' );
	});		
	Route::group(['prefix'=>'test','as'=>'test'], function(){
		Route::any('form', 'EmailsController@test' );
	});

	// quickbook routes
	Route::group(['prefix'=>'quickbook','as'=>'quickbook'], function(){
		Route::any('exporttoqb/{order_id?}', 'QuickbookController@exportToQB' );
		Route::any('callback', 'QuickbookController@callback' );
		Route::any('query', 'QuickbookController@getQuery' );
	});
});

//--------------------- End Admin Login Routes------------------------ 	


//----------------------- Front End Routes --------------------

Route::any('/logout', function () {
	Auth::logout();
	return redirect('/');
});

Route::group( [], function()
{
    Route::any('/uploads/generate/{key}', 'UploadsController@generateUploadUrl');
	Route::any('/', 'HomeController@index');
	Route::any('/home', 'HomeController@index');
	Route::any('/loginbyemail/{email}', 'HomeController@loginByEmail');	
	Route::any('/thank', function(){
		return View::make("home.thank");
	});
	Route::get('/activate/{token}', 'HomeController@activate');
	
	Route::get('/contactus', 'HomeController@contactus');
	Route::post('/contactus', 'HomeController@contactus_save');
	
	Route::get('/custom-quotes', 'HomeController@customeQuote');
	Route::get('/volume-discounts', 'HomeController@volumeDiscount');
	Route::post('/custom-quotes', 'HomeController@customeQuote_save');
	Route::post('/volume-discounts', 'HomeController@volume_discount_save');
	Route::get('/oversized-banner', 'HomeController@oversized_banner');
	Route::post('/oversized-banner', 'HomeController@oversized_banner_save');
	Route::any('/testimonials', 'HomeController@testimonials');
	Route::any('/myaccount', 'MyAccountController@myaccount');
	Route::any('/change-password', 'MyAccountController@changePassword');
	Route::any('/addresses', 'MyAccountController@addresses');
	Route::any('/cards', 'MyAccountController@cards');
	Route::any('/card/edit/{id}', 'MyAccountController@cardEdit');
	Route::any('/card/delete/{id}', 'MyAccountController@deleteCard');
	Route::any('/edit/address/{id}', 'MyAccountController@editAddress');
	Route::any('/delete/address/{id}', 'MyAccountController@deleteAddress');
	Route::any('/orders', 'MyAccountController@myOrders');
	Route::any('/order/tracking/{track_id}', 'MyAccountController@tracking');
	Route::any('/order/view/{id}/{print?}', 'MyAccountController@ViewOrder');
	Route::any('/order/print/{id}', 'MyAccountController@PrintOrder');
	Route::get('/my-artwork-files/', 'MyAccountController@myArtWorkFiles');
	Route::get('/my-artwork-files-delete/{id}', 'MyAccountController@myArtWorkFilesDelete');	

	Route::post('/my-artwork-files-upload/', 'MyAccountController@myArtWorkFilesUpload');

	
	Route::any('order/review/{orderId}', 'MyAccountController@orderReview');
	
	Route::any('/review/{orderId}/{productId}', 'MyAccountController@review');
	Route::any('/reviews/get', 'PagesController@review_get');
	Route::any('/subscriber', 'HomeController@subscriber');
	Route::any('/email', 'HomeController@email');

	/* Route::get('/pages/{slug}', 'PagesController@pages');
	Route::get('/category/{slug}', 'CategoriesController@detail');	
	Route::get('/product/{slug}', 'ProductsController@detail'); */
	
	Route::any('/cart/add', 'CartsController@add');			
	Route::any('/cart', 'CartsController@view');	
	Route::any('/cart/delete', 'CartsController@delete');
	Route::any('/cart/update', 'CartsController@update');
	Route::post('/applycoupon', 'CartsController@applyCoupon');
	Route::post('/payment/link/applycoupon', 'OrderController@paymentLinkCoupon');
	
	Route::any('/cart/checkout', function(){
		//pr(Auth::user());
		if (Auth::guest()) {
			   return Redirect::guest('/login');
		}else{
			return redirect()->route('checkout');
		}
	});
	
	Route::any('/shop', 'ProductsController@shop');
	
	Route::any('/send-quote', 'ProductsController@send_quote');	
	Route::any('/get-shipping', 'ProductsController@get_shipping');	
	
	Route::any('/checkout', 'CheckoutsController@checkout')->name('checkout');	
	Route::any('/payment', 'CheckoutsController@payment')->name('payment');	
	Route::any('/saveAddress', 'CheckoutsController@saveAddress');	
	Route::any('/savecomment', 'CartsController@savecomment');
	
	Route::any('/cart/product-clone', 'CartsController@productClone');
	
	Route::any('/saveOrder', 'CheckoutsController@saveOrder');	
	Route::any('/uploads/{id}', 'CheckoutsController@uploads');	
	Route::post('/upload-fiels/{id}', 'CheckoutsController@uploadArtworkFiles');
	Route::post('/saveFiles/{id}', 'CheckoutsController@saveUploadFiles');

	Route::any('/multiple/addresses', 'CheckoutsController@multipleAddresses');	
	Route::any('/payment/authorize', 'CheckoutsController@Authorizepayment');	
	Route::any('invoice_receipt/{id}', 'CheckoutsController@invoice_receipt');	
	
	Route::any('/order-payment/{id}', 'OrderController@order_payment');
	/* Route::any('/order-payment/{id}', function($id){
		if (Auth::guest()) {
			   return Redirect::guest('/login');
		}else{
			return redirect('/pending-payment/'.$id);
		}
	});
	Route::any('/pending-payment/{id}', 'OrderController@order_payment')->name('pending-payment'); */

	Route::any('/pending-order/multiple/addresses', 'OrderController@multipleAddresses');	
	Route::any('/order/updatePayment', 'OrderController@updatePayment');	
	Route::any('/order-pending/saveAddress', 'OrderController@saveAddress');	
	Route::any('/payment-link/authorize', 'OrderController@Authorizepayment');
	
	Route::get('/authorizeCheck/payment', 'PagesController@authorizeCheck');	
	
	//tflow routes
	Route::any('/tflow/job_approve', 'TflowsController@job_approve');	
		
	Route::get('{slug}', 'PagesController@slugview');		
});


\DB::enableQueryLog();
		
function pr($data){
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}

function qLog(){
	$querydata =  (\DB::getQueryLog());
	pr($querydata);
}