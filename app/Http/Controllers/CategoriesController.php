<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use App\Category;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except' => ['detail']]);
    }

    public function detail($slug=null,Request $request)
    {
		$category=Category::where('slug',$slug)->with('products')->first();
		//pr($category->toArray());die;
		if(!empty($category->meta_title))
			$pageTitle = $category->meta_title;
		else
			$pageTitle = $category->name;
		return view('category/detail',compact('pageTitle','category'));
    }	
}
