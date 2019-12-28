<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use DB;
use App\Orders;
use App\User;
use App\Products;
use App\Setting;

// quickbook related class
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\SalesReceipt;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Line;

/* SELECT `id`, `total`, `qb_id` FROM `orders` WHERE `qb_id` IS NOT NULL
SELECT `id`, `name`, `qb_id` FROM `products` WHERE `qb_id` IS NOT NULL
SELECT `id`, `fname`, `lname`, `email`, `qb_id` FROM `users` WHERE `qb_id` IS NOT NULL

SalesItemLineDetail -> ItemRef for shipping fee. for this need to get id of a product named "Shipping" from qb and set in invoice and sales
Need to get PA Sales Tax rate id from qb and  set in invoice and sales
need to get IncomeAccountRef from qb and set in create item 

shipping and handingl : 193
Vinyl Banner Production : 190
Pennsylvania sales tax : 8

*/

class QuickbookController extends Controller
{
	//public $clientID="Q0laV7ZImpNHn5tW9C6JbPy2Yq4YeHaljbtCT4By0yWIXrvV27"; // development
	public $clientID="Q0ywNv5eimZHHoIuMrH9OpRu8BnAr3toHsnyCpgI5LdLTZcWC7";

	//public $clientSecret="whxlsho49j4c9E0usku612MojvQv4BLJzJjnjV74"; // development
	public $clientSecret="JJcDmumh8cio7lygEbnXqcTKxFuYmX6kF7AB7Ymq";

	public $redirectURI="https://easyorderbanners.com/admin/quickbook/callback";

	public $scope="com.intuit.quickbooks.accounting";

	//public $baseUrl="Development";
	public $baseUrl="Production";

	public $dataService = null;

	// https://beta.easyordertablethrows.com/admin/quickbook/exporttoqb/8
	
	public function __construct(){		
		/* if($_SERVER['SERVER_NAME'] == 'eoblocal.com')
		{
			$this->redirectURI = "http://eoblocal.com/easyorderbanner/admin/quickbook/callback";
		}  */		      
    }

	/**
    get authrization code from quickbook
    */
    public function  login()
    {   
    	 	
    	$dataService = DataService::Configure(array(
			'auth_mode' => 'oauth2',
			'ClientID' => $this->clientID,
			'ClientSecret' => $this->clientSecret,
			'RedirectURI' => $this->redirectURI.'',
			'scope' => $this->scope,
			'baseUrl' => $this->baseUrl,			
			//'state' => session()->get('qb.order_id')			
		));

		$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

		$authorizationCodeUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
		
		return redirect()->to($authorizationCodeUrl)->send();
    }

    /**
    redirected on this fucntion after authorization from quickbook so we can get accesstoken and refresh token
    */
    public function  callback()
    {    	
    	$dataService = DataService::Configure(array(
			'auth_mode' => 'oauth2',
			'ClientID' => $this->clientID,
			'ClientSecret' => $this->clientSecret,
			'RedirectURI' => $this->redirectURI,
			'scope' => $this->scope,
			'baseUrl' => $this->baseUrl			
		));

		$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

		try{

			$accessTokenObj = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($_GET['code'], $_GET['realmId']);
			
			$accessTokenValue = $accessTokenObj->getAccessToken();
			$refreshTokenValue = $accessTokenObj->getRefreshToken();

			session()->put('qb.accessToken',$accessTokenValue);
			session()->put('qb.refreshToken',$refreshTokenValue);
			session()->put('qb.realmId',$_GET['realmId']);
			//session()->put('qb.order_id',$_GET['state']);

			$this->processOrder();
			//$this->getQuery();
		}
		catch(\Exception $e)		
		{
			session()->forget('qb');
			echo $e->getMessage();
		}

		return "";
  		
    }


