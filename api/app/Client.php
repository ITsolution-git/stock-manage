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
        				 ->where('cc.contact_main','=','1')
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
    public function ClientContacts($post,$id)
    {
    	DB::table('client_contact')->where('client_id','=',$id)->delete();
    	$result = DB::table('client_contact')->insert($post);
    	return $result;
    }
    public function getContacts($id)
    {
    	$result = DB::table('client_contact')->select('first_name','last_name','location','phone','email','contact_main')->where('client_id','=',$id)->get();
    	return $result;
    }
    public function clientAddress($post,$id)
    {
    	DB::table('client_address')->where('client_id','=',$id)->delete();
    	$result = DB::table('client_address')->insert($post);
    	return $result;
    }
    public function getAddress($id)
    {
    	$result = DB::table('client_address')->select('address','city','state','postal_code','type','client_id')->where('client_id','=',$id)->get();
    	return $result;
    }
    
    public function GetclientDetail($id)
    {

    	$retArray = DB::table('client as c')
    				->select('ca.*','cc.*','c.*')
    				->leftJoin('client_contact as cc','c.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
    				->leftJoin('client_address as ca','c.client_id','=',DB::raw("ca.client_id AND ca.address_main = '1' "))
    				->where('c.client_id','=',$id)
    				->get();
    	$result = array();
    	if(count($retArray)>0)
    	{
    		foreach ($retArray as $key => $value) 
    		{
    			$result['main']['client_id'] = $value->client_id;
    			$result['main']['client_company'] = $value->client_company;
    			$result['main']['created_date'] = $value->created_date;

    			$result['contact']['email'] = $value->email;
    			$result['contact']['first_name'] = $value->email;
    			$result['contact']['first_name'] = $value->first_name;
    			$result['contact']['last_name'] = $value->last_name;
    			$result['contact']['location'] = $value->location;
    			$result['contact']['phone'] = $value->phone;

    			$result['address']['address'] = $value->address;
    			$result['address']['street'] = $value->street;
    			$result['address']['city'] = $value->city;
    			$result['address']['postal_code'] = $value->postal_code;
    			$result['address']['state'] = $value->state;

    			$result['sales']['salesweb'] = $value->salesweb;
    			$result['sales']['anniversarydate'] = $value->anniversarydate;
    			$result['sales']['salesperson'] = $value->salesperson;
    			$result['sales']['salespricegrid'] = $value->salespricegrid;

    		}
    	}
    	return $result;
    }

   public function SaveSalesDetails($post,$id)
   {
   		$date = date('Y-m-d',strtotime($post['anniversarydate']));
   		$result = DB::table('client')
   						->where('client_id','=',$id)
   						->update(array(
   										"anniversarydate" => $date,
   										"salesperson" =>$post['salesperson'],
   										"salespricegrid"=>$post['salespricegrid'] ,
   										"salesweb"=>$post['salesweb']
   										)
   								);
    	return $result;
   }
}