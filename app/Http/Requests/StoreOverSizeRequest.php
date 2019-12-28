<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOverSizeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       return [
            'oversize.fname' => 'required|string',
            'oversize.lname' => 'required|string',
            'oversize.email' => 'required|email',
            'oversize.quantity' => 'required',
            'oversize.material_type' => 'required',
            'oversize.size' => 'required',
            'oversize.due_date' => 'required',
            'oversize.detail' => 'required',
        ];
    }
	
	public function messages()
	{
		return [
			'oversize.fname.required' => 'First Name is required',
			'oversize.lname.required' => 'Last Name is required',
			'oversize.fname.min'  => 'First Name should have atleast 3 charaters',
			'oversize.lname.min'  => 'Last  Name should have atleast 3 charaters',
			'oversize.email.required'  => 'Email is required.',
			'oversize.email.email'  => 'Please Enter Proper Email.',
			'oversize.quantity.required'  => 'Quantity is required.',
			'oversize.material_type.required'  => 'Material Type is required.',
			'oversize.size.required'  => 'Size is required.',
			'oversize.due_date.required'  => 'Project Due Date is required.',
			'oversize.detail.required'  => 'Project Detail is required.',
		];
	}
}