    public function  refreshToken()
    {
    	$dataService = DataService::Configure(array(
			'auth_mode' => 'oauth2',
			'ClientID' => $this->clientID,
			'ClientSecret' => $this->clientSecret,			
			'refreshTokenKey' => session()->get('qb.refreshToken'),
			'QBORealmID' => session()->get('qb.realmId'),
			'baseUrl' => $this->baseUrl
		));

		$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

		try{

			$accessTokenObj = $OAuth2LoginHelper->refreshToken();
			
			$accessTokenValue = $accessTokenObj->getAccessToken();			

			session()->put('qb.accessToken',$accessTokenValue);

		}
		catch(\Exception $e)		
		{
			session()->forget('qb');
			echo $e->getMessage();
		}

		return "";
  		
    }

	/**
	this function will be called when user click on export button from order list
	@params $order_id order_id which need to export to quickbook
	*/
    public function exportToQB($order_id="")
    {    	
    	//session()->put('qb.order_id',$order_id);  qb_ids   
    	Setting::where('name','qb_ids')->update(['value'=>$order_id]);	

    	if(session()->get('qb.accessToken')=="")	
    	{
    		$this->login();
    	}    	

    	$this->processOrder();    	
    	//$this->getQuery();
    	   	
    	return "";

    }

    /**
    this function will export order into quickbook
    */
    public function processOrder(){
    	
    	//$order_id = session()->get('qb.order_id');    	
    	
    	$this->dataService = DataService::Configure(array(
			'auth_mode' => 'oauth2',
			'ClientID' => $this->clientID,
			'ClientSecret' => $this->clientSecret,
			'accessTokenKey' =>session()->get('qb.accessToken'),
			'refreshTokenKey' => session()->get('qb.refreshToken'),
			'QBORealmID' => session()->get('qb.realmId'),
			'baseUrl' => $this->baseUrl
		));

		$qb_ids = Setting::where('name','qb_ids')->first();;
		$order_arr = explode(',', $qb_ids->value);		
		foreach($order_arr as $order_id)
		{				
			$this->processOrder_ByOrderID($order_id);
		}
		return "";
		
    }

    /**
    this function will export order into quickbook
    */
    public function processOrder_ByOrderID($order_id){    	

	    $orderData = Orders::where('id', $order_id)->with(['customer','orderProduct','orderAddress'])->first();
    		
		if($orderData->customer->qb_id == "")
		{
			$this->createCustomer($orderData);
		}
		
		/* foreach($orderData->orderProduct as $data)
		{
			if($data->product->qb_id == "")
			{
				$this->createItem($data->product);
			}				
		} */

		if($orderData->qb_id >= 1)
		{
			echo "<h4>Order ID #".$orderData->id."($".$orderData->total.") Already Sync on Quickbook</h4>";
			return "";
		}
		
		if($orderData->payment_method == 'pay_by_invoice' || $orderData->payment_status == 7)
		{
			$this->createInvoice($order_id);
		}
		else
		{
			$this->createSales($order_id);
		}
		
		//pr(session()->get('qb'));
		//session()->forget('qb');		
		//pr($orderData);

		return "";  
	}	

    /**
    this function will export order as invoice into quickbook    
    @reuturn boolen
    @reference doc : https://developer.intuit.com/docs/api/accounting
    */

