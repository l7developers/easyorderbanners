<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use DB;
use App\User;

class CustomersExports implements FromView{
	public $i = 1;
	
	public function __construct(){
       
    }
	
	public function view(): View{
		$db = User::where('role_id',3)->with('billing_add','shipping_add');
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
		}else{
			$start_date = date('Y-m-d', strtotime('-7 days'));
			$db->where("created_at",">", $start_date);
		}
		$users = $db->get();
		//pr($users);die;
		
		return view('Admin.Exports.customers', [
            'users' => $users,
        ]);
    }
}
?>