<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Client extends Model {

	public function addclient($client,$address,$contact)
	{
		$result = DB::table('client')->insert($client);

		$client_id = DB::getPdo()->lastInsertId();
		if(!empty($client_id))
		{
			$contact['client_id']=$client_id;
			$address['client_id']=$client_id;

			$result = DB::table('client_contact')->insert($contact);
			$result = DB::table('client_address')->insert($address);
		}
    	return $result;	
	}
	public function getClientdata()
	{

				$result =	DB::table('client as c')
        				 ->leftJoin('client_contact as cc','cc.client_id','=','c.client_id')
        				 ->select('c.client_id','c.client_company','cc.email','cc.first_name','cc.phone','cc.last_name','c.status')
        				 ->where('c.status','=','1')
        				 ->where('c.is_delete','=','1')
        				 ->get();
				return $result;	
	}
	public function DeleteClient($id)
    {
    	if(!empty($id))
    	{
    		$result = DB::table('client')->where('client_id','=',$id)->update(array("is_delete" => '0'));
    		return $result;
    	}
    	else
    	{
    		return false;
    	}
    }

}