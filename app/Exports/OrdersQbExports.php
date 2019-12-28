<?php
namespace App\Exports;

/* use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping; */

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use DB;
use App\Orders;
use App\OrderProducts;

class OrdersQbExports implements FromView{
	public $i = 1;
	
	public function __construct(){
       
    }
	
	public function view(): View{
		$db = OrderProducts::select('order_products.*',"product.name as productName",DB::raw("concat(user.fname, ' ', user.lname) as customer_name"),'user.email as customer_email','user.phone_number as customer_phone_number','user.company_name as customer_company_name',DB::raw("concat(agent.fname, ' ', agent.lname) as agent_name"),DB::raw("concat(designer.fname, ' ', designer.lname) as designer_name"),DB::raw("concat(vendor.fname, ' ', vendor.lname) as vendor_name"),'address.shipping_fname','address.shipping_lname','address.shipping_add1','address.shipping_add2','address.shipping_city','address.shipping_state','address.shipping_zipcode')->with('order','orderProductOptions');
		
		$db->leftJoin('orders as order', 'order_products.order_id', '=', 'order.id');
		$db->leftJoin('products as product', 'order_products.product_id', '=', 'product.id');
		$db->leftJoin('order_address as address', function($join){
			$join->on('order_products.order_id', '=', 'address.order_id');
			$join->on('order_products.id', '=', 'address.order_product_id');
		});
		$db->leftJoin('users as user', 'order.user_id', '=', 'user.id');
		$db->leftJoin('users as agent', 'order.agent_id', '=', 'agent.id');
		$db->leftJoin('designers as designer', 'order_products.designer_id', '=', 'designer.id');
		$db->leftJoin('vendors as vendor', 'order_products.vendor_id', '=', 'vendor.id');
		
		if (session()->has('reports.orders')) {
			if (session()->has('reports.orders.from_date') and !session()->has('reports.orders.end_date')){
				$from_date = session()->get('reports.orders.from_date');
				$db->where('order_products.created_at','>=',date('Y-m-d'.' 00:00:00',strtotime($from_date)));
				
				$start = $from_date;
				$end = date('Y-m-d');
			}
			else if (!session()->has('reports.orders.from_date') and session()->has('reports.orders.end_date')){
				$end_date = session()->get('reports.orders.end_date');
				$db->where('order_products.created_at','<=',date('Y-m-d'.' 23:59:59',strtotime($end_date)));
				
				$start = date("Y-m-d", strtotime( date( 'Y-m-01' )." -5 months"));
				$end = $end_date;
			}
			else if(session()->has('reports.orders.from_date') and session()->has('reports.orders.end_date')){
				$from_date = session()->get('reports.orders.from_date');
				$end_date = session()->get('reports.orders.end_date');
				$from_date = date('Y-m-d'.' 00:00:00',strtotime($from_date));
				$end_date = date('Y-m-d'.' 23:59:59',strtotime($end_date));
				
				$db->whereBetween('order_products.created_at',array($from_date,$end_date));
			}
		}else{
			$start_date = date('Y-m-d', strtotime('-7 days'));
			$db->where("order_products.created_at",">", $start_date);
		}

		$db->where('order.status','>=',1);
		$db->where('order.customer_status','>=',1);
		
		$db->orderBy('order_products.created_at','desc');
		$orders = $db->get();
		//pr($orders->toArray());die;
		return view('Admin.Exports.order_qb', [
            'orders' => $orders
        ]);
    }
}
?>