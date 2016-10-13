<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Api extends Model {


	public function GetCompanyApi($company_id)
	{
		
		$query = DB::table('api as a')
				->select('*','at.status')
			 	->Join('api_link_table as at','a.api_id','=','at.api_id')
			 	->where('at.company_id','=',$company_id)
				->get();
		return $query;
	}
	public function getApiData($post)
	{

	
		$table_name = $post["table"] . ' as ad';
		
		$query = DB::table($table_name)
				->select('*','api.status')
				->Join('api_link_table as api','api.id','=','ad.link_id')
			 	->Join('api as a','a.api_id','=','api.api_id')
			 	->where('ad.link_id','=',$post['id'])
				->get();
		return $query;
	}

	 public function checkApi($post)
    {
    	$data = DB::table('api_link_table')
    			->where('api_id','=',$post['api_id'])
    			->where('company_id','=',$post['company_id']);

        $data = $data->get();
        return $data;
    }

    public function getApiCredential($company_id,$page,$table,$where=array())
	{
		$table_name = $table . ' as ad';
		$whereApiConditions = ['a.api_page' => $page,'api.company_id' => $company_id];

		$result = DB::table($table_name)
				->select('*','api.status')
				->Join('api_link_table as api','api.id','=','ad.link_id')
			 	->Join('api as a','a.api_id','=','api.api_id')
			 	->where($whereApiConditions);
			 	if(!empty($where))
			 	{
			 		$result=$result->where($where);
			 	}
				$result=$result->get();
		return $result;
	}
	
}