    public function createInvoice($order_id){    	
    	    	
    	//$order_id = session()->get('qb.order_id');    	

    	$orderData = Orders::where('id', $order_id)->with(['customer','orderProduct','orderAddress'])->first();    		

    	$lineItem = array();

    	$shipping_tracking_number_arr= array();
    	$shipping_tracking_type_arr= array();

    	foreach($orderData->orderProduct as $key=>$data)
		{		
			$shipping_tracking_number_arr[]=$data->tracking_id;

			$taxVar ="NON";
			if($orderData->sales_tax > 0) 							
				$taxVar = "TAX"; //"NON/TAX" for no tax			
			
			$LineObj = Line::create([		       		      
				"Description"=>$data->product->name.' - '.$data->product_name,
		        "Amount" => $data->total,
		       	"DetailType" => "SalesItemLineDetail",
		       	"SalesItemLineDetail" => [
		           "ItemRef" => [
		               //"value" =>  $data->product->qb_id,		               
		               "value" =>  190,		               
		           ],
		           "UnitPrice" => $data->total / $data->qty,
		           "Qty" => $data->qty,			            
		           "TaxCodeRef" => [
               			"value" => $taxVar
           			]	           
		       ]
		   	]);

   			$lineItem[] = $LineObj;			
		}

		if($orderData->shipping_fee > 0) 				
		{
			$LineObj = Line::create([		       		      
		        "Amount" => $orderData->shipping_fee,
		       	"DetailType" => "SalesItemLineDetail",
		       	"SalesItemLineDetail" => [
		           "ItemRef" => [
		               "value" =>  193,		               
		           ],
		           "UnitPrice" => $orderData->shipping_fee,
		           "Qty" => 1,			            
		           "TaxCodeRef" => ["value" => "NON"]	           
		       ]
		   	]);

   			$lineItem[] = $LineObj;			 	
		}	

		if($orderData->discount > 0) 				
		{
			$lineItem[] =[
			    	//"LineNum" => 2,
			    	"Amount" =>$orderData->discount,
			    	"DetailType"=>"DiscountLineDetail",
			    	"DiscountLineDetail"=>["PercentBased"=>false]
		   	];			 	
		}

		$sales_tax =[];
		if($orderData->sales_tax > 0) 				
		{
			$sales_tax = [
		 		"TotalTax"=>$orderData->sales_tax,
		 		"TxnTaxCodeRef"=>["value"=>"8"],
		 	];		 	
		}	

		$shipping_tracking_number_str = implode(",",$shipping_tracking_number_arr);	
		$ship_method_ref = "";
		if($orderData->shipping_option != "")	
		{
			$ship_method_ref = config('constants.Shipping_option.'.$orderData->shipping_option);
		}

		/* $terms =[];
		if($orderData->terms == 1) 				
		{
			$terms = [
		 		"value"=>11, // Net 20
		 	];		 	
		}
		else if($orderData->terms == 2) 				
		{
			$terms = [
		 		"value"=>5,// due on receipt
		 	];		 	
		}	*/

		$invoiceObj = Invoice::create([
			  	"Line" => $lineItem,	
			    "CustomerRef"=>[
			      "value"=> $orderData->customer->qb_id, //Customer.id			      
			    ],
			    "BillEmail" => [
        		    "Address" => $orderData->customer->email
  				],
			    "BillAddr" => [
			     "Line1"=> $orderData->orderAddress->billing_add1,
			     "City"=>  $orderData->orderAddress->billing_city,
			     "Country"=>  "USA",
			     "CountrySubDivisionCode"=>  $orderData->orderAddress->billing_state,
			     "PostalCode"=>  $orderData->orderAddress->billing_zipcode
			 	],
			 	"TxnTaxDetail"=>$sales_tax,
			 	//"SalesTermRef"=>$terms,
			 	"ApplyTaxAfterDiscount"=>true,
			 	"PaymentRefNum" => $orderData->payment_method.' - '.$orderData->payment_id,
			 	//"CustomField" =>["DefinitionId"=>"1","StringValue"=>"#".$orderData->id],			 	
			 	//"TrackingNum" =>$shipping_tracking_number_str,
			 	"DocNumber" =>$orderData->id,		 								 				 	
			 	//"TrackingNum" =>$orderData->id,
			 	"ShipMethodRef" =>$ship_method_ref,			 				 	
			]);

			//pr($invoiceObj);
			//return "";

			$resultingObj = $this->dataService->Add($invoiceObj);
			$error = $this->dataService->getLastError();
			if ($error) {
				echo "Error in create invoice<br/>";
			    echo "The Status code is: " . $error->getHttpStatusCode() . "<br/>";
			    echo "The Helper message is: " . $error->getOAuthHelperError() . "<br/>";
			    echo "The Response message is: " . $error->getResponseBody() . "<br/>";
			    $this->refreshToken();
			    return "";
			    
			} else {			   			   
			    echo "<h4>Order ID #".$orderData->id."($".$orderData->total.") Invoice created with #ID ".$resultingObj->Id."  on Quickbook</h4>";
			  	DB::update("update orders set qb_id='".$resultingObj->Id."' where id=".$orderData->id);
			}

    	return "";
   	}

