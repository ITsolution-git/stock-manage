<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Account;
use DB;

use Request;
// CREATE COMPANY AND SET RIGHTS, MANAGE BY SUPER ADMIN ONLY
class AccountController extends Controller {  


 	public function __construct(Account $account) 
 	{
        $this->account = $account;

    }


    /**
     * Get All account list data
     *
     * @param  limitstart,limitend.
     * @return Response, success, records, message
     */

	public function listData ()
	{
		$getData = $this->account->GetCompanyData();

		$success = count($getData);
		$message  = ($success>0)? GET_RECORDS:NO_RECORDS;
		$data = array("records" => $getData,"success"=>$success,"message"=>$message);
		
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
		if(!empty($post['email']) && !empty($post['password']) && !empty($post['user_name']) )
		{
			$post['password'] = md5($post['password']);
			$post['created_at'] = date('Y-m-d H:i:s');
			$getData = $this->account->InsertCompanyData($post);
			
			if($getData)
			{
				$message = INSERT_RECORD;
				$success = 1;
			}
			else
			{
				$message = INSERT_ERROR;
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
     * Get All account list data BY id
     *
     * @param  id.
     * @return success message, data.
     */
	public function GetData ($id)
	{
		if(!empty($id))
		{
			$getData = $this->account->GetCompanybyId($id);
			$success = count($getData);
			$message  = ($success>0)? GET_RECORDS:NO_RECORDS;
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
		if(!empty($post['email']) && !empty($post['password']) && !empty($post['user_name']) && !empty($post['id']))
		{
			if($post['password']=='testcodal')
				{
					unset($post['password']);
				} 
			else 
				{
					$post['password']=md5($post['password']);
				}
			$getData = $this->account->SaveCompanyData($post);
			$message = UPDATE_RECORD;
			$success = 1;

		}
		else
		{
			$message = MISSING_PARAMS;
			$success = 0;
		}
		
		$data = array("success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);
	}
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
}
