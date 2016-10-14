<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Account;
use App\Common;
use DB;
use Mail;


use Request;
// CREATE COMPANY AND SET RIGHTS, MANAGE BY SUPER ADMIN ONLY
class AccountController extends Controller {  


 	public function __construct(Account $account, Common $common, Login $login) 
 	{
        $this->account = $account;
        $this->common = $common;
        $this->login = $login;

    }


    /**
     * Get All account list data
     *
     * @param  limitstart,limitend.
     * @return Response, success, records, message
     */

	public function listData ($parent_id)
	{
		if(!empty($parent_id) )
		{
			$getData = $this->account->GetCompanyData($parent_id);
			$count = count($getData);
			$success = 1;
			$message  = ($count>0)? GET_RECORDS:NO_RECORDS;
			$data = array("records" => $getData,"success"=>$success,"message"=>$message);
		}
		else
		{
			$message = MISSING_PARAMS ." - parent_id";
			$success = 0;
			$data = array("success"=>$success,"message"=>$message);
		}
		return response()->json(['data'=>$data]);
	}

	 /**
     * Get All account list data
     *
     * @param  user_name,email,password,role_id.
     * @return success message.
     */
	public function addData ()
	{
		$post = Input::all();

		//echo "<pre>"; print_r($post); echo "</pre>"; die;
		if(!empty($post['email']) && !empty($post['password']) && !empty($post['parent_id']))
		{
			$password = $post['password'];
			$usr_email = trim($post['email']);
			$user_name = trim($post['name']);
			
			$post['password'] = md5($post['password']);
			$post['created_date'] = date('Y-m-d H:i:s');

			$email = $this->common->checkemailExist($usr_email,0);
			if(count($email)>0)
			{
				$message = "Email Exists";
				$success = 0;
			}
			else
			{
				$getData = $this->account->InsertCompanyData($post);
				if($getData)
				{
			 		//$string = $this->login->getString(6);

					Mail::send('emails.newcompany', ['password' =>$password,'user'=>$user_name,'email'=>$usr_email], function($message) use ($usr_email) 
	                {
	                    $message->to($usr_email, 'New Stokkup Account')->subject('New Account for Stokkup');
	                });
					$message = INSERT_RECORD;
					$success = 1;
				}
				else
				{
					$message = MISSING_PARAMS;
					$success = 0;
				}
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
     * Get All account list data BY id
     *
     * @param  id.
     * @return success message, data.
     */
	public function GetData ($id,$parent_id)
	{
		if(!empty($id) && !empty($parent_id))
		{
			$getData = $this->account->GetCompanybyId($id,$parent_id);

			//echo "<pre>"; print_r($getData); echo "</pre>"; die;
			$count = count($getData);
			if($count>0)
			{
				if(empty($getData[0]->staff_id))
				{
					$getData[0]->staff_id = $this->common->InsertRecords('staff',array('user_id'=>$id,'is_delete'=>1));
				}
				$success = 1;
				$message  = GET_RECORDS;
			}
			else 
			{
				$success = 0;
				$message  = NO_RECORDS;

			}
			
		}
		else
		{
			$message = MISSING_PARAMS;
			$success = 0;
		}

		$data = array("records" => $getData,"success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);
	}

	/**
     * Save Edit Data
     *
     * @param  post.
     * @return success message.
     */
	public function SaveData ()
	{
		$post = Input::all();
		if(!empty($post['email']) && !empty($post['id']) && !empty($post['parent_id']))
		{

			$email = $this->common->checkemailExist($post['email'],$post['id']);
			if(count($email)>0)
			{
				$message = "Email Exists";
				$success = 0;
			}
			else
			{
				
				$post['updated_date'] = date('Y-m-d H:i:s');
				$getData = $this->account->SaveCompanyData($post);
				$message = UPDATE_RECORD;
				$success = 1;
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
     * Delete Data
     *
     * @param  post.
     * @return success message.
     */
	public function DeleteData()
	{
		$post = Input::all();

		if(!empty($post['id']))
		{
			$getData = $this->account->DeleteCompanyData($post['id']);
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
	public function ResetPasswordMail()
	{
		$post = Input::all();

		if(!empty($post['user_id']) && !empty($post['company_id']))
		{
			 $result = $this->common->GetTableRecords('users',array("id"=>$post["user_id"],'parent_id'=>$post['company_id']),array());
			 if(count($result)>0)
			 {
			 	$email = $result[0]->email;
			 	$string = $this->login->getString(6);
			 	Mail::send('emails.newpassword', ['url' =>$string,'user'=>$result[0]->name,'email'=>$email], function($message) use ($email) 
                {
                    $message->to($email, 'Hello, Your password has been changed, Your New Password is')->subject('Your New Password for Stokkup');
                });
				$this->common->UpdateTableRecords('users',array('id' => $post['user_id']),array('password' =>md5($string), 'reset_password'=>'1'));
                $message = "New password sent Successfully";
				$success = 1;
			 }
			 else
			 {
			 	$message = NO_RECORDS;
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
}
