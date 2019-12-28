<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVolumeRequest extends FormRequest
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
           'volume.fname' => 'required|string',
            'volume.lname' => 'required|string',
            'volume.email' => 'required|email',
        ];
    }
	
	public function messages()
	{
		return [
			'volume.fname.required' => 'First Name is required',
			'volume.lname.required' => 'Last Name is required',
			'volume.fname.min'  => 'First Name should have atleast 3 charaters',
			'volume.lname.min'  => 'Last  Name should have atleast 3 charaters',
			'volume.email.required'  => 'Email is required.',
			'volume.email.email'  => 'Please Enter Proper Email.',
		];
	}
}
