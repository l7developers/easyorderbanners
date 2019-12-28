<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomQuoteRequest extends FormRequest
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
            'custom.fname' => 'required|string',
            'custom.lname' => 'required|string',
            'custom.email' => 'required|email',
            'custom.quantity' => 'required',
            'custom.material_type' => 'required',
            'custom.size' => 'required',
            'custom.due_date' => 'required',
            'custom.detail' => 'required',
        ];
    }
	
	public function messages()
	{
		return [
			'custom.fname.required' => 'First Name is required',
			'custom.lname.required' => 'Last Name is required',
			'custom.fname.min'  => 'First Name should have atleast 3 charaters',
			'custom.lname.min'  => 'Last  Name should have atleast 3 charaters',
			'custom.email.required'  => 'Email is required.',
			'custom.email.email'  => 'Please Enter Proper Email.',
			'custom.quantity.required'  => 'Quantity is required.',
			'custom.material_type.required'  => 'Material Type is required.',
			'custom.size.required'  => 'Size is required.',
			'custom.due_date.required'  => 'Project Due Date is required.',
			'custom.detail.required'  => 'Project Detail is required.',
		];
	}
}
