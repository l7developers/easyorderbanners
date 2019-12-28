<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\User;
use App\Orders;
use App\Events;
use App\Messages;
use App\State;
use Hash;
use Carbon\Carbon;
use Excel;
use App\Exports\CustomersExports;
use App\Exports\OrdersExports;
use App\Exports\OrdersQbExports;
use App\Exports\SalesExports;
use App\Exports\POExports;

class ReportsController extends Controller
{
    public function Orders(Request $request){
		$pageTitle = "Orders Report";
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			if(isset($data['from_date']) and !empty($data['from_date'])){
				session(['reports.orders.from_date' => $data['from_date']]);
			}else{
				session()->forget('reports.orders.from_date');
			}
			
			if(isset($data['end_date']) and !empty($data['end_date'])){
				session(['reports.orders.end_date' => $data['end_date']]);
			}else{
				session()->forget('reports.orders.end_date');
			}
		}else{
			session()->forget('reports.orders');
		}
		
		
		$db = DB::table('orders');
		if (session()->has('reports.orders')) {
			if (session()->has('reports.orders.from_date') and !session()->has('reports.orders.end_date')){
				$from_date = session()->get('reports.orders.from_date');
				$db->where('created_at','>=',date('Y-m-d'.' 00:00:00',strtotime($from_date)));
				
				$start = $from_date;
				$end = date('Y-m-d');
			}
			else if (!session()->has('reports.orders.from_date') and session()->has('reports.orders.end_date')){
				$end_date = session()->get('reports.orders.end_date');
				$db->where('created_at','<=',date('Y-m-d'.' 23:59:59',strtotime($end_date)));
				
				$start = date("Y-m-d", strtotime( date( 'Y-m-01' )." -5 months"));
				$end = $end_date;
			}
			else if(session()->has('reports.orders.from_date') and session()->has('reports.orders.end_date')){
				$from_date = session()->get('reports.orders.from_date');
				$end_date = session()->get('reports.orders.end_date');
				$from_date = date('Y-m-d'.' 00:00:00',strtotime($from_date));
				$end_date = date('Y-m-d'.' 23:59:59',strtotime($end_date));
				$db->whereBetween('created_at',array($from_date,$end_date));
				
				$start = session()->get('reports.orders.from_date');
				$end = session()->get('reports.orders.end_date');
			}
			
			$order_graph = $this->date_range($start,$end,'+1 day','d-M');
			
		}else{
			$start_date = date('Y-m-d', strtotime('-7 days'));
			$db->where("created_at",">", $start_date);
			
			$order_graph = array();
			$order_graph[date('d-M')] = 0;
			
			for ($i = 1; $i < 7; $i++) {
				$order_graph[date('d-M', strtotime("-$i day"))] = 0;
			}
			$order_graph = array_reverse($order_graph);
		}

		$db->where('orders.status','>=',1);
		$db->where('orders.customer_status','>=',1);
		
		$orders = $db->get();
		
		foreach($orders as $order){
			$month = date('d-M',strtotime($order->created_at));
			if(array_key_exists($month,$order_graph)){
				$order_graph[$month] = $order_graph[$month]+1;
			}
		}
		
