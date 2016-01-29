<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Api extends Model {


	public function GetCompanyApi($post)
	{
		$query = DB::table('api_detail as ad')
				->select('*','ad.api_status')
			 	->leftJoin('api as a','a.api_id','=','ad.api_id')
				->get();
		return $query;
	}
	public function GetSNSData($snsid,$company_id)
	{
		$query = DB::table('api_detail as ad')
				->select('*','ad.api_status')
			 	->Join('api as a','a.api_id','=','ad.api_id')
			 	->where('ad.id','=',$snsid)
			 	->where('ad.company_id','=',$company_id)
				->get();

		
		if(count($query)>0 && !empty($query[0]->api_value))
		{
			$trans_array = unserialize($query[0]->api_value);
			$query[0]->api_key = $trans_array['key'];
			$query[0]->api_spassword = $trans_array['pass'];
		}
		//echo "<pre>"; print_r($query); echo "</pre>"; die;
		return $query;
	}
	public function save_api($post)
    {
    	$trans_array = serialize(array("key"=>$post['api_key'],'pass'=>$post['api_spassword']));
    	$result = DB::table('api_detail')
   						->where('id','=',$post['id'])
   						->update(array('api_user'=>$post['api_user'],'api_value'=>$trans_array,'api_status'=>$post['api_status'],'api_date'=>date('Y-m-d')));
    	return $result;
    }
}