<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;
use \App\Products;

class Category extends Model{
	
    protected $table = 'categories';
	
	public function child_list(){
		 return $this->hasMany('\App\Category','parent_id')->whereIn('status',array(1,0))->with('child_list');		
	}
	
	public function child(){
		 return $this->hasMany('\App\Category','parent_id')->where('status',1)->with('child')->orderBy('weight','ASC');		
	}
	
	public function childProducts(){
		 return $this->hasMany('\App\Category','parent_id')->where('status',1)->with('childProducts','products')->orderBy('weight','ASC');		
	}
	
	public function products(){
		 return $this->hasMany('\App\Products','category_id')->with('product_image')->where('status',1)->orderBy('weight','ASC');		
	}
	
	public function detail($id){
		return DB::table('categories')->where('id', $id)->first();
	}
	
	public function allProducts($id,$sub_cat_id=null)
    {
        $categoryIds = array_merge([$this->id], $this->subcategoryIds());

        $data['detail'] =  $this->detail($id);
		if($sub_cat_id != null){
			$data['sub_cat'] = $this->detail($sub_cat_id);
		}
        $data['products'] =  Products::select('id','slug','name','image','image_title','price')->whereIn('category_id', $categoryIds)->with('Images')->limit(10)->orderBy('weight','ASC')->get();
		return $data;
    }

    protected function subcategoryIds($id = null, &$ids= [])
    {
        if (is_null($id)) {
            $id = $this->id;
        }

        $categoryIds = $this->query()->where('parent_id', $id)->pluck('id');
			
        foreach ($categoryIds as $categoryId) {
            $ids[] = $categoryId;
            $ids += $this->subcategoryIds($categoryId, $ids);
        }
        return $ids;
    }
}
