<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Company;
use DB;
use Image;

use Request;
// CREATE COMPANY AND SET RIGHTS, MANAGE BY SUPER ADMIN ONLY
class CompanyController extends Controller {  


 	public function __construct(Company $company) 
 	{
        $this->company = $company;

    }


    /**
     * Get All account list data
     *
     * @param  limitstart,limitend.
     * @return Response, success, records, message
     */

	public function listData ()
	{

			$getData = $this->company->GetCompanyData();
			
			$count = count($getData);
			$success = 1;
			$message  = ($count>0)? GET_RECORDS:NO_RECORDS;
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
		if(!empty($post['email']) && !empty($post['password']) && !empty($post['user_name']) && !empty($post['role_id']) )
		{
			$post['password'] = md5($post['password']);
			$post['created_date'] = date('Y-m-d H:i:s');


			if(isset($post['company_logo']['base64'])){

                $split = explode( '/', $post['company_logo']['filetype'] );
                $type = $split[1]; 

		        $png_url = "company-".time().".".$type;
				$path = base_path() . "/public/uploads/company/" . $png_url;
				$img = $post['company_logo']['base64'];
				
				$data = base64_decode($img);
				$success = file_put_contents($path, $data);
				$post['company_logo'] = $png_url;
			   }


			$getData = $this->company->InsertCompanyData($post);
			
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
	public function GetData ($id,$company_id)
	{
		if(!empty($id) && !empty($company_id))
		{
			$getData = $this->company->GetCompanybyId($id,$company_id);
			
			$getData[0]->company_url_photo = UPLOAD_PATH.'company/'.$getData[0]->company_logo;

			$count = count($getData);
			if($count>0)
				{
					$message = GET_RECORDS;
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
			$post['updated_date'] = date('Y-m-d H:i:s');


                if(isset($post['company_logo']['base64'])){

            	$split = explode( '/', $post['company_logo']['filetype'] );
                $type = $split[1]; 

		        $png_url = "company-".time().".".$type;
				$path = base_path() . "/public/uploads/company/" . $png_url;
				$img = $post['company_logo']['base64'];
				
				$data = base64_decode($img);
				$success = file_put_contents($path, $data);
				

				$post['company_logo'] = $png_url;
			   }



			$getData = $this->company->SaveCompanyData($post);
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
			$getData = $this->company->DeleteCompanyData($post['id']);

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
