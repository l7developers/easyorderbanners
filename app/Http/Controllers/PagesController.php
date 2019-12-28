<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use App\StaticPage;
use App\Category;
use App\Products;
use App\ProductOptions;
use App\Reviews;
use App\Discounts;
use App\Testimonials;

use \App\Helpers\PaymentAuthorize;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except' => ['index','pages','email','contact','slugview']]);
    }
	
    public function pages($slug=null,Request $request)
    {
		$page=StaticPage::where('slug',$slug)->get()->toArray();
		$pageTitle = $page[0]['title'];
		
		return view('pages/index',compact('page'));
    }

	public function slugview($slug=null,Request $request){
		$page=StaticPage::where('slug',$slug)->get()->toArray();
		if(count($page)>=1)
		{
			if(!empty($page[0]['meta_title']))
				$pageTitle = $page[0]['meta_title'];
			else
				$pageTitle = $page[0]['title'];
			
			if($page[0]['testimonials'] != ''){
				$testimonial = Testimonials::where('id',$page[0]['testimonials'])->first();
			}else{
				$testimonial = Testimonials::inRandomOrder()->first();
			}
			return view('pages/index',compact('page','pageTitle','testimonial'));
		}
		
		$category = Category::where('slug',$slug)->with('products','child')->where('status',1)->first();
		if(count($category)>=1)
		{
			if(!empty($category->meta_title))
				$pageTitle = $category->meta_title;
			else
				$pageTitle = $category->name;
			return view('category/detail',compact('pageTitle','category'));
		}
		
		$product=Products::where('slug',$slug)->with('shipping','Catgory','Images','custom','variants','variantCombinantion','product_prices')->first();
		if(count($product)>=1)
		{
			$reviewsTotal = Reviews::where('product_id',$product->id)->where('status',1)->with('user')->orderBy('created_at','DESC')->get();
			
			$reviews = Reviews::where('product_id',$product->id)->where('status',1)->with('user')->limit(4)->orderBy('created_at','DESC')->get();
		
			$related_products = Products::where('id','!=',$product->id)->where('category_id',$product->category_id)->where('status',1)->with('product_prices')->get();
			$options = ProductOptions::where('product_id',$product->id)->with('CustomOption')->get();
			$options_array = array();
			$i = 1;
			foreach($options as $val){
				$values = json_decode($val->CustomOption->option_keys,true);
				$options_array[$val->CustomOption->field_group][$i]['id'] = $val->CustomOption->id;
				$options_array[$val->CustomOption->field_group][$i]['name'] = $val->CustomOption->name;
				$options_array[$val->CustomOption->field_group][$i]['price_formate'] = $val->CustomOption->price_formate;
				$options_array[$val->CustomOption->field_group][$i]['type'] = $val->CustomOption->option_type;
				$options_array[$val->CustomOption->field_group][$i]['description'] = $val->CustomOption->description;
				$options_array[$val->CustomOption->field_group][$i]['free'] = $val->CustomOption->free;
				$options_array[$val->CustomOption->field_group][$i]['values'] = $values;
				$i++;
			}
			if(!empty($product->meta_title))
				$pageTitle = $product->meta_title;
			else
				$pageTitle = $product->name;
			
			// Make a array of old combinations //
			
			$sets = array();
			if(count($product->variantCombinantion) > 0){
				$i = 0;
				foreach($product->variantCombinantion as $val){
					$key = $val->varient_id1;
					if($val->varient_id2 != ''){
						$key .= '-'.$val->varient_id2;
					}
					if($product->price_sqft_area == 1){
						$sets[$key]['variable_price'] = 1;
						$sets[$key]['price'][$i]['price'] = $val['price'];  
						$sets[$key]['price'][$i]['min_area'] = $val['min_area'];  
						$sets[$key]['price'][$i]['max_area'] = $val['max_area'];  
					}else{
						$sets[$key]['variable_price'] = 0;
						$sets[$key]['price'] = $val['price'];
					}
					$i++;
				}
			}

			$variable_price = array();
			if($product->price_sqft_area == 1){				
				$i = 0;
				foreach($product->product_prices as $val){										
					$variable_price[$i]['price'] = $val['price'];  
					$variable_price[$i]['min_area'] = $val['min_area'];  
					$variable_price[$i]['max_area'] = $val['max_area'];
					$i++;
				}
			}
			
			$discount = array();
			$discount_all = Discounts::where('status',1)->orderBy('quantity','ASC')->get();
			foreach($discount_all as $val){
				if(empty($val->products)){
					$discount[$val->quantity] = $val->percent;
				}else{
					$ids = explode(',',$val->products);
					if(in_array($product->id,$ids)){
						$discount[$val->quantity] = $val->percent;
					}
				}
			}
			//pr($discount);die;
			$total = 0;
			$count = 0;
			$avg = 0;
			foreach($reviewsTotal as $review){
				$total += $review->rating;
				$count++;
			}
			if($count > 0)
			$avg = round($total/$count,1);
			
			$cartkey="";
			if(isset($_GET['cartkey']) && $_GET['cartkey'] !="")
			{
				$cartkey = $_GET['cartkey'];
			}
			return view('product/detail',compact('pageTitle','product','options_array','related_products','reviews','total','count','avg','slug','sets','variable_price','discount','cartkey'));
		}
    }
	
	public function review_get(Request $request){
		$data = $request->all();
		//pr($data);die;
		$db = Reviews::where('product_id',$data['product_id'])->where('status',1)->with('user');
		
		$db->orderBy('created_at','DESC');
		
		$reviews = $db->paginate(4);
		
		if(count($reviews) > 0){
			$str = '';
			foreach($reviews as $review){
				$str .= '<span class="comment_star" data="'.$review->rating.'"></span>
						<p>'.ucfirst($review->comment).'<br/>Posted on : '.date('d-M',strtotime($review->created_at));
				if(isset($review->user))
						$str .= ' - By : '.ucwords($review->user->fname.' '.$review->user->lname);
					
				$str .= '</p><hr/>';
			}
			return json_encode(array('status'=>'success','str'=>$str,'data'=>1));
		}else{
			return json_encode(array('status'=>'success','data'=>0));
		}
	}

	public function authorizeCheck(){
		$payment = new PaymentAuthorize();
		
		$billing_add = array();
		$billing_add['fname'] = "Jitendra";
		$billing_add['lname'] = "Dariwal";
		$billing_add['address'] = "882/29 Mayo Link Road";
		$billing_add['company'] = "Test Company";
		$billing_add['phone_number'] = "9549590287";
		$billing_add['city'] = "Ajmer";
		$billing_add['state'] = "Rajasthan";
		$billing_add['country'] = "India";
		$billing_add['zipcode'] = "305001";
		
		$shipping_add = array();
		$shipping_add['fname'] = "Jitendra";
		$shipping_add['lname'] = "Dariwal";
		$shipping_add['address'] = "882/29 Mayo Link Road";
		$shipping_add['company'] = "Test Company";
		$shipping_add['city'] = "Ajmer";
		$shipping_add['state'] = "Rajasthan";
		$shipping_add['country'] = "India";
		$shipping_add['zipcode'] = "305001";
		
		$result = $payment->payment("5Ayx72AM","6aQJ573LwVw5g966",2,"4111111111111111","513","2038-12",$billing_add,$shipping_add);
		if($result['status'] == 'success'){
			echo "payment Succuess";
			pr($result);
		}else{
			echo "payment Not Succuess";
			pr($result);
		}
		die;
	}
}
