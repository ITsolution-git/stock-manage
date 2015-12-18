<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Client;
use App\Common;
use DB;

use Request;

class ClientController extends Controller { 

	public function __construct(Client $client,Common $common) 
 	{
        $this->client = $client;
        $this->common = $common;
    }

    /**
    * Get Array of Client,contact and address table.
    * @return json data
    */
    public function addclient()
	{
		$client = array(); $contact = array(); $address = array();
		$post = Input::all();

	/* SEPARATE CLIENT DATA IN TO ARRAY */
		$client['client_company'] = $post['client_company'];
		$client['client_companytype'] = $post['client_companytype'];
		$client['created_date']=CURRENT_DATETIME;
		$client['status']='1';
	/* FINISH CLIENT DATA IN TO ARRAY */

		$address = $post['add'];	// SEPARATE ADDRESS ARRAY DATA

		$contact = $post['contact'];	// SEPARATE CONTACT ARRAY DATA
		$contact['contact_main']='1';	// SET ACTIVE CONDITION

		$result = $this->client->addclient($client,$address,$contact);	// PASS ARRAY IN CLIENT MODEL TO INSERT.

		if($result)
			{
				$message = INSERT_RECORD;
				$success = 1;
				$data = $result;
			}
			else
			{
				$message = INSERT_ERROR;
				$success = 0;
				$data = '';
			}
		$data = array("success"=>$success,"message"=>$message,"data"=>$data);
		return response()->json(['data'=>$data]);
		
	}

