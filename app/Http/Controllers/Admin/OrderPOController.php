<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ups;
use DB;
use PDF;
use Form;
use App\User;
use App\UserAddress;
use App\Designers;
use App\Vendors;
use App\Notes;
use App\Events;
use App\Messages;
use App\Category;
use App\Products;
use App\CustomOptions;
use App\ProductOptions;
use App\Orders;
use App\OrderProducts;
use App\OrderProductOptions;
use App\OrderAddress;
use App\Discounts;
use App\ProductVariant;
use App\Coupons;
use App\State;
use App\OrderPo;
use App\OrderPoDetails;
use App\OrderPoOptions;
use App\OrderPOAddress;
use App\ProductShipping;
use \App\Helpers\UPSShipping;
use \App\Helpers\CalculateShippingWeight;

use Exception;
use Carbon\Carbon;
use \App\Helpers\FunctionsHelper;

/**
 * Class OrderPOController
 * @package App\Http\Controllers\Admin
 */
class OrderPOController extends Controller
{
    /**
     * OrderPOController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth',['except' => ['save_po','create_pdf']]);
    }

    /**
     * @param $id
     * @param $product_id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
	public function create_po($id,$product_id)
    {
		$details = OrderProducts::select('order_products.id','order_products.order_id','order_products.vendor_id','order_products.qty','order_products.product_name as custom_product_name','order_products.project_name as product_project','order_products.comments as product_comment','order_products.description','order_products.tflow_job_id','orders.multiple_shipping','product.name as product_name')->where('order_id',$id)->whereNull('po_id')->with('orderProductOptions','orderProductAddress')->leftJoin('orders','orders.id','=','order_products.order_id')->leftJoin('products as product','product.id','=','order_products.product_id')->get();
		
		$order_details = Orders::where('id',$id)->first();
		
		$ids = array();
		$product_array = array();		
				
		foreach($details as $detail){
			
			$ids[$detail->vendor_id][] = $detail->id;
			$product_array[$detail->id] = $detail;
			
			$po_address = new OrderPOAddress();
			$po_address->order_id = $detail->order_id;
			$po_address->order_product_id = $detail->id;
			$po_address->shipping_company_name = $detail->orderProductAddress->shipping_company_name;
			$po_address->shipping_phone_number = $detail->orderProductAddress->shipping_phone_number;
			$po_address->shipping_fname = $detail->orderProductAddress->shipping_fname;
			$po_address->shipping_lname = $detail->orderProductAddress->shipping_lname;
			$po_address->shipping_add1 = $detail->orderProductAddress->shipping_add1;
			$po_address->shipping_add2 = $detail->orderProductAddress->shipping_add2;
			$po_address->shipping_ship_in_care = $detail->orderProductAddress->shipping_ship_in_care;
			$po_address->shipping_city = $detail->orderProductAddress->shipping_city;
			$po_address->shipping_zipcode = $detail->orderProductAddress->shipping_zipcode;
			$po_address->shipping_state = $detail->orderProductAddress->shipping_state;
			$po_address->shipping_country = $detail->orderProductAddress->shipping_country;
			$po_address->status = 1;			
			$po_address->save();			

			foreach($detail->orderProductOptions as $option){
				if($option->custom_option_field_group == 'production'){
					continue;
				}

				$po_options = new OrderPoOptions();
				$po_options->order_id = $detail->order_id;
				$po_options->vendor_id = $detail->vendor_id;
				$po_options->order_product_id = $detail->id;
				$po_options->option_type = $option->type;
				$po_options->option_field_group = $option->custom_option_field_group;
				$po_options->option_name = $option->custom_option_name;
				$po_options->option_value = $option->value;
				$po_options->status = 1;
				$po_options->save();				
			}
		}	
				
		$i= 0;
		$vendor_po_ids= array();
		$o_products = OrderProducts::select('id','po_id','vendor_id','product_id')->where('order_id',$id)->where('po_id','!=','')->get();
		
		if(count($o_products)>=1)
		{
			foreach($o_products as $product)
			{
				$vendor_po_ids[$product->vendor_id] = $product->po_id;
				$p_id_arr = explode('-', $product->po_id);
				if($p_id_arr[2] > $i )
				{
					$i = $p_id_arr[2] + 1;
				}
			}
		}
		else
		{
			$i= 1;
		}		
		
		//pr(array_keys($ids));die;
		
		foreach($ids as $key=>$order_product_id){
					
			$po_id = 'PO-'.$id.'-'.$i;
			if(isset($vendor_po_ids[$key]))
			{
				$po_id = $vendor_po_ids[$key];
			}
			else
			{
				$vendor_details = Vendors::where('id',$key)->first();

				$order_po = new OrderPo();
				$order_po->po_id = $po_id;
				$order_po->order_id = $id;
				$order_po->agent_id = $order_details->agent_id;
				$order_po->vendor_id = $key;
				$order_po->terms = $vendor_details->terms;
				$order_po->new_terms = $vendor_details->new_terms;
				$order_po->subtotal = 0;
				$order_po->shipping = 0;
				$order_po->total = 0;
				$order_po->status = null;
				$order_po->save();
			}

			foreach ($order_product_id as $key => $value) {
				$order_po_detail = new OrderPoDetails();
				$order_po_detail->order_id = $id;
				$order_po_detail->po_id = $po_id;
				$order_po_detail->order_product_id = $value;
				$order_po_detail->product_name = $product_array[$value]['product_name'];
				if($product_array[$value]['custom_product_name'] !="")
				{
					$order_po_detail->product_name = $product_array[$value]['custom_product_name'];	
				}				

				$order_po_detail->project_name = $product_array[$value]['product_project'];
				$order_po_detail->comments = $product_array[$value]['product_comment'];
				$order_po_detail->description = $product_array[$value]['description'];
				$order_po_detail->qty = $product_array[$value]['qty'];
				$order_po_detail->amount = 0;
				$order_po_detail->due_date = $product_array[$value]['due_date'];;
				$order_po_detail->tflow_job_id = $product_array[$value]['tflow_job_id'];;
				$order_po_detail->shipping_option = $order_details->shipping_option;
				$order_po_detail->status =1;
				$order_po_detail->save();
			}

			$product = OrderProducts::where('order_id',$id)->whereIn('id',$order_product_id)->update(['po_id'=>$po_id]);

			$i++;
		}

		Orders::where('id',$id)->update(['po_status'=>2]);
		
		$po_detail = OrderProducts::where('order_id',$id)->where('product_id',$product_id)->first();
		
		$poVendorEmails = OrderPo::where('po_id',$po_detail->po_id)->leftJoin('vendors as vendor','order_po.vendor_id','=','vendor.id')->leftJoin('users as agent','agent.id','=','order_po.agent_id')->select('order_po.*','vendor.email as email',DB::raw("concat(vendor.fname, ' ', vendor.lname) as vendor_name"),DB::raw("concat(agent.fname, ' ', agent.lname) as agent_name"),'agent.email as agent_email','agent.direct as agent_direct')->with('PoDetails')->get();
		
		foreach($poVendorEmails as $val){
			if(!empty($val->agent_name))
				$agent_name = $val->agent_name;
			else
				$agent_name = config('constants.ADMIN_NAME');
			
			if(!empty($val->agent_email))
				$agent_email = $val->agent_email;
			else
				$agent_email = config('constants.ADMIN_MAIL');
			
			$detail = '<table border="1" cellpadding="1" cellspacing="1"><thead><tr><th scope="col">Order Id</th><th scope="col">Product name</th><th scope="col">Qantity</th><th scope="col">Order Options</th></tr></thead><tbody>';
							
				foreach($val->PoDetails as $val1){
					$detail .= '<tr>';
					$detail .= '<td>'.$val->order_id.'</td>';
					$detail .= '<td>'.$val1->product_name.'</td>';
					$detail .= '<td>'.$val1->qty.'</td>';
					$detail .= '<td>';
					
					$order_options['printing'] = '';
					$order_options['finishing'] = '';
					$order_options['production'] = '';
					foreach($val1->PoOption as $option){					
						$order_options[$option->option_field_group] .= '<li>'.$option->option_name.' => '.$option->option_value.'</li>';
					}
					
					if(!empty($order_options['printing'])){
						$detail .= '<b>Printing</b><ol>'.$order_options['printing'].'</ol>';
					}
					if(!empty($order_options['finishing'])){
						$detail .= '<b>Finishing</b><ol>'.$order_options['finishing'].'</ol>';
					}
					if(!empty($order_options['production'])){
						$detail .= '<b>Design Services Options</b><ol>'.$order_options['production'].'</ol>';
					}

					$detail .= '</td>';
					$detail .= '</tr>';
				}
				
				$detail .= '</tbody>';
				$detail .= '</table>';
			
			$params = array('slug'=>'po_to_vendor',
							'to'=>$val->email,
							'bcc'=>config('constants.store_email'),
							'params'=>array(
										'{{name}}'=>$val['vendor_name'],
										'{{detail}}'=>$detail,
										'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
										'{{AGENT_NAME}}'=>$agent_name,
										'{{AGENT_MAIL}}'=>$agent_email,
										'{{AGENT_DIRECT}}'=>$val->agent_direct,
										));
			//parent::sendMail($params);
		}
		
		return redirect('/admin/order/po/'.$po_detail->po_id);
	}
	
	public function po($id){
		$pageTitle = "Purchase Order Details";
		
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$vendors = Vendors::select('id', DB::raw("concat(fname, ' ', lname) as name"),'company_name','company_address')->where('status', 1)->get()->keyBy('id');
		$temp = array();
		foreach($vendors as $vendor){
			if(!empty($vendor->company_name)){
				$temp[$vendor->id] = $vendor->company_name;
			}else{
				$temp[$vendor->id] = $vendor->name;
			}
		}
		$vendorList = $temp;
		
		$order_products = OrderProducts::where('po_id',$id)->with('product','orderPOAddress')->get();		
		$order_id = 0;
		$vendor_id = 0;
		foreach($order_products as $product){
			$order_id = $product->order_id;
			$vendor_id = $product->vendor_id;
		}

		$po_products = OrderPoDetails::where('order_po_details.po_id',$id)->with('PoProduct')->leftJoin('order_products','order_products.id','=','order_po_details.order_product_id')->select('order_po_details.*','order_products.art_work_status')->get();

		$orderPO = OrderPO::where('po_id',$id)->first();

		//pr($vendors->keyBy('id')->toArray());die;
		$vendor = DB::table('vendors')->select('vendors.*')->where('id', $orderPO->vendor_id)->first();
		
		$db = Orders::where('id', $order_id)->with(['customer','agent','orderProduct','orderPOAddress']);
		$order = $db->first();

		// vendor id
        if (!empty($vendor_id)) {
            $orderProduct = OrderProducts::where('order_id', $order_id)->where('vendor_id', $vendor_id)->get();
            $ids = array_column($orderProduct->toArray(), 'id');
            $db = OrderPOAddress::whereIn('order_product_id', $ids);
            $order->orderPOAddress = $db->first();
        }

		if(count($order) > 0){
			$order_po = OrderPo::where('po_id',$id)->with('po_details')->first();
			
			return view('Admin/orders/PO/po',compact('pageTitle','order','agents','type','vendor_id','vendor','vendors','vendorList','order_products','id','order_po','po_products','orderPO'));
		}else{
			return redirect('/admin/order/lists');
		}
	}
	
	public function address_edit(Request $request) {
		$responce['status'] = '';
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;

			$orderData = Orders::where('id',$data['order_id'])->first();
			$userAddress = UserAddress::where('user_id',$orderData->user_id)->get();

			$userAddressArr = array();
			foreach($userAddress as $val)
			{
				if($val->address_name !="")
					$userAddressArr[$val->id] = $val->address_name;
				else
					$userAddressArr[$val->id] = $val->add1;
			}					
			
			$states = State::where('status',1)->pluck('stateName','stateCode')->all();
			
			$responce['html'] = Form::model('edit_add',['id'=>'edit_add']).'<div class="col-xs-12">';
			$responce['html'] .= Form::hidden('order_id',$data['order_id'],['id'=>'order_id']);
			$responce['html'] .= Form::hidden('order_multiple',$data['order_multiple'],['id'=>'order_multiple']);
			$responce['html'] .= Form::hidden('vendor_id',$data['vendor_id'],['id'=>'vendor_id']);

			$db = OrderPoAddress::where('order_id',$data['order_id']);

			// if there is a specific address being requested,
            // get it, otherwise get it by vendor
			if (!empty($data['order_po_address_id'])) {
			    $db->where('id', $data['order_po_address_id']);
            } elseif (!empty($data['vendor_id'])) {
                $orderProduct = OrderProducts::where('order_id', $data['order_id'])->where('vendor_id', $data['vendor_id'])->get();
                $ids = array_column($orderProduct->toArray(), 'id');
                $db->whereIn('order_product_id', $ids);
            }

			if($data['order_multiple'] != '0'){
				$db->where('order_product_id',$data['order_product_id']);
				$responce['html'] .= Form::hidden('order_product_id',$data['order_product_id'],['id'=>'order_product_id']);
			}
			
			$detail = $db->first();
			//pr($detail);die;
			if(isset($data['address_id']) && $data['address_id']>=1)
			{
				$detail = UserAddress::where('id',$data['address_id'])->first();
				$detail->shipping_company_name = $detail->company_name;
				$detail->shipping_phone_number = $detail->phone_number;
				$detail->shipping_fname = $detail->fname;
				$detail->shipping_lname = $detail->lname;
				$detail->shipping_add1 = $detail->add1;
				$detail->shipping_add2 = $detail->add2;
				$detail->shipping_ship_in_care = $detail->ship_in_care;
				$detail->shipping_zipcode = $detail->zipcode;
				$detail->shipping_city = $detail->city;
				$detail->shipping_state = $detail->state;
				$detail->shipping_country = $detail->country;				
			}

			if(count($userAddressArr) >= 1)
			{
				$responce['html'] .='<div class="col-md-12 form-group">'.Form::label('saved_address', 'Select Address',array('class'=>'form-control-label')).Form::select('saved_address', $userAddressArr,@$data['address_id'],array('class'=>'form-control add_option','id'=>'saved_address','placeholder'=>'Select from saved Address','order-id'=>$data['order_id'],'order-multiple'=>$data['order_multiple'],'order-product-id'=>$data['order_product_id'])).'</div>';				
			}
			$responce['html'] .='<div class="col-md-6 form-group">'.Form::label('shipping_company_name', 'Shipping Company Name',array('class'=>'form-control-label')).Form::text('shipping_company_name', $detail->shipping_company_name,array('class'=>'form-control add_option','id'=>'shipping_company_name','placeholder'=>'Enter Shipping Company Name')).'</div>';
			$responce['html'] .='<div class="col-md-6 form-group">'.Form::label('shipping_phone_number', 'Shipping Phone Number',array('class'=>'form-control-label')).Form::text('shipping_phone_number', $detail->shipping_phone_number,array('class'=>'form-control add_option','id'=>'shipping_phone_number','placeholder'=>'Enter Shipping Phone Number')).'</div>';

			$responce['html'] .='<div class="col-md-6 form-group">';
			$responce['html'] .= Form::label('shipping_fname', 'First Name',array('class'=>'form-control-label'));
			$responce['html'] .= Form::text('shipping_fname', $detail->shipping_fname,array('class'=>'form-control validate_add','id'=>'shipping_fname','placeholder'=>'Shipping First Name'));
			$responce['html'] .= '</div><div class="col-md-6 form-group">';
			$responce['html'] .= Form::label('shipping_lname', 'Last Name',array('class'=>'form-control-label'));
			$responce['html'] .= Form::text('shipping_lname', $detail->shipping_lname,array('class'=>'form-control validate_add','id'=>'shipping_lname','placeholder'=>'Shipping Last Name'));
			$responce['html'] .= '</div><div class="clearfix"></div><div class="col-md-6 form-group">';
			$responce['html'] .= Form::label('shipping_add1', 'Shipping Add 1',array('class'=>'form-control-label'));
			$responce['html'] .= Form::text('shipping_add1', $detail->shipping_add1,array('class'=>'form-control validate_add','id'=>'shipping_add1','placeholder'=>'Enter Shipping Add'));
			$responce['html'] .= '</div><div class="col-md-6 form-group">';
			$responce['html'] .= Form::label('shipping_add2', 'Shipping Add 2',array('class'=>'form-control-label'));
			$responce['html'] .= Form::text('shipping_add2', $detail->shipping_add2,array('class'=>'form-control add2','id'=>'shipping_add2','placeholder'=>'Enter Shipping Add'));
			$responce['html'] .= '</div><div class="clearfix"></div>';
			
			$responce['html'] .='<div class="col-md-12 form-group">'.Form::label('shipping_ship_in_care', 'Ship in care of',array('class'=>'form-control-label')).Form::text('shipping_ship_in_care', $detail->shipping_ship_in_care,array('class'=>'form-control add_option','id'=>'shipping_ship_in_care','placeholder'=>'Enter Shipping Ship in care of')).'</div>';

			$responce['html'] .='<div class="col-md-6 form-group">';
			$responce['html'] .= Form::label('shipping_zipcode', 'Shipping Zipcode',array('class'=>'form-control-label'));
			$responce['html'] .= Form::number('shipping_zipcode', $detail->shipping_zipcode,array('class'=>'form-control validate_add','id'=>'shipping_zipcode','placeholder'=>'Enter Shipping Zipcode'));
			$responce['html'] .= '</div><div class="col-md-6 form-group">';
			$responce['html'] .= Form::label('shipping_city', 'Shipping City',array('class'=>'form-control-label'));
			$responce['html'] .= Form::text('shipping_city', $detail->shipping_city,array('class'=>'form-control validate_add','id'=>'shipping_city','placeholder'=>'Enter Shipping City'));
			$responce['html'] .= '</div><div class="clearfix"></div><div class="col-md-6 form-group">';
			$responce['html'] .= Form::label('shipping_state', 'Shipping State',array('class'=>'form-control-label'));
			$responce['html'] .= Form::select('shipping_state',$states, $detail->shipping_state,array('class'=>'form-control validate_add','id'=>'shipping_state','placeholder'=>'Select State'));
			$responce['html'] .= '</div><div class="col-md-6 form-group">';
			$responce['html'] .= Form::label('shipping_country', 'Shipping Country',array('class'=>'form-control-label'));
			$responce['html'] .= Form::text('shipping_country', $detail->shipping_country,array('class'=>'form-control validate_add','id'=>'shipping_country','readonly'));
			$responce['html'] .= '</div></div><div class="clearfix"></div><div class="col-xs-12"><div class="col-md-6 form-group"><button type="button" class="btn btn-success edit_address">Update</button><img id="edit_add_loader_img" class="loader_img" src="'.url('public/img/loader/Spinner.gif').'"></div></div>';
			$responce['html'] .= Form::close();
			
			$responce['status'] = 'success';
		}
		return json_encode($responce);
	}
	
	public function address_save(Request $request){
		$res['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;

            $db = OrderPOAddress::where('order_id',$data['order_id']);

            if (!empty($data['vendor_id'])) {
                $orderProduct = OrderProducts::where('order_id', $data['order_id'])->where('vendor_id', $data['vendor_id'])->get();
                $ids = array_column($orderProduct->toArray(), 'id');
                $db->whereIn('order_product_id', $ids);
            }

			if($data['order_multiple'] != '0'){
				$db->where('order_product_id',$data['order_product_id']);
				$res['order_product_id'] = $data['order_product_id'];
			}
			$status = $db->update(['shipping_company_name'=>$data['shipping_company_name'],'shipping_phone_number'=>$data['shipping_phone_number'],'shipping_fname'=>$data['shipping_fname'],'shipping_lname'=>$data['shipping_lname'],'shipping_add1'=>$data['shipping_add1'],'shipping_add2'=>$data['shipping_add2'],'shipping_ship_in_care'=>$data['shipping_ship_in_care'],'shipping_city'=>$data['shipping_city'],'shipping_zipcode'=>$data['shipping_zipcode'],'shipping_state'=>$data['shipping_state'],'shipping_country'=>$data['shipping_country']]);
			if($status){
				$res['status'] = 'success';
				$res['order_multiple'] = $data['order_multiple'];
				$res['str']="";
				if($data['shipping_company_name'] != ''){
					$res['str'] .= '<strong>'.$data['shipping_company_name'].'</strong><br/>';
				}	
				if($data['shipping_phone_number'] != ''){
					$res['str'] .= $data['shipping_phone_number'].'<br/>';
				}
				if($data['shipping_fname'] != '' and $data['shipping_lname'] != ''){
					$res['str'] .= '<strong>'.$data['shipping_fname'].' '.$data['shipping_lname'].'</strong><br/>';
				}

				$res['str'] .= $data['shipping_add1'].','.$data['shipping_add2'].'<br/>Zipcode : '.$data['shipping_zipcode'].'<br/>';
				if($data['shipping_ship_in_care'] != ''){
					$res['str'] .= '<strong>Care of: </strong>' . $data['shipping_ship_in_care'].'<br/>';
				}

				$res['str'] .='City : '.$data['shipping_city'].'<br/>State : '.$data['shipping_state'].'<br/>Country : '.$data['shipping_country'];
			}else{
				$res['msg'] = 'Address not updated successfully, please try again.';
			}
		}
		return json_encode($res);
	}
	
	public function option_edit(Request $request){
		$res['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$po_options = OrderPoOptions::where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_id'])->with('orderProduct')->get();
			//pr($po_options);die;
			$width = '';
			$width_option_id = '';
			$height = '';
			$height_option_id = '';
			$variant_str = '';
			$html = array('production'=>'','printing'=>'','finishing'=>'','extra'=>'');
			foreach($po_options as $option){
				if($option->option_type == 3){
					if($option->option_name == 'Width'){
						$width_option_id = $option->id;
						$width = $option->option_value;
					}else if($option->option_name == 'Height'){
						$height_option_id = $option->id;
						$height = $option->option_value;
					}
				}else if($option->option_type == 1){
					$custom_option = CustomOptions::where('name',$option->option_name)->first();
					//pr($custom_option);
					if(count($custom_option) > 0){
						if($custom_option->option_type == 1){
							$values = json_decode($custom_option->option_keys,true);	
							$html[$custom_option->field_group] .=  '<div class="col-sm-6 col-xs-12 form-group" id="option_div_'.$option->id.'">';
							
							$html[$custom_option->field_group] .= '<i class="fa fa-trash text-danger delete_option pull-right" data-product-id="'.trim($data['order_product_id']).'" data-id="'.$option->id.'"></i><label class="form-control-label" for="options['.$custom_option->field_group.']['.$custom_option->id.']['.str_replace(' ','_',strtolower($custom_option->name)).']">'.$custom_option->label.'</label>';
							
							$html[$custom_option->field_group] .= '<select class="form-control option_fields option_custom" name="options['.$custom_option->field_group.']'.'['.$custom_option->name.']" id="option_custom'.$custom_option->id.'" data-name="'.$custom_option->name.'" data-id="'.$custom_option->id.'" required><option value="" rel="0" selected="selected">Select</option>';
							
							foreach($values as $value){
								$price = 0;
								$weight = 0;
								$selected_value = '';
								if($option->option_value === $value['value']){
									$selected_value = 'selected="selected"';
								}
								if(array_key_exists('price',$value) and !empty($value['price'])){ $price = $value['price']; }
								if(array_key_exists('weight',$value) and !empty($value['weight'])){ $weight = $value['weight']; }
								$html[$custom_option->field_group] .= '<option '.$selected_value.' value=\''.htmlentities($value['value']).'\' rel="'.$price.'" data-price="'.$price.'" data-weight="'.$weight.'" data-price-type="'.$custom_option->price_formate.'">'.htmlentities($value['value']).'</option>';
							}
							$html[$custom_option->field_group] .= '</select></div>';
						}
						if($custom_option->option_type == 2){
							$values = json_decode($custom_option->option_keys,true);
							$html[$custom_option->field_group] .= '<div class="col-sm-6 col-xs-12 form-group" id="option_div_'.$option->id.'">';
							
							$html[$custom_option->field_group] .= '<i class="fa fa-trash text-danger delete_option pull-right" data-product-id="'.trim($data['order_product_id']).'" data-id="'.$option->id.'"></i><label class="form-control-label" for="options['.$custom_option->field_group.']['.$custom_option->name.']">'.$custom_option->name.'</label>';
							
							foreach($values as $value){
								$price = 0;
								if(array_key_exists('price',$value) and !empty($value['price'])){ $price = $value['price']; }
								
								$html[$custom_option->field_group] .= Form::text('options['.$custom_option->field_group.']'.'['.$custom_option->name.']',$option->option_value,['class'=>'form-control option_custom','placeholder'=>$custom_option->name,'required']);
							}
							$html[$custom_option->field_group] .=  '</div>'; 
						}
					}else{
						$html['extra'] .= '<div class="col-sm-6 col-xs-12 form-group" id="option_div_'.$option->id.'">';
						
						$html['extra'] .= '<i class="fa fa-trash text-danger delete_option pull-right" data-product-id="'.trim($data['order_product_id']).'" data-id="'.$option->id.'"></i><label class="form-control-label" for="options[extra]['.$option->option_name.']">'.$option->option_name.'</label>';
						
						$html['extra'] .= Form::text('options[extra]['.$option->option_name.']',$option->option_value,['class'=>'form-control option_custom','placeholder'=>$option->option_name,'required']);
						$html['extra'] .= '</div>';
					}
				}else if($option->option_type == 2){
					$variant_option = ProductVariant::where('product_id',$option->orderProduct->product_id)->where('name',$option->option_name)->with('variantValues')->first();
					//pr($variant_option);
					$variant_options = array();
					foreach($variant_option->variantValues as $value){
						if($option->option_value == $value->value){
							$selected_id = $value->value;
						}
						$variant_options[$value->value] = $value->value;
					}
					//pr($variant_options);
					$variant_str .= '<div class="col-xs-12 col-sm-6 form-group" id="option_div_'.$option->id.'"><i class="fa fa-trash text-danger delete_option pull-right" data-product-id="'.trim($data['order_product_id']).'" data-id="'.$option->id.'"></i><label class="form-control-label" for="variants['.$variant_option->name.']">'.$variant_option->name.'</label>'. Form::select('variants['.$variant_option->name.']',$variant_options,$selected_id,['class'=>'form-control option_custom option_fields variants_option','id'=>'']).'</div>';
				}else if($option->option_type == 4){
					$html['extra'] .= '<div class="col-sm-6 col-xs-12 form-group" id="option_div_'.$option->id.'">';
						
					$html['extra'] .= '<i class="fa fa-trash text-danger delete_option pull-right" data-product-id="'.trim($data['order_product_id']).'" data-id="'.$option->id.'"></i><label class="form-control-label" for="options[extra]['.$option->option_name.']">'.$option->option_name.'</label>';
					
					$html['extra'] .= Form::text('options[extra]['.$option->option_name.']',$option->option_value,['class'=>'form-control option_custom','placeholder'=>$option->option_name,'required']);
					$html['extra'] .= '</div>';
				}
			}
			//pr($html);die;
			$str = Form::model('edit_po_option',['id'=>'edit_po_option']).'<div class="col-md-12 form-group order_po_option_box">';
			
			$str .= Form::hidden('order_id',$data['order_id'],['class'=>'form-control']);
			$str .= Form::hidden('order_product_id',$data['order_product_id'],['class'=>'form-control']);
			$str .= Form::hidden('vendor_id',$data['vendor'],['class'=>'form-control']);
			$str .= Form::hidden('custom_line_item',$data['custom_line_item'],['class'=>'form-control']);
			
			$str .= '<div class="form-group col-xs-12"><h3 class="page-header">Product Details</h3><div class="col-sm-12 col-xs-12 form-group"><label class="form-control-label" for="product_name">Product Name</label>'. Form::text('product_name',$data['order_product_name'],['class'=>'form-control']).'</div>';
			
			if($data['custom_line_item'] == 1)
			{
				$po_details = OrderPoDetails::where('order_product_id',$data['order_product_id'])->first();
				$description = str_replace('<br />',"",$po_details->description) ;

				$str .='<div class="col-sm-12 col-xs-12 form-group"><label class="form-control-label" for="product_name">Product Description</label>'. Form::textarea('description',$description,['class'=>'form-control','rows'=>5]).'</div>';
			}
			
			$str .='</div>';
			
			if($html['printing'] != '' or $variant_str != '' or ($width != '' and $height != '')){		
				$str .= '<div class="form-group col-xs-12"><h3 class="page-header">Printing Options</h3>';
				if($width != '' and $height != ''){
					$str .= '<div class="col-sm-6 col-xs-12 form-group" id="option_div_'.$width_option_id.'"><i class="fa fa-trash text-danger delete_option pull-right" data-product-id="'.trim($data['order_product_id']).'" data-id="'.$width_option_id.'"></i><label class="form-control-label" for="options[printing][Width]">Width(ft)</label>'. Form::number('options[printing][Width]',$width,['class'=>'form-control option_fields option_custom','id'=>'width','min'=>1]).'</div><div class="col-sm-6 col-xs-12 form-group" id="option_div_'.$height_option_id.'"><i class="fa fa-trash text-danger delete_option pull-right" data-product-id="'.trim($data['order_product_id']).'" data-id="'.$height_option_id.'"></i><label class="form-control-label" for="options[printing][Height]">Height(ft)</label>'. Form::number('options[printing][Height]',$height,['class'=>'form-control option_fields option_custom','id'=>'height','min'=>1]).'</div>';
				}
				$str .= $variant_str;
				$str .= $html['printing'].'</div>';
			}
			
			if($html['finishing'] != ''){
				$str .= '<div class="form-group col-xs-12"><h3 class="page-header">Finishing Options</h3>'.$html['finishing'].'</div>';
			}
			
			if($html['extra'] != ''){
				$str .= '<div class="form-group col-xs-12"><h3 class="page-header">Extra Options</h3>'.$html['extra'].'</div>';
			}
			
			$str .= '<div class="form-group col-xs-12 hide extra_option_div"><h3 class="page-header">New Options</h3></div></div><div class="clearfix"></div><div class="col-xs-12"><div class="col-md-6 form-group"><button type="button" class="btn btn-success edit_option">Update</button><img id="edit_option_loader_img" class="loader_img" src="'.url('public/img/loader/Spinner.gif').'"></div>';

			if($data['custom_line_item'] == 0)
			{
				$str .='<div class="col-md-6 form-group"><button type="button" class="btn btn-primary add_option pull-right">Add Option</button></div>';
			}

			$str .='</div>';
			$str .= Form::close();
			
			$res['status'] = 'success';
			$res['html'] = $str;
		}
		return json_encode($res);
	}
	
	public function option_save(Request $request){
		$res['status'] = '';
		$res['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;

			if($data['custom_line_item'] == 1)
			{
				DB::table('order_po_details')->where('id',$data['order_product_id'])->update(['product_name'=>$data['product_name'],'description'=>nl2br($data['description'])]);

				$res['product_name'] = $data['product_name'];
				$res['html'] .= nl2br($data['description']);				
				$res['product_id'] = $data['order_product_id'];
				$res['status'] = 'success';
				return json_encode($res);		
			}
			
			$po_options = DB::table('order_po_details')->where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_id'])->update(['product_name'=>$data['product_name']]);
			
			$res['product_name'] = $data['product_name'];
			
			// Below code for update po variants //
			
			if(array_key_exists('variants',$data)){
				foreach($data['variants'] as $key=>$val){
					OrderPoOptions::where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_id'])->where('option_name',$key)->update(['option_value'=>$val]);
				}
			}
			
			// Below code for update po options //
			
			if(array_key_exists('options',$data)){
				foreach($data['options'] as $key=>$val){
					$temp = array();
					if($key == 'extra'){
						$temp['option_type'] = 4;
						$temp['option_field_group'] = 'extra';
					}
					
					foreach($val as $key1=>$val1){
						$temp['option_value'] = $val1;
						OrderPoOptions::where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_id'])->where('option_name',$key1)->update($temp);
					}
				}
			}
			
			// Below code for save new po options //
			
			if(array_key_exists('new_option',$data)){
				foreach($data['new_option'] as $key=>$val){
					$obj = new OrderPoOptions();
					$obj->order_id = $data['order_id'];
					$obj->vendor_id = $data['vendor_id'];
					$obj->order_product_id = $data['order_product_id'];
					$obj->option_type = 4;
					$obj->option_field_group = 'extra';
					$obj->option_name = $val['name'];
					$obj->option_value = $val['value'];
					$obj->status = 1;
					$obj->save();
				}
			}
			
			$po_options = DB::table('order_po_options')->where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_id'])->get();
			
			foreach($po_options as $options){
				if($options->option_name == 'Width' or $options->option_name == 'Height'){
					$res['html'] .= $options->option_name.'(ft):'.$options->option_value.'<br/>';
				}else{	
					$res['html'] .= $options->option_name.':'.$options->option_value.'<br/>';
				}
			}
			$res['product_id'] = $data['order_product_id'];
			$res['status'] = 'success';
		}
		return json_encode($res);
	}

	public function product_delete(Request $request){		
		$res['status'] = '';
		$res['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();	
			//pr($data);die();		

			DB::table('order_po_options')->where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_id'])->delete();
			DB::table('order_po_address')->where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_id'])->delete();
			DB::table('order_po_details')->where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_id'])->delete();
			DB::table('order_products')->where('order_id',$data['order_id'])->where('id',$data['order_product_id'])->update(['po_id'=>NULL]);			

			if($data['custom_line_item'] == 1)
			{
				DB::table('order_po_details')->where('order_id',$data['order_id'])->where('id',$data['order_product_id'])->delete();		
			}
			
			$subtotal = 0;
			$po_details = DB::table('order_po_details')->where('order_id',$data['order_id'])->get();
			foreach($po_details as $val)
			{
				$subtotal = $subtotal + $val->amount;
			}
			$order_po = DB::table('order_po')->where('order_id',$data['order_id'])->first();
			$total = $subtotal + $order_po->shipping;
			DB::table('order_po')->where('order_id',$data['order_id'])->update(['subtotal'=>$subtotal,'total'=>$total]);
			
			$res['subtotal'] = $subtotal;			
			$res['total'] = $total;			
			$res['status'] = 'success';
		}
		return json_encode($res);
	}
	
	public function po_mail(Request $request,$id){
		$order_products = OrderProducts::where('po_id',$id)->with('orderPOAddress','product')->get();
		$order_po = OrderPo::where('po_id',$id)->with('po_details','agent')->first();
		//pr($order_po);die;
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
		}
		
		if(!empty($order_po) and count($order_po) > 0){
			$agent = DB::table('users')->select('users.*')->where('id',$order_po->agent_id)->where('role_id',2)->where('status', 1)->first();
			
			$vendor = Vendors::where('id',$order_po->vendor_id)->first();
			
			$db = Orders::where('id', $order_po->order_id)->with(['customer','orderProduct','agent']);
			$order = $db->first();
			
			$pdf = $this->genrate_pdf($id);
			
			$po_products = OrderPoDetails::where('order_po_details.po_id',$id)->with('PoProduct')->leftJoin('order_products','order_products.id','=','order_po_details.order_product_id')->select('order_po_details.*','order_products.art_work_status')->get();
			//pr($po_products);die;
			
			$mail = DB::table('emails')->where('slug','po_mail')->first();
			$subject = $mail->subject;
			
			$temp = $mail->message;
			
			$subject = str_replace('{{id}}',$id,$subject);
			
			$temp = str_replace('{{name}}',$vendor->fname.' '.$vendor->lname,$temp);
			
			$colspan=6;

            $address = '';

            if($order->orderPOAddress->shipping_company_name !="")
                $address .= $order->orderPOAddress->shipping_company_name.'<br/>';

            if($order->orderPOAddress->shipping_fname != '')
                $address .= $order->orderPOAddress->shipping_fname.' '.$order->orderPOAddress->shipping_lname.'<br/>';

            if($order->orderPOAddress->shipping_ship_in_care != '' and $order->orderPOAddress->shipping_ship_in_care != '')
                $address .= '<strong>Care of: </strong>'.$order->orderPOAddress->shipping_ship_in_care.'<br/>';

            if ($order->orderPOAddress->shipping_add1 != '' and $order->orderPOAddress->shipping_add2 != '') {
                $address .= $order->orderPOAddress->shipping_add1.'<br>'.$order->orderPOAddress->shipping_add2.'<br>';
            } elseif($order->orderPOAddress->shipping_add1 != '') {
                $address .= $order->orderPOAddress->shipping_add1.'<br>';
            }

            $address .= $order->orderPOAddress->shipping_city.', '.$order->orderPOAddress->shipping_state.' '.$order->orderPOAddress->shipping_zipcode.' '.$order->orderPOAddress->shipping_country;

            if($order->orderPOAddress->shipping_phone_number != '')
                $address .= '<br/>' . $order->orderPOAddress->shipping_phone_number;

			$str = '<table class="table table-condensed" width="100%" style="border-collapse: collapse !important;border:1px solid #CCC">
								<thead>
									<tr>
									    <td colspan="6">
									        <strong>Ship To:</strong></br>
						                    <div class="col-xs-12">
						                        <address class="col-xs-10">'.$address.'</address>
						                    </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6"></td>
                                    </tr>
									<tr>
										<th class="nowrap">Description</th>
										<th class="nowrap">Qty:</th>
										<th class="nowrap">Rate:</th>
										<th class="nowrap">Amount:</th>
										<th class="nowrap">Due Date:</th>
										<th class="nowrap">Shipping Via:</th>';
										
										if($order->multiple_shipping != 0)
										{
											$colspan=7;
											$str .= '<th class="nowrap">Ship To:</th></tr></thead><tbody>';
										}
		$i = 1;
		foreach($po_products as $val){
			$qty = number_format($val->qty,2);
			$rate = priceFormat($val->rate);
			$amount = priceFormat($val->amount);
			$shipping_option = $order->shipping_option;
			if(isset($val->PoProduct)){
				$due_date = date('M-d-Y', strtotime(date('M-d-Y',strtotime($val->PoProduct->created_at)).' +3 days'));
				
				if(!empty($val->due_date))
					$due_date = $val->due_date;
				if(!empty($val->shipping_option))
					$shipping_option = $val->shipping_option;
			}
			
			if(strlen($shipping_option) < 2){
				$shipping_option = '0'.$shipping_option;
			}
		
			$str .= '<tr style="border-top:1px solid #CCC"><td class="nowrap" style="width:40%">';
			if(isset($val->PoProduct)){
				$str .= '<div class="col-xs-12 no-padding">
						<div class="col-xs-11">	
							<strong>'.$val->product_name.'.</strong>
							<div class="col-xs-12">
								'.$val->description .'
							</div>
							<div class="col-xs-12">';
								$order_vendor_options = DB::table('order_po_options')->where('order_id',$order->id)->where('order_product_id',$val->PoProduct->id)->get();
								//pr($order_vendor_options);
							foreach($order_vendor_options as $options){
								if($options->option_name == 'Width' or $options->option_name == 'Height')
									$str .= $options->option_name.'(ft): '.$options->option_value.'<br/>';
								else
									$str .= $options->option_name.': '.$options->option_value.'<br/>';
							}
							
							$str .= '<br/></div>
						</div>
					</div>';
			}else{
				$str .= '<div class="col-xs-12 no-padding">
					<strong>'.$val->product_name.'.</strong>
					<div class="col-xs-12">
						'.$val->description .'
					</div>
				</div>';
			}			
			
			$str .= '
				<td class="nowrap" style="width:5%;text-align:center;">
					<div class="col-xs-12 no-padding">'.$qty.'</div>
				</td>
				<td class="nowrap" style="width:5%;text-align:center;">
					<div class="form-group col-xs-12 no-padding">$'.$rate.'</div>
				</td>
				<td class="nowrap" style="width:5%;text-align:center;">
					<div>$'.$amount.'</div>
				</td>';
			
			if(isset($val->PoProduct)){
				$str .= '<td class="nowrap" style="width:10%;text-align:center;">
						<div class="form-group col-xs-12 no-padding">'.$due_date.'
						</div>
					</td>
					<td class="nowrap" style="width:15%">'.config('constants.Shipping_option.'.$shipping_option).'</td>';
					
					if($order->multiple_shipping != 0){
						$str .= '<td class="nowrap" style="width:30%">
						<div class="col-xs-12"><address class="col-xs-10">';
						
						if($val->PoProduct->orderPOAddress->shipping_company_name !="")
							$str .= $val->PoProduct->orderPOAddress->shipping_company_name.'<br/>';
							
						if($val->PoProduct->orderPOAddress->shipping_fname != '' and $val->PoProduct->orderPOAddress->shipping_lname != '')
							$str .= $val->PoProduct->orderPOAddress->shipping_fname.' '.$val->PoProduct->orderPOAddress->shipping_lname.'<br/>';

                        if($val->PoProduct->orderPOAddress->shipping_ship_in_care != '')
                            $str .= '<strong>Care of: </strong>'.$val->PoProduct->orderPOAddress->shipping_ship_in_care.'<br/>';

						$str .= $val->PoProduct->orderPOAddress->shipping_add1.'<br>'.$val->PoProduct->orderPOAddress->shipping_add2.'<br>';
						
						$str .= $val->PoProduct->orderPOAddress->shipping_city.', '.$val->PoProduct->orderPOAddress->shipping_state.' '.$val->PoProduct->orderPOAddress->shipping_zipcode.' '.$val->PoProduct->orderPOAddress->shipping_country.'<br>';

                        if($val->PoProduct->orderPOAddress->shipping_phone_number != '')
                            $str .= $val->PoProduct->orderPOAddress->shipping_phone_number.'<br/>';

						$str .= '</address>	</div></td>';
					}
			}else{
				$str .= '<td class="nowrap" colspan="3" style="width:10%"></td>';
			}
			$str .= '</tr>';

			$str .= '<tr><td colspan="'.$colspan.'">';

				$str .= '<strong>Project Name:</strong>';
				
				if(!empty($val->project_name))
					$str .= $val->project_name;
				else
					$str .= 'None';
				
				$str .= '<br/><strong>Comment:</strong>';							
				if(!empty($val->comments))
					$str .= $val->comments;
				else
					$str .= 'None';
											
				if($val->tflow_job_id != '' && $val->art_work_status == 6){
					$str .= '<br/><strong>ArtWork File:</strong>';
					$str .= '<a class="pointer" target="_blank" href="http://108.61.143.179:9016/application/job/'.$val->tflow_job_id.'/download/preflighted?hash=GdDF7OAwo2xvxqbNKge6z5SXxYB81hHrhojPoD5KkPvZC33z77MR7KvOVqkCw4ZT">View ArtWork File</a>';
				}

			$str .= '<br/><br/></td></tr>';
							
			$i++;
		}

		if(!empty($order_po->notes)){
			$str .= '<tr style="border-top:1px solid #CCC">
						<td colspan="'.($colspan-2).'"> 
						<strong>Order Notes : </strong>'.$order_po->notes .'						
			</td></tr>';
		}

		
		$str .= '<tr style="border-top:1px solid #CCC">
					<td colspan="'.($colspan-2).'"></td>
					<th class="nowrap">Sub Total : </th>
					<td class="nowrap">&nbsp;$'.priceFormat($order_po->subtotal).'</td>
				</tr>
				<tr style="border-top:1px solid #CCC">
					<td colspan="'.($colspan-2).'"></td>
					<th class="nowrap">Shipping : </th>
					<td class="nowrap">
						<span style="float:left;">&nbsp;$'.priceFormat($order_po->shipping).'</span>
					</td>
				</tr>
				<tr style="border-top:1px solid #CCC">
					<td colspan="'.($colspan-2).'"></td>
					<th class="nowrap">Total : </th>
					<td class="nowrap">&nbsp;$'.priceFormat($order_po->subtotal+$order_po->shipping).'</td>
				</tr>
			</tbody>
		</table>';
							
			/*********** Below Code For Set Global Hook Variable ***********/
			$agent_name = (count($order->agent_id) > 0)?$order->agent->fname.' '.$order->agent->lname:'';
			$agent_direct = (count($order->agent_id) > 0)?$order->agent->direct:'';
			
