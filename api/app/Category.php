<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Category extends Model {

	public function addcategory($category)
	{
        $result = DB::table('finishing_category')->insert($category);
        $result = DB::getPdo()->lastInsertId();
        return $result;	
	}
    public function getCategoryByName($name)
    {
    	$result = DB::table('finishing_category')->select('id','category_name')->where('category_name','=',$name)->get();
    	return $result;
    }
}