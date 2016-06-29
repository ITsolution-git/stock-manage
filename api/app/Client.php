<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
 
class Client extends Model {

	public function addclient($client,$contact)
	{

		$result = DB::table('client')->insert($client);
		$client_id = DB::getPdo()->lastInsertId();
    $address = array();
		if(!empty($client_id))
		{
			$contact['client_id']=$client_id;
			$address['client_id']=$client_id;

			$result = DB::table('client_contact')->insert($contact);
			$result = DB::table('client_address')->insert(array('address'=>$client['pl_address'],'street'=>$client['pl_suite'],'city'=>$client['pl_city'],'state'=>$client['pl_state'],'postal_code'=>$client['pl_pincode'],'type'=>$client['client_companytype'],'client_id'=>$client_id,'address_main'=>1,'address_shipping'=>1,'address_billing'=>1));
		}
    	return $client_id;	
	}
	public function getClientdata($post)
	{
            $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }
    
        $listArray = [DB::raw('SQL_CALC_FOUND_ROWS c.client_id,c.client_id as id,c.client_company,cc.email,cc.first_name,cc.phone,cc.last_name,c.status,c.client_company as label')];
        $whereConditions = ['c.company_id' => $post['company_id']];
				$result =	DB::table('client as c')
        				 ->leftJoin('client_contact as cc','c.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
        				 ->select($listArray)
        				 ->where($whereConditions);
                 if($search != '')               
                  {
                     $result = $result->Where(function($query) use($search)
                          {
                              $query->orWhere('c.client_company', 'LIKE', '%'.$search.'%')
                                    ->orWhere('cc.first_name', 'LIKE', '%'.$search.'%')
                                    ->orWhere('cc.last_name', 'LIKE', '%'.$search.'%')
                                    ->orWhere('cc.email', 'LIKE', '%'.$search.'%')
                                    ->orWhere('cc.phone', 'LIKE', '%'.$search.'%');
                          });
                  }

                  $result = $result->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                  ->skip($post['start'])
                  ->take($post['range'])
                  ->get();
        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $result;
        $returnData['count'] = $count[0]->Totalcount;
        return $returnData;
	}
  public function getClientFilterData($post)
  {
        $whereConditions = ['c.status' => '1','c.company_id' => $post['cond']['company_id'],'c.is_delete' =>'1'];
        $result = DB::table('client as c')
                 ->leftJoin('client_contact as cc','c.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
                 ->select('c.client_id','c.client_id as id','c.client_company','cc.email','cc.first_name','cc.phone','cc.last_name','c.status','c.client_company as label')
                 ->where($whereConditions)
                 ->orderBy('c.client_id', 'desc')
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
    public function ClientContacts($table,$id)
    {
    	$result = DB::table($table)->insert(array('client_id'=>$id));
    	return $result;
    }
    public function getContacts($id)
    {
    	$result = DB::table('client_contact')->select('*')->where('client_id','=',$id)->get();
    	return $result;
    }
    public function clientAddress($post,$id,$address)
    {

    	//echo "<pre>"; print_r($address['address_main']); echo "</pre>"; die;
    	//echo $id; die;

    	DB::table('client_address')->where('client_id','=',$id)->delete();
    	$result = DB::table('client_address')->insert($post);
		
    
    	
    	return $result;
    }
    public function GetDistributionAddress($id)
    {
        $result = DB::table('client_distaddress as cd')
                  ->leftJoin('state as st','st.id','=','cd.state')
                  ->select('st.name as state_name','cd.*')
                  ->where('cd.client_id','=',$id)
                  ->get();

      
      $retArray = array('result'=>$result);
      //echo "<pre>"; print_r($retArray); echo "</pre>"; die;
      return $retArray;          
    }
    public function getAddress($id)
    {
    	  $result = DB::table('client_address as ca')
                  ->leftJoin('misc_type as mt','mt.id','=','ca.type')
                  ->leftJoin('state as st','st.id','=','ca.state')
                  ->select('mt.value as address_type','st.name as state_name','ca.*')
                  ->where('client_id','=',$id)
                  ->get();

    	/*if(count($result)>0)
    	{
    		$temp['address_main']='0'; $temp['address_shipping']='0'; $temp['address_billing']='0';
    		foreach ($result as $key => $value) {
    			  if($value->address_main=='1'){$temp['address_main']= $value->address;}
    			  if($value->address_shipping=='1'){$temp['address_shipping']= $value->address;}
    			  if($value->address_billing=='1'){$temp['address_billing']= $value->address;}

    		}}*/
    		
    	
    	$retArray = array('result'=>$result);
    	//echo "<pre>"; print_r($retArray); echo "</pre>"; die;
    	return $retArray;
    }
    
    public function GetclientDetail($id)
    {

    	$retArray = DB::table('client as c')
    				->select('st.name as state_name','st.id as state_id','pg.name as price_grid','stf.first_name as sales_fname','stf.last_name as sales_lname','tp.id as type_id','tp.name as type_name','mt.id as misc_id','mt.value as misc_value_p','ca.*','cc.*','cc.id as contact_id','c.*')
    				->leftJoin('client_contact as cc','c.client_id','=',DB::raw("cc.client_id AND cc.contact_main = '1' "))
    				->leftJoin('client_address as ca','c.client_id','=',DB::raw("ca.client_id AND ca.address_main = '1' "))
    				->leftJoin('misc_type as mt','mt.id','=',"c.client_desposition")
            ->leftJoin('staff as stf','stf.id','=',"c.salesperson")
    				->leftJoin('type as tp','tp.id','=',"c.client_companytype")
            ->leftJoin('price_grid as pg','pg.id','=','c.salespricegrid')
            ->leftJoin('state as st','st.id','=',"c.pl_state")
    				->where('c.client_id','=',$id)
    				->get();
    	$result = array();

    	
    	if(count($retArray)>0)
    	{
    		foreach ($retArray as $key => $value) 
    		{
    			$result['main']['client_id'] = $value->client_id;
    			$result['main']['client_company'] = $value->client_company;
    			//$result['main']['created_date'] = $value->created_date;
    			$result['main']['billing_email'] = $value->billing_email;
    			$result['main']['company_phone'] = $value->company_phone;
    			$result['main']['salesweb'] = $value->salesweb;

          $result['main']['type_id'] = $value->type_id;
    			$result['main']['client_companytype'] = $value->type_name;

    			$result['main']['misc_id'] = $value->misc_id;
          $result['main']['client_desposition'] = $value->misc_value_p;
          
          $result['main']['color_logo'] = $value->color_logo;
          $result['main']['b_w_logo'] = $value->b_w_logo;
          $result['main']['shipping_logo'] = $value->shipping_logo;
          $result['main']['blind_text'] = $value->blind_text;
          $result['main']['company_url'] = $value->company_url;

          $result['main']['client_address']  = !empty($value->pl_address)?$value->pl_address.",":'' ; 
          $result['main']['client_address'] .= !empty($value->pl_city)?$value->pl_city.", ":''; 
          $result['main']['client_address'] .= !empty($value->pl_suite)?$value->pl_suite.", ":''; 
          $result['main']['client_address'] .= !empty($value->state_name)?$value->state_name.", ":'';
          $result['main']['client_address'] .= !empty($value->pl_pincode)?$value->pl_pincode:'' ;

          $result['main']['pl_address'] = !empty($value->pl_address)?$value->pl_address:'' ;
          $result['main']['pl_city']    = !empty($value->pl_city)?$value->pl_city:'' ;
          $result['main']['pl_suite']   = !empty($value->pl_suite)?$value->pl_suite:'' ;
          $result['main']['state_name'] = !empty($value->state_name)?$value->state_name:'' ;
          $result['main']['pl_pincode'] = !empty($value->pl_pincode)?$value->pl_pincode:'' ;
          $result['main']['state_id']   = !empty($value->state_id)?$value->state_id:'' ;

          $result['main']['color_url_photo'] = (!empty($result['main']['color_logo']))?UPLOAD_PATH.$value->company_id.'/client/'.$value->client_id."/".$result['main']['color_logo']:'';
          $result['main']['bw_url_photo'] = (!empty($result['main']['b_w_logo']))?UPLOAD_PATH.$value->company_id.'/client/'.$value->client_id."/".$result['main']['b_w_logo']:'';
          $result['main']['shipping_url_photo'] = (!empty($result['main']['shipping_logo']))?UPLOAD_PATH.$value->company_id.'/client/'.$value->client_id."/".$result['main']['shipping_logo']:'';

          $result['contact']['contact_id'] = !empty($value->contact_id)?$value->contact_id:'0' ;
    			$result['contact']['email'] = $value->email;
    			$result['contact']['first_name'] = $value->first_name;
    			$result['contact']['last_name'] = $value->last_name;
    			$result['contact']['location'] = $value->location;
    			$result['contact']['phone'] = $value->phone;


    			$result['sales']['salesweb'] = $value->salesweb;
    			$result['sales']['anniversarydate'] = $value->anniversarydate;
    			$result['sales']['salesperson'] = $value->salesperson;
          $result['sales']['first_name'] = $value->sales_fname;
          $result['sales']['last_name'] = $value->sales_lname;
    			$result['sales']['salespricegrid'] = $value->salespricegrid;
          $result['sales']['price_grid'] = $value->price_grid;
          $result['sales']['anniversarydate'] = ($result['sales']['anniversarydate']=='0000-00-00')? '': date('m/d/Y',strtotime($result['sales']['anniversarydate']));

    			$result['tax']['tax_id'] = $value->tax_id;
    			$result['tax']['tax_rate'] = $value->tax_rate;
    			$result['tax']['tax_exempt'] = $value->tax_exempt;
          $result['tax']['tax_exempt_show'] = ($value->tax_exempt=='0')? "No" : "Yes";
          $result['tax']['tax_document'] = (empty($value->tax_document))? "1" : $value->tax_document ;
          $result['tax']['tax_document_url'] = (!empty($value->tax_document))?UPLOAD_PATH.$value->company_id."/tax/".$value->client_id."/".$value->tax_document:'';
          $result['tax']['tax_unlink_url'] = $value->tax_document;


    		}
    	}
    	return $result;
    }

   public function SaveSalesDetails($post,$id)
   {
   		$post['anniversarydate'] = date('Y-m-d',strtotime($post['anniversarydate']));
   		$result = DB::table('client')
   						->where('client_id','=',$id)
   						->update($post);
    	return $result;
   }
   public function SaveCleintDetails($post,$id)
   {

     unset($post['color_url_photo']);
     unset($post['bw_url_photo']);
     unset($post['shipping_url_photo']);

   		$result = DB::table('client')
   						->where('client_id','=',$id)
   						->update($post);
    	return $result;
   }
   public function SaveCleintTax($post,$id)
   {
    unset($post['tax_document_url']);
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
     public function GetNoteDetails($id)
   {
   		$result = DB::table('client_notes as cn')
   					->select('cn.client_notes','cn.note_id',DB::raw('DATE_FORMAT(cn.created_date, "%m/%d/%Y") as created_date'),'u.user_name')
   					->join('users as u','u.id','=','cn.user_id')
   					->where('cn.client_id','=',$id)
   					->where('cn.note_status','=','1')
   					->get();
   		return $result;
   }
   public function SaveCleintNotes($post)
   {
   		$result = DB::table('client_notes')->insert($post);
   		return $result;
   }
   public function  DeleteCleintNotes($id)
   {
   		$result = DB::table('client_notes')
   						->where('note_id','=',$id)
   						->update(array('note_status'=>'0'));
   		return $result;
   }
   public function GetClientDetailById($id)
   {
   		$result = DB::table('client_notes')->where('note_id','=',$id)->get();
   		return $result;
   }
   public function UpdateCleintNotes($post)
   {
   		$result = DB::table('client_notes')
   					->where('note_id','=',$post['note_id'])
   					->update(array('client_notes'=>$post['client_notes']));
   		return $result;
   }
   public function SaveDistAddress($post)
   {
   	
   }
   public function ListClientOrder($id)
   {
   		$result = DB::table('orders as ord')
   					->leftJoin('misc_type as mt','mt.id','=','ord.approval_id')
   					->select('mt.value','ord.id','ord.client_id','ord.name','ord.approval_id',DB::raw('DATE_FORMAT(ord.created_date, "%m/%d/%Y") as created_date'))
   					->where('ord.client_id','=',$id)
   					->where('ord.is_delete','=','1')
   					->get();

   		return $result;
   }
  public function checkCompName($post)
  {
    $result = DB::table('client')
              ->where ('company_id',"=",$post['company_id'])
              ->where ('client_company',"=",trim($post['value']));

    if(!empty($post['client_id']))
    {
      $result = $result->where('client_id','<>',$post['client_id']);
    }
      $result = $result->get();
      return count($result);
  }


/**
* Document          
* @access public getDocument
* @param  int $clientId
* @return array $result
*/ 

     public function getDocument($id,$company_id)
   {
       
        $whereConditions = ['client_id' => $id,'status' => '1','is_delete' => '1'];

        $clientDocumentData = DB::table('client_document')
                         ->select("*")
                         ->where($whereConditions)
                         ->get();

        $final_array = array();
        foreach ($clientDocumentData as $key => $all_data) 
        {

          $all_data->document_photo_url = '';
          $all_data->created_date = date('m/d/Y',strtotime( $all_data->created_date));
          if($all_data->document_photo != '')
          {
            $all_data->document_photo_url = UPLOAD_PATH.$company_id.'/document/'.$id.'/'.$all_data->document_photo;
          }
         $final_array[] = $all_data;
        }
      
        return $final_array;  

   }

   /**
* Document Note Details           
* @access public getDocumentDetailbyId
* @param  int $documentId
* @return array $result
*/


   public function getDocumentDetailbyId($id)
   {
        $result = DB::table('client_document')->where('id','=',$id)->get();
        return $result;
   }

   /**
* Update Order Note           
* @access public updateOrderNotes
* @param  array $post
* @return array $result
*/


    public function updateDoc($post)
   {
            $result = DB::table('client_document')
                        ->where('id','=',$post['id'])
                        ->update(array('description'=>$post['description'],'document_photo'=>$post['document_photo']));
        return $result;
   }


   /**
* Insert Document       
* @access public saveDoc
* @param  array $post
* @return array $result
*/


public function saveDoc($post)
   {
        $result = DB::table('client_document')->insert($post);
        return $result;
   }

public function saveTaxDoc($post)
{
      $result = DB::table('client')
                        ->where('client_id','=',$post['client_id'])
                        ->update(array('tax_document'=>$post['data']['tax_document']));
        return $result;
}

   /**
* Delete Doc          
* @access public deleteClientDoc
* @param  array $post
* @return array $result
*/
    public function  deleteClientDoc($id)
   {
        $result = DB::table('client_document')
                        ->where('id','=',$id)
                        ->update(array('is_delete'=>'0'));
        return $result;
   }

   public function getStaffDetail($id)
    {
        $whereConditions = ['s.user_id' => $id];
        $result = DB::table('staff as s')
                  ->leftJoin('state as st','st.id','=','s.prime_address_state')
                  ->leftJoin('users as u','u.id','=','s.user_id')
                  ->select('st.name as state_name','st.code as code','s.*','u.email')
                  ->where($whereConditions)
                  ->get();
      return $result;
    }



}