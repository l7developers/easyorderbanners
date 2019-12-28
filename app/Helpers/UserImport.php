<?php

namespace App\Helpers;

use DB;
use App\User;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements ToCollection,WithHeadingRow
{
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {  
        foreach ($rows as $row) 
        {
        	if($row['email'] !=""){
        		$cnt = User::where('email',$row['email'])->count();
        		if($cnt == 0)
        		{
	    			$user = new User();        	
					$user->role_id = 3;
					$user->fname = $row['first_name'];
					$user->lname = $row['last_name'];
					$user->email = trim($row['email']);			
					$user->phone_number = $row['phone_number'];
					$user->company_name = $row['company_name'];
					$user->pay_by_invoice = 0;
					$user->tax_exempt = 0;
					$user->status = '1';
					$user->password = bcrypt(trim($row['password']));
					$user->save();
				}	
        	}		
           
        }
    }
  
}
