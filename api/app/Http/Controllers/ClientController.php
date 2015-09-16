<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Client;
use DB;

use Request;

class ClientController extends Controller { 

	public function __construct(Client $client) 
 	{
        $this->client = $client;
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
			}
			else
			{
				$message = INSERT_ERROR;
				$success = 0;
			}
		$data = array("success"=>$success,"message"=>$message);
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
			}

			//echo "<pre>"; print_r($post['data']); echo "</pre>"; die;
			$message = INSERT_RECORD;
			$success = 1;
			$this->client->clientAddress($post['data'],$post['id']);
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

		$result = $this->client->GetclientDetail($id);
    	return $this->return_response($result);
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
}