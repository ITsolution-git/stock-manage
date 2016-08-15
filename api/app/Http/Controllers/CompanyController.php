<?php
namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Company;
use App\Common;
use DB;
use Image;
use Request;
// CREATE COMPANY AND SET RIGHTS, MANAGE BY SUPER ADMIN ONLY
class CompanyController extends Controller {
	public function __construct(Company $company, Common $common)
	{
$this->company = $company;
$this->common = $common;
}
/**
* Get All account list data
*
* @param  limitstart,limitend.
* @return Response, success, records, message
*/
	public function listData ()
	{
		$post_all = Input::all();
        $records = array();

        $post = $post_all['cond']['params'];

    	if(!isset($post['page']['page'])) {
             $post['page']['page']=1;
        }
        $post['range'] = RECORDS_PER_PAGE;
        $post['start'] = ($post['page']['page'] - 1) * $post['range'];
        $post['limit'] = $post['range'];
        
        if(!isset($post['sorts']['sortOrder'])) {
             $post['sorts']['sortOrder']='desc';
        }
        if(!isset($post['sorts']['sortBy'])) {
            $post['sorts']['sortBy'] = 'usr.id';
        }

    	$result = $this->company->GetCompanyData($post);
    	$records = $result['allData'];
    	$success = (empty($result['count']))?'0':1;
    	$message = (empty($result['count']))?NO_RECORDS:GET_RECORDS;
        $result['count'] = (empty($result['count']))?'1':$result['count'];
        $pagination = array('count' => $post['range'],'page' => $post['page']['page'],'pages' => RECORDS_PAGE_RANGE,'size' => $result['count']);

        $header = array(
                        0=>array('key' => 'usr.name', 'name' => 'Name'),
                        1=>array('key' => 'usr.email', 'name' => 'Email'),
                        2=>array('key' => 'usr.created_date', 'name' => 'Create Date'),
                        );

        $data = array('header'=>$header,'rows' => $records,'pagination' => $pagination,'sortBy' =>$post['sorts']['sortBy'],'sortOrder' => $post['sorts']['sortOrder'],'success'=>$success,'message'=>$message);
        return  response()->json($data);
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
		if(!empty($post['email']) && !empty($post['name']))
		{
			$post['role_id'] = 17;
			$email = $this->common->checkemailExist($post['email'],0); // CHECK EMAIL EXIST, FOR ALL USERS
			if(count($email)>0)
			{
				$message = "Email already exists!";
				$success = 0;
				$data = '';
			}
			else
			{
				$post['password'] = md5('admin');
				$post['created_date'] = date('Y-m-d H:i:s');
				$getData = $this->company->InsertCompanyData($post);
				
				if($getData)
				{
					$message = INSERT_RECORD;
					$success = 1;
					$data = $getData;
				}
				else
				{
					$message = INSERT_ERROR;
					$success = 0;
					$data = '';
				}
			}
		}
		else
		{
			$message = MISSING_PARAMS;
			$success = 0;
			$data = '';
		}
		$data = array("success"=>$success,"message"=>$message,'data'=>$data);
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
			
			if(count($getData)>0)
			{
				if(empty($getData[0]->staff_id))
				{
					$getData[0]->staff_id = $this->common->InsertRecords('staff',array('user_id'=>$id,'is_delete'=>1));
				}
			$getData[0]->company_url_photo = UPLOAD_PATH.$id."/staff/".$getData[0]->staff_id."/".$getData[0]->photo;
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

		if(!empty($post['email']) && !empty($post['id']) && !empty($post['name']))
		{
			$email = $this->common->checkemailExist($post['email'],$post['id']); // CHECK UNIQUE EMIAL IN USERS TABLE
			if(count($email)>0)
			{
				$message = "Email already exists!";
				$success = 0;
			}
			else
			{
				$post['updated_date'] = date('Y-m-d H:i:s');
				$getData = $this->company->SaveCompanyData($post);
				//$this->common->UpdateTableRecords('client',array('company_id' => $post['id']),array('tax_rate' => $post['tax_rate']));
				
				//$company_data = $this->common->GetTableRecords('client',array('company_id' => $post['id']),array());

				//foreach ($company_data as $company) {
				//	if($company->tax_exempt == 0)
				//	{
						//$this->common->UpdateTableRecords('orders',array('client_id' => $company->client_id),array('tax_rate' => $post['tax_rate']));
					//}
			//}

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
	public function change_password()
	{
		$post = Input::all();
		if(!empty($post['password']) && !empty($post['new_password']) && !empty($post['confirm_password']) && !empty($post['user_id'])) // CHECK VALIDATION 
		{
			if($post['new_password']==$post['confirm_password']) // CHECK BOTH PASSWORD SAME
			{
				$company_data = $this->common->GetTableRecords('users',array('id' => $post['user_id']),array()); // GET USER DATA
				if(count($company_data)>0)
				{
					$pass = md5($post['password']);
					if($pass==$company_data[0]->password)
					{
						$this->common->UpdateTableRecords('users',array('id' => $post['user_id']),array('password' =>md5($post['new_password']) )); // SUCCESS ANDY UPDATE PASSWORD
						$message = "Password successfully changed.";
						$success = 1;
					}
					else
					{
						$message = "Password you provided is wrong, Please try again!";
						$success = 0;
					}
				}
				else
				{
					$message = MISSING_PARAMS;
					$success = 0;
				}
			}
			else 
			{
				$message = "New password and confirm password do not match, Please try again";
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
	public function getCompanyInfo($company_id)
	{
		if(!empty($company_id))
		{
			$result = $this->company->getCompanyInfo($company_id);
			if(count($result)>0)
			{
				$result = $result[0];
			}
			$message = GET_RECORDS;
			$success = 1;
		}
		else
		{
			$message = MISSING_PARAMS;
			$success = 0;
			$result="";
		}
		$data = array("success"=>$success,"message"=>$message,"data"=>$result);
		return response()->json(['data'=>$data]);
	}
	public function getAffiliate($company_id,$affilite_id)
	{
		if(!empty($company_id))
		{
			$result = $this->company->getAffiliate($company_id,$affilite_id); // getAffiliate DATA
			if(count($result)>0)
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
			$result="";
		}
		$data = array("success"=>$success,"message"=>$message,"data"=>$result);
		return response()->json(['data'=>$data]);

	}
	public function addAffilite()
	{
		$post = Input::all();
		if(!empty($post['name']))
		{
			//echo "<pre>"; print_r($post); echo "</pre>"; die;
			$post['screen_print']=!empty($post['screen_print'])? 1:0;
			$post['embroidery']=!empty($post['embroidery'])? 1:0;
			$post['packing']=!empty($post['packing'])? 1:0;
			$post['shipping']=!empty($post['shipping'])? 1:0;
			$post['art_work']=!empty($post['art_work'])? 1:0;

			$post['created_date']=CURRENT_DATE;

			$result = $this->company->addAffilite($post); // INSERT DATA
			$message = INSERT_RECORD;
			$success = 1;
		}
		else
		{
			$message = MISSING_PARAMS."- Name";
			$success = 0;
		}
		$data = array("success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);
	}
	public function UpdateAffilite()
	{
		$post = Input::all();
		if(!empty($post['name']))
		{
			$post['screen_print']=!empty($post['screen_print'])? 1:0;
			$post['embroidery']=!empty($post['embroidery'])? 1:0;
			$post['packing']=!empty($post['packing'])? 1:0;
			$post['shipping']=!empty($post['shipping'])? 1:0;
			$post['art_work']=!empty($post['art_work'])? 1:0;
			$id = $post['id'];
			$post['price_grid'] = $post['price_id'];
			unset($post['id']);
			unset($post['price_id']);
			$result = $this->company->UpdateAffilite($post,$id); // INSERT DATA
			$message = UPDATE_RECORD;
			$success = 1;
		}
		else
		{
			$message = MISSING_PARAMS."- Name";
			$success = 0;
		}
		$data = array("success"=>$success,"message"=>$message);
		return response()->json(['data'=>$data]);
	}
	public function getAuthorizeAPI($company_id)
	{
		if(!empty($company_id))
		{
			$result = $this->company->getAuthorizeAPI($company_id); // GET API DETAILS
			if(count($result)==0)
			{
				$this->company->InsertAuthorizeAPI($company_id);
				$result = $this->company->getAuthorizeAPI($company_id); // GET API DETAILS
			}
			$message = GET_RECORDS;
			$success = 1;
		}
		else
		{
			$message = MISSING_PARAMS."- company_id";
			$success = 0;
			$result = '';
		}
		$data = array("success"=>$success,"message"=>$message,'data'=>$result);
		return response()->json(['data'=>$data]);
	}
	public function getUpsAPI($company_id)
	{
		if(!empty($company_id))
		{
			$result = $this->company->getUpsAPI($company_id); // GET API DETAILS
			if(count($result)==0)
			{
				$this->company->InsertUpsAPI($company_id);
				$result = $this->company->getUpsAPI($company_id); // GET API DETAILS
			}
			$message = GET_RECORDS;
			$success = 1;
		}
		else
		{
			$message = MISSING_PARAMS."- company_id";
			$success = 0;
			$result = '';
		}
		$data = array("success"=>$success,"message"=>$message,'data'=>$result);
		return response()->json(['data'=>$data]);
	}
	public function getSnsAPI($company_id)
	{
		if(!empty($company_id))
		{
			$result = $this->company->getSnsAPI($company_id); // GET API DETAILS
			//echo count($result);
			if(count($result)==0)
			{
				$this->company->InsertSnsAPI($company_id);
				$result = $this->company->getSnsAPI($company_id); // GET API DETAILS
			}
			$message = GET_RECORDS;
			$success = 1;
		}
		else
		{
			$message = MISSING_PARAMS."- company_id";
			$success = 0;
			$result = '';
		}
		$data = array("success"=>$success,"message"=>$message,'data'=>$result);
		return response()->json(['data'=>$data]);
	}

	public function getQBAPI($company_id)
	{
		if(!empty($company_id))
		{
			$result = $this->company->getQBAPI($company_id); // GET API DETAILS
			//echo count($result);
			if(count($result)==0)
			{
				$this->company->InsertSnsAPI($company_id);
				$result = $this->company->InsertQBAPI($company_id); // GET API DETAILS
			}
			$message = GET_RECORDS;
			$success = 1;
		}
		else
		{
			$message = MISSING_PARAMS."- company_id";
			$success = 0;
			$result = '';
		}
		$data = array("success"=>$success,"message"=>$message,'data'=>$result);
		return response()->json(['data'=>$data]);
	}

	
}