	/**
    * Get Array
    * @return json data
    */
    public function return_response($result)
    {
        if (count($result) > 0) 
        {
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
        } 
        else 
        {
            $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $result);
        }
        return  response()->json(["data" => $response]);
    }

    /**
    * Get Array List of All Client details
    * @return json data
    */
    public function ListClient()
    {
    	$result = $this->client->getClientdata();
    	return $this->return_response($result);
    }
    /**
     * Delete Data
     *
     * @param  post.
     * @return success message.
     */
	public function DeleteClient()
	{
		$post = Input::all();

		if(!empty($post['id']))
		{
			$getData = $this->client->DeleteClient($post['id']);
			if($getData)
			{
				$message = DELETE_RECORD;
				$success = 1;
			}
			else
			{
				$message = MISSING_PARAMS;
				$success = 0;
			}
		}
		else
		{
			$message = MISSING_PARAMS;
			$success = 0;
		}
		$data = array("success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);

	}
	/**
    * INSERT CONTACTS AS MULTIPLE SENT
    * @return json data
    */
	public function ClientContacts()
	{
		$post = Input::all();
		if(!empty($post['id']))
		{
			foreach ($post['data'] as $key => $value) 
			{
				$post['data'][$key]['client_id']=$post['id'];
				if(isset($post['maincontact']) && $post['maincontact']==$key)
				{
					$post['data'][$key]['contact_main'] = '1';
				}
				else
				{
					$post['data'][$key]['contact_main'] = '0';
				}
			}
			$message = INSERT_RECORD;
			$success = 1;
			$this->client->ClientContacts($post['data'],$post['id']);
		}
		else
		{
			$message = MISSING_PARAMS.", id";
			$success = 0;
		}
		
		$data = array("success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);
		
	}
	/**
    * Get Array List of All Client contacts for edit tab display
    * @return json data
    */
	public function getContacts()
	{
		$post = Input::all();
		$result = $this->client->getContacts($post[0]);
    	return $this->return_response($result);
	}
	/**
    * INSERT MULTIPLE ADDRESS FROM CLIENT EDIT FORM
    * @return json data
    */
	public function clientAddress()
	{
		$post = Input::all();
		if(!empty($post['id']))
		{
			foreach ($post['data'] as $key => $value) 
			{
				$post['data'][$key]['client_id']=$post['id'];
				unset($post['data'][$key]['id']);
				unset($post['data'][$key]['address_main']);
				unset($post['data'][$key]['address_shipping']);
				unset($post['data'][$key]['address_billing']);
				unset($post['data'][$key]['street']);
				
			}


			//echo "<pre>"; print_r($post['data']); echo "</pre>"; die;
			$message = INSERT_RECORD;
			$success = 1;
			$this->client->clientAddress($post['data'],$post['id'],$post['permadd']);
		}
		else
		{
			$message = MISSING_PARAMS.", id";
			$success = 0;
		}
		
		$data = array("success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);
	}
	/**
    * Get Array List of All Client Address
    * @return json data
    */
	public function getAddress()
	{
		$post = Input::all();
		$result = $this->client->getAddress($post[0]);
    	return $this->return_response($result);
	}
	/**
    * Get Array List of Client details(added from client create page)
    * @return json data
    */
	public function GetclientDetail($id)
	{
		if(!empty($id))
		{
			$result = $this->client->GetclientDetail($id);
			if(count($result)>0)
			{
				$StaffList = $this->common->getStaffList();
				$ArrCleintType=$this->common->TypeList('company');
				$AddrTypeData = $this->common->GetMicType('address_type');
				$Arrdisposition = $this->common->GetMicType('disposition');
				$allContacts=$this->client->getContacts($id);
				$allclientnotes = $this->client->GetNoteDetails($id);
				$Client_orders = $this->client->ListClientOrder($id);

				$records = array('clientDetail'=>$result,'StaffList'=>$StaffList,'ArrCleintType'=>$ArrCleintType,'AddrTypeData'=>$AddrTypeData, 'Arrdisposition'=>$Arrdisposition,
					'allContacts'=>$allContacts,'allclientnotes'=>$allclientnotes,'Client_orders'=>$Client_orders);
	    		$data = array("success"=>1,"message"=>UPDATE_RECORD,'records'=>$records);
    		}
    		else
    		{
    			$data = array("success"=>0,"message"=>NO_RECORDS);
    		}
    	}
    	else
    	{
    		$data = array("success"=>0,"message"=>MISSING_PARAMS);
    	}
    	
		return response()->json(['data'=>$data]);
    	
	}
	/**
    * Sales tabe in client edit form data seve, refrence of client ID.
    * @return json data
    */
	public function SaveSalesDetails()
	{
		$post = Input::all();
		$result = $this->client->SaveSalesDetails($post['data'],$post['id']);

    	$data = array("success"=>1,"message"=>UPDATE_RECORD);
		return response()->json(['data'=>$data]);
	}
	/**
    * Update client form data, .
    * @return json data
    */
	public function SaveCleintDetails()
	{
		$post = Input::all();
		$result = $this->client->SaveCleintDetails($post['data'],$post['id']);

    	$data = array("success"=>1,"message"=>UPDATE_RECORD);
		return response()->json(['data'=>$data]);
	}
	/**
    * Update client Tax data, .
    * @return json data
    */
	public function SaveCleintTax()
	{
		$post = Input::all();
		$result = $this->client->SaveCleintTax($post['data'],$post['id']);

    	$data = array("success"=>1,"message"=>UPDATE_RECORD);
		return response()->json(['data'=>$data]);
	}

	public function SaveCleintPlimp()
	{
		$post = Input::all();
		$result = $this->client->SaveCleintPlimp($post['data'],$post['id']);

    	$data = array("success"=>1,"message"=>UPDATE_RECORD);
		return response()->json(['data'=>$data]);
	}
/**
   * Get Client notes.
   * @return json data
   */
	public function GetNoteDetails($id)
	{
		$result = $this->client->GetNoteDetails($id);
   	return $this->return_response($result);
	}

	/**
   * Save Client notes.
   * @return json data
    */
	public function SaveCleintNotes()
	{
		$post = Input::all();
		$post['data']['created_date']=date('Y-m-d');
 
	
		if(!empty($post['data']['client_id']) && !empty($post['data']['client_notes']))
		{
			$result = $this->client->SaveCleintNotes($post['data']);
			$message = INSERT_RECORD;
			$success = 1;
		}
		else
		{
			$message = MISSING_PARAMS.", id";
			$success = 0;
		}
		
    	$data = array("success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);
	}
	/**
    * Delete Client note tab record.
    * @params note_id
    * @return json data
    */
	public function DeleteCleintNotes($id)
	{
		$result = $this->client->DeleteCleintNotes($id);
		$data = array("success"=>1,"message"=>UPDATE_RECORD);
		return response()->json(['data'=>$data]);
	}
	/**
    * Get Client Details by ID
    * @params client_id
    * @return json data
    */
	public function GetClientDetailById($id)
	{
		$result = $this->client->GetClientDetailById($id);
    	return $this->return_response($result);
	}
	/**
    * Update Client Note tab record
    * @params client note array
    * @return json data
    */
	public function UpdateCleintNotes()
	{
		$post = Input::all();
		$result = $this->client->UpdateCleintNotes($post['data'][0]);
		$data = array("success"=>1,"message"=>UPDATE_RECORD);
		return response()->json(['data'=>$data]);
	}
	/**
    * Save Distribution address
    * @params Form array
    * @return json data
    */
	public function SaveDistAddress()
	{
		$post = Input::all();
		
		if(!empty($post['data']['client_id']) && !empty($post['data']['address']))
		{
			$result = $this->client->SaveDistAddress($post);
			$message = INSERT_RECORD;
			$success = 1;
		}
		else
		{
			$message = MISSING_PARAMS.", id";
			$success = 0;
		}
		
    	$data = array("success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);
	}
	/**
    * Check Unique name of client for Company
    * @params Form array with client name and company_id
    * @return json data
    */
	public function checkCompName()
	{
		$post = Input::all();
		if(!empty($post['data']['value']) && !empty($post['data']['company_id']))
		{
			$result = $this->client->checkCompName($post['data']);
			$message = 'success';
			$success = 1;
		}
		else
		{
			$result=1;
			$message = MISSING_PARAMS;
			$success = 0;
		}
		
    	$data = array("success"=>$success,"message"=>$message,'result'=>$result);
		return response()->json(['data'=>$data]);
	}
 } 