    /**
    this function will export order into quickbook    
    @reuturn boolen
    @reference doc : https://developer.intuit.com/docs/api/accounting
    */

    public function createSales($order_id){    	
    	    	
    	//$order_id = session()->get('qb.order_id');    	

    	$orderData = Orders::where('id', $order_id)->with(['customer','orderProduct','orderAddress'])->first();    		

    	$lineItem = array();

    	$shipping_tracking_number_arr= array();
    	$shipping_tracking_type_arr= array();

    	foreach($orderData->orderProduct as $key=>$data)
		{	
			$shipping_tracking_number_arr[]=$data->tracking_id;

			$taxVar ="NON";
			if($orderData->sales_tax > 0) 							
				$taxVar = "TAX"; //"NON/TAX" for no tax			

			$LineObj = Line::create([	
				"Description"=>$data->product->name.' - '.$data->product_name,	       		      
		        "Amount" => $data->total,
		       	"DetailType" => "SalesItemLineDetail",
		       	"SalesItemLineDetail" => [
		           "ItemRef" => [
		               //"value" =>  $data->product->qb_id,		               
		               "value" =>  190,		               
		           ],
		           "UnitPrice" => $data->total / $data->qty,
		           "Qty" => $data->qty,	
		           "TaxCodeRef" => [
               			"value" => $taxVar
           			]	           
		       ]
		   	]);

   			$lineItem[] = $LineObj;			
		}	

		if($orderData->shipping_fee > 0) 				
		{
			$LineObj = Line::create([		       		      
		        "Amount" => $orderData->shipping_fee,
		       	"DetailType" => "SalesItemLineDetail",
		       	"SalesItemLineDetail" => [
		           "ItemRef" => [
		               "value" => 193,		               
		           ],
		           "UnitPrice" => $orderData->shipping_fee,
		           "Qty" => 1,			            
		           "TaxCodeRef" => ["value" => "NON"]	           
		       ]
		   	]);

   			$lineItem[] = $LineObj;			 	
		}

		if($orderData->discount > 0) 				
		{
			$lineItem[] =[
			    	//"LineNum" => 2,
			    	"Amount" =>$orderData->discount,
			    	"DetailType"=>"DiscountLineDetail",
			    	"DiscountLineDetail"=>["PercentBased"=>false]
		   	];			 	
		}

		$sales_tax =[];
		if($orderData->sales_tax > 0) 				
		{
			$sales_tax = [
		 		"TotalTax"=>$orderData->sales_tax,
		 		"TxnTaxCodeRef"=>["value"=>"8"],			 		
		 	];		 	
		}	

		$shipping_tracking_number_str = implode(",",$shipping_tracking_number_arr);	
		$ship_method_ref = "";
		if($orderData->shipping_option != "")	
		{
			$ship_method_ref = config('constants.Shipping_option.'.$orderData->shipping_option);
		}

		$salesReceiptObj = SalesReceipt::create([
			  	"Line" => $lineItem,	
			    "CustomerRef"=>[
			      "value"=> $orderData->customer->qb_id, //Customer.id			      
			    ],			    
			    "BillEmail" => [
        		    "Address" => $orderData->customer->email
  				],
			    "BillAddr" => [
			     "Line1"=> $orderData->orderAddress->billing_add1,
			     "City"=>  $orderData->orderAddress->billing_city,
			     "Country"=>  "USA",
			     "CountrySubDivisionCode"=>  $orderData->orderAddress->billing_state,
			     "PostalCode"=>  $orderData->orderAddress->billing_zipcode
			 	],
			 	"TxnTaxDetail"=>$sales_tax,
			 	"ApplyTaxAfterDiscount"=>true,
			 	"PaymentRefNum" => $orderData->payment_method.' - '.$orderData->payment_id,
			 	//"CustomField" =>["DefinitionId"=>"1","StringValue"=>"#".$orderData->id],			 	
			 	//"TrackingNum" =>$shipping_tracking_number_str,		 								 				 	
			 	"DocNumber" =>$orderData->id,
			 	//"TrackingNum" =>$orderData->id,
			 	"ShipMethodRef" =>$ship_method_ref,		 								 				 	
			]);						

			//pr($salesReceiptObj);
			//return "";

			$resultingObj = $this->dataService->Add($salesReceiptObj);
			$error = $this->dataService->getLastError();
			if ($error) {
				echo "Error in create sales<br/>";
			    echo "The Status code is: " . $error->getHttpStatusCode() . "<br/>";
			    echo "The Helper message is: " . $error->getOAuthHelperError() . "<br/>";
			    echo "The Response message is: " . $error->getResponseBody() . "<br/>";
			    $this->refreshToken();
			    return "";
			    
			} else {			   
			    //echo "<h2>Created Sales Id={$resultingObj->Id}. </h2><br/>";
			    echo "<h4>Order ID #".$orderData->id."($".$orderData->total.") Sales created with #ID ".$resultingObj->Id."  on Quickbook</h4>";
			  	DB::update("update orders set qb_id='".$resultingObj->Id."' where id=".$orderData->id);
			}

    	return "";
   	} 	

