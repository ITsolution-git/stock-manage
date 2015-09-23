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
        				 ->leftJoin('client_contact as cc','c.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
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
    public function clientAddress($post,$id,$address)
    {

    	//echo "<pre>"; print_r($address['address_main']); echo "</pre>"; die;
    	//echo $id; die;

    	DB::table('client_address')->where('client_id','=',$id)->delete();
    	$result = DB::table('client_address')->insert($post);
		
		DB::table('client_address')->where('address','=',$address['address_main'])->where('client_id','=',$id)->update(array("address_main" => '1'));
		DB::table('client_address')->where('address','=',$address['address_billing'])->where('client_id','=',$id)->update(array("address_billing" => '1'));
    	DB::table('client_address')->where('address','=',$address['address_shipping'])->where('client_id','=',$id)->update(array("address_shipping" => '1'));
    	
    
    	
    	return $result;
    }
    public function getAddress($id)
    {
    	$result = DB::table('client_address')->where('client_id','=',$id)->get();

    	$temp = array();
    	if(count($result)>0)
    	{
    		$temp['address_main']='0'; $temp['address_shipping']='0'; $temp['address_billing']='0';
    		foreach ($result as $key => $value) {
    			   if($value->address_main=='1'){$temp['address_main']= $value->address;}
    			  if($value->address_shipping=='1'){$temp['address_shipping']= $value->address;}
    			  if($value->address_billing=='1'){$temp['address_billing']= $value->address;}

    		}
    		
    	}
    	$retArray = array('result'=>$result,'address'=>$temp);
    	//echo "<pre>"; print_r($retArray); echo "</pre>"; die;
    	return $retArray;
    }
    
    public function GetclientDetail($id)
    {

    	$retArray = DB::table('client as c')
    				->select('tp.*','mt.*','ca.*','cc.*','c.*')
    				->leftJoin('client_contact as cc','c.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
    				->leftJoin('client_address as ca','c.client_id','=',DB::raw("ca.client_id AND ca.address_main = '1' "))
    				->leftJoin('misc_type as mt','mt.id','=',"c.client_desposition")
    				->leftJoin('type as tp','tp.id','=',"c.client_type")
    				->where('c.client_id','=',$id)
    				->get();
    	$result = array();

    	
    	if(count($retArray)>0)
    	{
    		foreach ($retArray as $key => $value) 
    		{
    			//$result['main']['client_id'] = $value->client_id;
    			$result['main']['client_company'] = $value->client_company;
    			//$result['main']['created_date'] = $value->created_date;
    			$result['main']['billing_email'] = $value->billing_email;
    			$result['main']['company_phone'] = $value->company_phone;
    			$result['main']['salesweb'] = $value->salesweb;
    			$result['main']['client_type'] = $value->client_type;
    			$result['main']['client_desposition'] = $value->client_desposition;


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

    			$result['tax']['tax_id'] = $value->tax_id;
    			$result['tax']['tax_rate'] = $value->tax_rate;
    			$result['tax']['tax_exempt'] = $value->tax_exempt;

    			$result['pl_imp']['pl_businessname'] = $value->pl_businessname;
    			$result['pl_imp']['pl_address'] = $value->pl_address;
    			$result['pl_imp']['pl_city'] = $value->pl_city;
    			$result['pl_imp']['pl_state'] = $value->pl_state;
    			$result['pl_imp']['pl_pincode'] = $value->pl_pincode;
    			$result['pl_imp']['pl_account_status'] = $value->pl_account_status;
    			$result['pl_imp']['pl_salesrep'] = $value->pl_salesrep;
    			$result['pl_imp']['pl_deposite'] = $value->pl_deposite;
    			$result['pl_imp']['pl_tax'] = $value->pl_tax;
    			$result['pl_imp']['pl_contactid'] = $value->pl_contactid;
    			$result['pl_imp']['pl_contact'] = $value->pl_contact;
    			$result['pl_imp']['pl_firstname'] = $value->pl_firstname;
    			$result['pl_imp']['pl_lastname'] = $value->pl_lastname;
    			$result['pl_imp']['pl_businesstitle'] = $value->pl_businesstitle;
    			$result['pl_imp']['pl_email'] = $value->pl_email;
    			$result['pl_imp']['pl_homenumber'] = $value->pl_homenumber;
    			$result['pl_imp']['pl_fax'] = $value->pl_fax;
    			$result['pl_imp']['pl_altphone'] = $value->pl_altphone;
    			$result['pl_imp']['pl_notes'] = $value->pl_notes;



    		}
    	}
    	return $result;
    }

   public function SaveSalesDetails($post,$id)
   {
   		$date = date('Y-m-d',strtotime($post['anniversarydate']));
   		$result = DB::table('client')
   						->where('client_id','=',$id)
   						->update($post);
    	return $result;
   }
   public function SaveCleintDetails($post,$id)
   {
   		$result = DB::table('client')
   						->where('client_id','=',$id)
   						->update($post);
    	return $result;
   }
   public function SaveCleintTax($post,$id)
   {
   		$result = DB::table('client')
   						->where('client_id','=',$id)
   						->update($post);
    	return $result;
   }
    public function SaveCleintPlimp($post,$id)
   {
   		$result = DB::table('client')
   						->where('client_id','=',$id)
   						->update($post);
    	return $result;
   }
}