		return view('Admin/reports/orders',compact('pageTitle','order_graph','orders'));
	}
	
	public function OrderExport(){
		
		// Below excel is old formated //
		
		//return Excel::download(new OrdersExports(), 'orders.csv');
		
		$start = date('F-d-Y');
		$end = date('F-d-Y');
		if (session()->has('reports.orders')) {
			if (session()->has('reports.orders.from_date') and !session()->has('reports.orders.end_date')){
				$start = date('F-d-Y',strtotime(session()->get('reports.orders.from_date')));
				$end = date('F-d-Y');
			}
			else if (!session()->has('reports.orders.from_date') and session()->has('reports.orders.end_date')){
				$start = date('F-d-Y', strtotime( date( 'Y-m-01' )." -5 months"));
				$end = date('F-d-Y',strtotime(session()->get('reports.orders.end_date')));
			}
			else if(session()->has('reports.orders.from_date') and session()->has('reports.orders.end_date')){
				$from_date = session()->get('reports.orders.from_date');
				$end_date = session()->get('reports.orders.end_date');
				$start = date('F-d-Y',strtotime($from_date));
				$end = date('F-d-Y',strtotime($end_date));
			}
		}else{
			$start = date('F-d-Y', strtotime('-7 days'));
			$end = date('F-d-Y');
		}
		
		$file_name = "orders_".$start."-".$end;
		
		return Excel::download(new OrdersQbExports(), $file_name.'.csv');
	}
	
	public function Customers(Request $request){
		$pageTitle = "Customers Report";
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			if(isset($data['from_date']) and !empty($data['from_date'])){
				session(['reports.customer.from_date' => $data['from_date']]);
			}else{
				session()->forget('reports.customer.from_date');
			}
			
			if(isset($data['end_date']) and !empty($data['end_date'])){
				session(['reports.customer.end_date' => $data['end_date']]);
			}else{
				session()->forget('reports.customer.end_date');
			}
		}else{
			session()->forget('reports.customer');
		}
		
		
		$db = DB::table('users')->where('role_id',3);
		if (session()->has('reports.customer')) {
			if (session()->has('reports.customer.from_date') and !session()->has('reports.customer.end_date')){
				$from_date = session()->get('reports.customer.from_date');
				$end_date  = null;
				$db->where('created_at','>=',date('Y-m-d'.' 00:00:00',strtotime($from_date)));
				
				$start = $from_date;
				$end = date('Y-m-d');
			}
			else if (!session()->has('reports.customer.from_date') and session()->has('reports.customer.end_date')){
				$from_date = null;
				$end_date = session()->get('reports.customer.end_date');
				$db->where('created_at','<=',date('Y-m-d'.' 23:59:59',strtotime($end_date)));
				
				$start = date("Y-m-d", strtotime( date( 'Y-m-01' )." -5 months"));
				$end = $end_date;
			}
			else if(session()->has('reports.customer.from_date') and session()->has('reports.customer.end_date')){
				$from_date = session()->get('reports.customer.from_date');
				$end_date = session()->get('reports.customer.end_date');
				$from_date = date('Y-m-d'.' 00:00:00',strtotime($from_date));
				$end_date = date('Y-m-d'.' 23:59:59',strtotime($end_date));
				$db->whereBetween('created_at',array($from_date,$end_date));
				
				$start = session()->get('reports.customer.from_date');
				$end = session()->get('reports.customer.end_date');
			}
			
			$customer_graph = $this->date_range($start,$end,'+1 day','d-M');
			
		}else{
			$start_date = date('Y-m-d', strtotime('-7 days'));
			$db->where("created_at",">", $start_date);
			
			$customer_graph = array();
			$customer_graph[date('d-M')] = 0;
			
			for ($i = 1; $i < 7; $i++) {
				$customer_graph[date('d-M', strtotime("-$i day"))] = 0;
			}
			$customer_graph = array_reverse($customer_graph);
		}
		
		$users = $db->get();
		
		foreach($users as $user){
			$month = date('d-M',strtotime($user->created_at));
			if(array_key_exists($month,$customer_graph)){
				$customer_graph[$month] = $customer_graph[$month]+1;
			}
		}
		//pr($customer_graph);die;
		
		return view('Admin/reports/customers',compact('pageTitle','customer_graph','users'));
	}
	
	public function CustomerExport(){
		return Excel::download(new CustomersExports(), 'customers.csv');
	}
	
	public function Sales(Request $request){
		
		$pageTitle = "Sales Report";
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			if(isset($data['from_date']) and !empty($data['from_date'])){
				session(['reports.sales.from_date' => $data['from_date']]);
			}else{
				session()->forget('reports.sales.from_date');
			}
			
			if(isset($data['end_date']) and !empty($data['end_date'])){
				session(['reports.sales.end_date' => $data['end_date']]);
			}else{
				session()->forget('reports.sales.end_date');
			}
		}else{
			session()->forget('reports.sales');
		}
		
		
		$db = DB::table('orders');
		if (session()->has('reports.sales')) {
			if (session()->has('reports.sales.from_date') and !session()->has('reports.sales.end_date')){
				$from_date = session()->get('reports.sales.from_date');
				$db->where('created_at','>=',date('Y-m-d'.' 00:00:00',strtotime($from_date)));
				
				$start = $from_date;
				$end = date('Y-m-d');
			}
			else if (!session()->has('reports.sales.from_date') and session()->has('reports.sales.end_date')){
				$end_date = session()->get('reports.sales.end_date');
				$db->where('created_at','<=',date('Y-m-d'.' 23:59:59',strtotime($end_date)));
				
				$start = date("Y-m-d", strtotime( date( 'Y-m-01' )." -5 months"));
				$end = $end_date;
			}
			else if(session()->has('reports.sales.from_date') and session()->has('reports.sales.end_date')){
				$from_date = session()->get('reports.sales.from_date');
				$end_date = session()->get('reports.sales.end_date');
				$from_date = date('Y-m-d'.' 00:00:00',strtotime($from_date));
				$end_date = date('Y-m-d'.' 23:59:59',strtotime($end_date));
				$db->whereBetween('created_at',array($from_date,$end_date));
				
				$start = session()->get('reports.sales.from_date');
				$end = session()->get('reports.sales.end_date');
			}
			
			$sales_graph = $this->date_range($start,$end,'+1 day','d-M');
		}else{
			$start_date = date('Y-m-d', strtotime('-7 days'));
			$db->where("created_at",">", $start_date);
			
			$sales_graph = array();
			$sales_graph[date('d-M')] = 0;
			
			for ($i = 1; $i < 7; $i++) {
				$sales_graph[date('d-M', strtotime("-$i day"))] = 0;
			}
			$sales_graph = array_reverse($sales_graph);
		}

		$db->where('orders.status','=',1);
		$db->where('orders.customer_status','>=',1);
		
		$orders = $db->get();
		
		foreach($orders as $order){
			$month = date('d-M',strtotime($order->created_at));
			if(array_key_exists($month,$sales_graph)){
				$sales_graph[$month] = $sales_graph[$month]+$order->total;
			}
		}
		//pr($sales_graph);
		//die;
		
		return view('Admin/reports/sales',compact('pageTitle','sales_graph','orders'));
	}
	
	public function SalesExport(){
		return Excel::download(new SalesExports(), 'sales.csv');
	}
	
	public function poExports(Request $request){
		$pageTitle = "PO-Exports";
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			if(isset($data['from_date']) and !empty($data['from_date'])){
				session(['reports.poExports.from_date' => $data['from_date']]);
			}else{
				session()->forget('reports.poExports.from_date');
			}
			
			if(isset($data['end_date']) and !empty($data['end_date'])){
				session(['reports.poExports.end_date' => $data['end_date']]);
			}else{
				session()->forget('reports.poExports.end_date');
			}
			return Excel::download(new POExports(), 'PO.csv');
		}else{
			session()->forget('reports.poExports');
		}
		return view('Admin/reports/po_exports');
	}
	
	public function date_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y' ) {
		$dates = array();
		$current = strtotime($first);
		$last = strtotime($last);

		while( $current <= $last ) {

			$dates[date($output_format, $current)] = 0;
			$current = strtotime($step, $current);
		}
		return $dates;
	}
	
}
