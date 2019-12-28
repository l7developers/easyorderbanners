<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use DB;
use App\Orders;
use App\OrderProducts;

class SalesExports implements FromView{
	public $i = 1;
	
	public function __construct(){
       
    }
	
	public function view(): View{
		$number_of_days = 0;
		$db = Orders::with('orderProduct');
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
				$start = $from_date;
				$end = $end_date;
			}
			$start_date = $start;
			$end_date = $end;
			
			$start = strtotime('2010-01-25');
			$end = strtotime('2010-02-20');

			$number_of_days = ceil(abs($end - $start) / 86400);
		}else{
			$number_of_days = 7;
			$start_date = date('Y-m-d', strtotime('-7 days'));
			$end_date = date('Y-m-d');
			$db->where("created_at",">", $start_date);
		}
		$number_of_days;

		$db->where('orders.status','=',1);
		$db->where('orders.customer_status','>=',1);

		$orders = $db->get();
		//pr($orders);die;
		
		return view('Admin.Exports.sales', [
            'orders' => $orders,
            'number_of_days' => $number_of_days,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }
	
}
?>