    /**
    this function will export product into quickbook
    @params $product contain product array
    @reuturn boolen
    */
    public function createItem($product){    	
		
		$Item = Item::create([
		      "Name" => $product->name,		      
		      "Active" => true,		     
		      //"UnitPrice" =>  $product->price,
		      "Type" => "NonInventory",		     
		      "IncomeAccountRef"=> [
		        "value"=> 398,
		        "name"=> "Production Income"
		      ],		    
		]);

		$resultingObj = $this->dataService->Add($Item);
		$error = $this->dataService->getLastError();
		if ($error) {			
			echo "Error in create item<br/>";
		    echo "The Status code is: " . $error->getHttpStatusCode() . "<br/>";
		    echo "The Helper message is: " . $error->getOAuthHelperError() . "<br/>";
		    echo "The Response message is: " . $error->getResponseBody() . "<br/>";
		    $this->refreshToken();
		    return "";
		}
		else {
		    //echo "Created Id={$resultingObj->Id}. Reconstructed response body:\n\n";
		   	DB::update("update products set qb_id='".$resultingObj->Id."' where id=".$product->id);
		}

		return true;
	}	


	/**
    this function will export customer into quickbook
    @params  $orderData order related data
    @return boolen
    */
    public function createCustomer($orderData){ 

    	$res = $this->checkCustomer($orderData);
    	if($res == true)
    	{
    		return true;
    	}

    	$displayName = $orderData->customer->fname.' '.$orderData->customer->lname;
    	if($orderData->customer->company_name !="")
    	{
    		$displayName = $orderData->customer->company_name;
    	}
    	$companyName = $orderData->customer->company_name;
    	if($orderData->customer->company_name =="")
    	{
    		$companyName = $orderData->customer->fname.' '.$orderData->customer->lname;
    	}

    			
		$customerObj = Customer::create([
			  "BillAddr" => [
			     "Line1"=> $orderData->orderAddress->billing_add1,
			     "City"=>  $orderData->orderAddress->billing_city,
			     "Country"=>  "USA",
			     "CountrySubDivisionCode"=>  $orderData->orderAddress->billing_state,
			     "PostalCode"=>  $orderData->orderAddress->billing_zipcode
			 ],

			 "CompanyName"=>  $companyName,
			 "DisplayName"=>  $displayName,
			 "GivenName"=>  $orderData->customer->fname,
			 "FamilyName"=> $orderData->customer->lname,
			 "PrimaryPhone"=>  [
			     "FreeFormNumber"=>  $orderData->customer->phone_number
			 ],
			 "PrimaryEmailAddr"=>  [
			     "Address" => $orderData->customer->email
			 ]
			]);		

			$resultingObj = $this->dataService->Add($customerObj);

			$error = $this->dataService->getLastError();
			if ($error) {
				echo "Error in create customer<br/>";
			    echo "The Status code is: " . $error->getHttpStatusCode() . "<br/>";
			    echo "The Helper message is: " . $error->getOAuthHelperError() . "<br/>";
			    echo "The Response message is: " . $error->getResponseBody() . "<br/>";
			    $this->refreshToken();
			    die('');
			    return "";
			} else {
				//var_dump($resultingObj);
			    DB::update("update users set qb_id='".$resultingObj->Id."' where id=".$orderData->customer->id);
			}

		return true;
	}	