			$temp = str_replace('{{detail}}',$str,$temp);
			$temp = str_ireplace('{{agent_name}}',$agent_name,$temp);
			$temp = str_ireplace('{{AGENT_DIRECT}}',$agent_direct,$temp);
			$temp = str_ireplace('{{ADMIN_MAIL}}',config('constants.ADMIN_MAIL'),$temp);
			
			$temp = str_ireplace('{{SITE_URL}}',config('constants.SITE_URL'),$temp);
			$temp = str_ireplace('{{SITE_LOGIN_URL}}',config('constants.SITE_URL').'/login',$temp);
			$temp = str_ireplace('{{SITE_NAME}}',config('constants.SITE_NAME'),$temp);
			$temp = str_ireplace('{{ADMIN_NAME}}',config('constants.ADMIN_NAME'),$temp);
			$temp = str_ireplace('{{ADMIN_MAIL}}',config('constants.ADMIN_MAIL'),$temp);
			
			$mailHtml = $temp;
			/*********** End Code For Set Global Hook Variable ***********/
			
			return view('Admin/orders/PO/po_mail',compact('data','vendor','agent','order','order_po','pdf','id','order_products','po_products','mailHtml','subject'));
		}else{
			return redirect('/admin/order/lists');
		}
	}
	
	public function genrate_pdf($id,$type=null){		
		$res = array();
		$url = url("admin/order/po/create_pdf/".$id);
		//echo "hello";die;
		$file_name = 'order_po_'.$id.'.pdf';
		$file_path = "public/pdf/PO/".$file_name;	
		
		$exe = config('constants.phantomjs_path');
		$output = exec("$exe --ssl-protocol=any --ignore-ssl-errors=yes pages.js  $url $file_path 2>&1");
		
		if($output){
			$res = array('file_name'=>$file_name);
		}else{
			$res = array('file_name'=>'');
		}

		if($type != null){
			header('Content-Type: application/pdf');
			header("Content-Transfer-Encoding: Binary");
			header("Content-disposition: attachment; filename=" . $file_name);
			readfile($file_path);
			exit;
		}else{
			return $res;
		}
	}

    /**
     * @param Request $request
     * @return string
     */
	public function save_po(Request $request)
    {
		$responce['status'] = false;
		
		if($request->isMethod('post')) {
			$data = $request->all();
			
			$order_po_detail = OrderPo::where('po_id',$data['po_id'])->first();

			$po_total = 0;
			foreach ($data['amount'] as $amount) {
			    $po_total += FunctionsHelper::reverseNumberFormat($data['amount']);
            }

			// Below Code for check new line items are available or not and save it. //
			if(array_key_exists('new_item',$data)){
				foreach($data['new_item'] as $item){
					$order_po_detail = new OrderPoDetails();
					$order_po_detail->order_id = $data['order_id'];
					$order_po_detail->po_id = $data['po_id'];
					$order_po_detail->product_name = $item['name'];
					$order_po_detail->qty = $item['qty'];
					$order_po_detail->rate = FunctionsHelper::reverseNumberFormat($item['rate']);
					$order_po_detail->amount = FunctionsHelper::reverseNumberFormat($item['amount']);
					$order_po_detail->description = $item['description'];
					$order_po_detail->status = 1;
					$order_po_detail->save();

					$po_total += number_format(FunctionsHelper::reverseNumberFormat($item['amount']),2,'.','');
				}
			}

			// End Code for check new line items are available or not and save it. //

			$po_total += number_format(FunctionsHelper::reverseNumberFormat($data['shipping_amount']),2,'.','');

			if($data['representative'] >= 1 )
			{
				Orders::where('id', $data['order_id'])
                    ->update([
                        'agent_id'=>$data['representative']
                    ]);
			}

			OrderPo::where('po_id', $data['po_id'])
                ->update([
                    'vendor_id' => $data['vendor'],
                    'terms' => $data['terms'],
                    'new_terms' => $data['new_terms'],
                    'notes' => $data['order_notes'],
                    'subtotal' => FunctionsHelper::reverseNumberFormat($data['po_sub_total']),
                    'shipping' => FunctionsHelper::reverseNumberFormat($data['shipping_amount']),
                    'total' => $po_total
                ]);

            OrderProducts::where('order_id', $data['order_id'])
                ->where('po_id', $data['po_id'])
                ->update(['vendor_id' => $data['vendor']]);

			foreach($data['qty'] as $key => $val) {
				$order_po_details = OrderPoDetails::where('order_id', $data['order_id'])
                    ->where('po_id', $data['po_id'])
                    ->where('id', $key)
                    ->first();

				$order_po_details->qty = $val;
				$order_po_details->rate = FunctionsHelper::reverseNumberFormat($data['rate'][$key]);
				$order_po_details->amount = FunctionsHelper::reverseNumberFormat($data['amount'][$key]);
				if(array_key_exists($key,$data['due_date']))
					$order_po_details->due_date = $data['due_date'][$key];
				if(array_key_exists($key,$data['shipping_option']))
					$order_po_details->shipping_option = $data['shipping_option'][$key];
				$order_po_details->status = 1;
				$order_po_details->save();
				//pr($order_po_details);
			}//die;

			$responce['status'] = 'success';
		}
		return json_encode($responce);
	}
	
	public function create_pdf(Request $request,$id){
		$order_products = OrderProducts::where('po_id',$id)->with('product','orderPOAddress')->get();
		$order_po = OrderPo::where('po_id',$id)->with('po_details','vendor','agent')->first();
		$db = Orders::where('id', $order_po->order_id)->with(['customer','orderProduct','orderPOAddress','agent']);
		$order = $db->first();
		
		$po_products = OrderPoDetails::where('order_po_details.po_id',$id)->with('PoProduct')->leftJoin('order_products','order_products.id','=','order_po_details.order_product_id')->select('order_po_details.*','order_products.art_work_status')->get();
		return view('Admin/orders/PO/pdf',compact('order_po','order','order_products','po_products'));
	}
	
	public function print_pdf(Request $request,$id){
		$order_products = OrderProducts::where('po_id',$id)->with('orderProductAddress','orderPOAddress')->get();
		$order_po = OrderPo::where('po_id',$id)->with('po_details','vendor','agent')->first();
		$db = Orders::where('id', $order_po->order_id)->with(['customer','orderProduct','orderPOAddress','agent']);
		$order = $db->first();
		
		$po_products = OrderPoDetails::where('order_po_details.po_id',$id)->with('PoProduct')->leftJoin('order_products','order_products.id','=','order_po_details.order_product_id')->select('order_po_details.*','order_products.art_work_status')->get();
		
		return view('Admin/orders/PO/print',compact('order_po','order','order_products','po_products'));
	}
	
	public function send_po(Request $request){
		$responce['status'] = false;
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			$order = Orders::where('id', $data['order_id'])->with(['customer','agent','orderProduct','orderProductOptions','orderAddress'])->first();
			
			$message_body = $data['message'];
			
			/*********** Below Code For Set Global Hook Variable ***********/
			
			if(count($order->agent) > 0){
				$message_body = str_ireplace('{{AGENT_NAME}}',$order->agent->fname.' '.$order->agent->lname,$message_body);
				$message_body = str_ireplace('{{AGENT_MAIL}}',$order->agent->email,$message_body);
				$message_body = str_ireplace('{{AGENT_DIRECT}}',$order->agent->direct,$message_body);
			}
			
			$message_body = str_ireplace('{{SITE_URL}}',config('constants.SITE_URL'),$message_body);
			$message_body = str_ireplace('{{SITE_LOGIN_URL}}',config('constants.SITE_URL').'/login',$message_body);
			$message_body = str_ireplace('{{SITE_NAME}}',config('constants.SITE_NAME'),$message_body);
			$message_body = str_ireplace('{{ADMIN_NAME}}',config('constants.ADMIN_NAME'),$message_body);
			$message_body = str_ireplace('{{ADMIN_MAIL}}',config('constants.ADMIN_MAIL'),$message_body);
			
			/*********** End Code For Set Global Hook Variable ***********/
			
			\Mail::send([], [], function ($message) use ($data,$message_body) {
				$message->from($data['from'],config('constants.SITE_NAME'));
				$message->to(explode(',',$data['to']));
				if($data['cc'] !='')
				{
					$message->cc(explode(',',$data['cc']));
				}	
				$message->bcc(config('constants.store_email'));
				$message->subject($data['subject']);
				$message->attach("public/pdf/PO/".$data['file_name'], [
						'as' => str_replace(" ", "_", $data['subject']),
						'mime' => 'application/pdf',
					]);
				$message->setBody($message_body, 'text/html');
			});
			$responce['status'] = true;
			\Session::flash('success', "Email sent successfully.");
		}
		return json_encode($responce);
	}
	
	public function delete_option(Request $request){
		$res['status'] = false;
		$res['html'] = '';
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			OrderPoOptions::where('id',$data['option_id'])->delete();
			
			$po_options = DB::table('order_po_options')->where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_id'])->get();
			
			foreach($po_options as $options){
				if($options->option_name == 'Width' or $options->option_name == 'Height'){
					$res['html'] .= $options->option_name.'(ft):'.$options->option_value.'<br/>';
				}else{	
					$res['html'] .= $options->option_name.':'.$options->option_value.'<br/>';
				}
			}
			$res['product_id'] = $data['order_product_id'];
			$res['status'] = 'success';
		}
		return json_encode($res);
	}
	
	public function change_vendor(Request $request){
		$responce['status'] = false;
		$responce['detail'] = '';
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$res = OrderProducts::where('order_id', $data['order_id'])->where('po_id', $data['po_id'])->update(['vendor_id'=>$data['vendor_id']]);
			
			$res1 = OrderPo::where('order_id', $data['order_id'])->where('po_id', $data['po_id'])->update(['vendor_id'=>$data['vendor_id']]);
			
			$products_list = OrderProducts::where('po_id',$data['po_id'])->pluck('id')->all();
			
			$res2 = OrderPoOptions::where('order_id', $data['order_id'])->whereIn('order_product_id', $products_list)->update(['vendor_id'=>$data['vendor_id']]);
			
			$vendor = Vendors::findOrFail($data['vendor_id']);
			
			$str = '<strong>Company : </strong>'.$vendor->company_name.'<br/><strong>Name : </strong>'.$vendor->fname.' '.$vendor->lname.'<br/><strong>Address : </strong>'.$vendor->company_address;
			
			$responce['status'] = 'success';
			$responce['detail'] = $str;
		}
		return json_encode($responce);
	}
}
