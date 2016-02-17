<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Client;
use App\Common;
use App\Art;
use DB;

use Request;

class ClientController extends Controller { 

	public function __construct(Client $client,Common $common,Art $art) 
 	{
        $this->client = $client;
        $this->common = $common;
        $this->art = $art;
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
	    $client['company_id'] = $post['company_id'];
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
    	$post = Input::all();
    	$result = $this->client->getClientdata($post);
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
	public function GetclientDetail()
	{
		$post = Input::all();
		$id = $post['client_id'];
		if(!empty($id))
		{
			$result = $this->client->GetclientDetail($id);
			
           
			$result['main']['color_url_photo'] = UPLOAD_PATH.'client/'.$result['main']['color_logo'];
			$result['main']['bw_url_photo'] = UPLOAD_PATH.'client/'.$result['main']['b_w_logo'];
			$result['main']['shipping_url_photo'] = UPLOAD_PATH.'client/'.$result['main']['shipping_logo'];
			$result['tax']['tax_document_url'] = UPLOAD_PATH.'tax/'.$result['tax']['tax_document'];
			if($result['sales']['anniversarydate'] != '')
			{
				$result['sales']['anniversarydate'] = date('m/d/Y',strtotime($result['sales']['anniversarydate']));
			}

			if(count($result)>0)
			{
				$StaffList = $this->common->getStaffList();
				$ArrCleintType=$this->common->TypeList('company');
				$AddrTypeData = $this->common->GetMicType('address_type',$post['company_id']);
				$Arrdisposition = $this->common->GetMicType('disposition',$post['company_id']);
				$allContacts=$this->client->getContacts($id);
				$allclientnotes = $this->client->GetNoteDetails($id);
				$Client_orders = $this->client->ListClientOrder($id);
				$art_detail = $this->art->Client_art_screen($post['client_id'],$post['company_id']);
				

				$records = array('clientDetail'=>$result,'StaffList'=>$StaffList,'ArrCleintType'=>$ArrCleintType,'AddrTypeData'=>$AddrTypeData, 'Arrdisposition'=>$Arrdisposition,
					'allContacts'=>$allContacts,'allclientnotes'=>$allclientnotes,'Client_orders'=>$Client_orders,'art_detail' => $art_detail);
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



		if(isset($post['data']['color_logo']['base64']))
		{

	    	$split = explode( '/', $post['data']['color_logo']['filetype'] );
	        $type = $split[1]; 

	        $png_url1 = "color-logo-".time().".".$type;
			$path = base_path() . "/public/uploads/client/" . $png_url1;
			$img = $post['data']['color_logo']['base64'];
			
			$data = base64_decode($img);
			$success = file_put_contents($path, $data);
			

			$post['data']['color_logo'] = $png_url1;

	    }

	    if(isset($post['data']['b_w_logo']['base64'])){

            	$split = explode( '/', $post['data']['b_w_logo']['filetype'] );
                $type = $split[1]; 

		        $png_url2 = "b-w-".time().".".$type;
				$path = base_path() . "/public/uploads/client/" . $png_url2;
				$img = $post['data']['b_w_logo']['base64'];
				
				$data = base64_decode($img);
				$success = file_put_contents($path, $data);
				

				$post['data']['b_w_logo'] = $png_url2;
	    }

	    if(isset($post['data']['shipping_logo']['base64'])){

            	$split = explode( '/', $post['data']['shipping_logo']['filetype'] );
                $type = $split[1]; 

		        $png_url3 = "shipping-".time().".".$type;
				$path = base_path() . "/public/uploads/client/" . $png_url3;
				$img = $post['data']['shipping_logo']['base64'];
				
				$data = base64_decode($img);
				$success = file_put_contents($path, $data);
				

				$post['data']['shipping_logo'] = $png_url3;
	    }


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

		 if(isset($post['data']['tax_document']['base64'])){

            	$split = explode( '/', $post['data']['tax_document']['filetype'] );
                $type = $split[1]; 

		        $png_url4 = "shipping-".time().".".$type;
				$path = base_path() . "/public/uploads/tax/" . $png_url4;
				$img = $post['data']['tax_document']['base64'];
				
				$data = base64_decode($img);
				$success = file_put_contents($path, $data);
				

				$post['data']['tax_document'] = $png_url4;
	    }

		$result = $this->client->SaveCleintTax($post['data'],$post['id']);

		if($post['data']['tax_exempt'] == '0' && $post['data']['tax_rate'] > 0)
		{
			$this->common->UpdateTableRecords('orders',array('client_id' => $post['id']),array('tax_rate' => $post['data']['tax_rate']));
		}

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


	 /**
   * Get Documents.
   * @return json data
   */
    public function getDocument($id)
    {

        $result = $this->client->getDocument($id);
        return $this->return_response($result);
        
    }

     /**
    * Get Document Details by ID
    * @params document_id
    * @return json data
    */
    public function getDocumentDetailbyId($id)
    {
        $result = $this->client->getDocumentDetailbyId($id);

        $result[0]->document_photo_url = UPLOAD_PATH.'document/'.$result[0]->document_photo;
        return $this->return_response($result);
    }


     /**
    * Update document tab record
    * @params document note array
    * @return json data
    */
    public function updateDoc()
    {
        $post = Input::all();

         if(isset($post['data'][0]['document_photo']['base64'])){

            	$split = explode( '/', $post['data'][0]['document_photo']['filetype'] );
                $type = $split[1]; 

		        $png_url_doc = "doc-logo-".time().".".$type;
				$path = base_path() . "/public/uploads/document/" . $png_url_doc;
				$img = $post['data'][0]['document_photo']['base64'];
				
				$data = base64_decode($img);
				$success = file_put_contents($path, $data);
				

				$post['data'][0]['document_photo'] = $png_url_doc;

	    }


        $result = $this->client->updateDoc($post['data'][0]);
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }


    /**
   * Save Order notes.
   * @return json data
    */
    public function saveDoc()
    {

        $post = Input::all();
        $post['data']['created_date']=date('Y-m-d');


        if(isset($post['data']['document_photo']['base64'])){

            	$split = explode( '/', $post['data']['document_photo']['filetype'] );
                $type = $split[1]; 

		        $png_url_doc = "doc-logo-".time().".".$type;
				$path = base_path() . "/public/uploads/document/" . $png_url_doc;
				$img = $post['data']['document_photo']['base64'];
				
				$data = base64_decode($img);
				$success = file_put_contents($path, $data);
				

				$post['data']['document_photo'] = $png_url_doc;

	    }

 
        if(!empty($post['data']['client_id']))
        {
            $result = $this->client->saveDoc($post['data']);
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
    * Delete Doc.
    * @params id
    * @return json data
    */
    public function deleteClientDoc($id)
    {
        $result = $this->client->deleteClientDoc($id);
        $data = array("success"=>1,"message"=>UPDATE_RECORD);
        return response()->json(['data'=>$data]);
    }




 } 