	public function checkCustomer($orderData){ 

    	$displayName = $orderData->customer->fname.' '.$orderData->customer->lname;
    	$company_name = $orderData->customer->company_name;
    	$email = $orderData->customer->email;

    	$entities = $this->dataService->Query("select * from Customer Where PrimaryEmailAddr='".$email."'");
    	
		$error = $this->dataService->getLastError();
		if ($error) {			   
		    echo "The Response message is: " . $error->getResponseBody() . "<br/>";			   
		    return false;			    
		}
		// Echo some formatted output
		if (count($entities) > 0) {		    
		    foreach ($entities as $val) {
		    	$qb_id = $val->Id;	
		    	DB::update("update users set qb_id='".$qb_id."' where id=".$orderData->customer->id);	   
		    }		    
		    return true;
		}	
		else
		{
			return false;
		}			
	}

	public function getQuery()
	{
		$dataService = DataService::Configure(array(
			'auth_mode' => 'oauth2',
			'ClientID' => $this->clientID,
			'ClientSecret' => $this->clientSecret,
			'accessTokenKey' =>session()->get('qb.accessToken'),
			'refreshTokenKey' => session()->get('qb.refreshToken'),
			'QBORealmID' => session()->get('qb.realmId'),
			'baseUrl' => $this->baseUrl
		));

		//$entities = $dataService->Query("SELECT * FROM TaxRate");
		//$entities = $dataService->Query("SELECT * FROM TaxCode");
		//$entities = $dataService->Query("select * from Item");
		//$entities = $dataService->Query("select * from Account");
		//$entities = $dataService->Query("select * from Term");
		//$entities = $dataService->Query("select * from Customer");
		$entities = $dataService->Query("select * from Customer Where PrimaryEmailAddr='royduptain@gmail.com'");
		//$entities = $dataService->Query("select * from Customer Where DisplayName='Andy Choquette - andy@strategic1dentity.com'");
		//$entities = $dataService->Query("select * from SalesReceipt where id='176'");
		//$entities = $dataService->Query("select * from Invoice where id='162703'");

		$error = $dataService->getLastError();
		if ($error) {			   
		    echo "The Response message is: " . $error->getResponseBody() . "<br/>";			   
		    return "";			    
		}

		// Echo some formatted output
		if (count($entities) > 0) {
		    $i = 0;
		    foreach ($entities as $oneTaxRate) {
		    	pr($oneTaxRate);
		    	//echo $oneTaxRate->Name.'='.$oneTaxRate->Id.'<br/>';
		        //echo "TaxRate[$oneTaxRate->Id] Name: {$oneTaxRate->Name}	Rate {$oneTaxRate->RateValue} AgencyRef {$oneTaxRate->AgencyRef} (SpecialTaxType {$oneTaxRate->SpecialTaxType})<br/>";
		        $i++;
		    }
		}
		
		die();
	}
    
}
