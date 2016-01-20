<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Company extends Model {

    /**
     * login verify function
     *
     *
     */
    public function GetCompanyData() {
        $admindata = DB::table('users as usr')
        				 ->leftJoin('roles as rol', 'usr.role_id', '=', 'rol.id')
                         
        				 ->select('usr.name','usr.user_name','usr.email','usr.remember_token','usr.status','rol.title','usr.id')
        				 ->where('usr.is_delete','=','1')
                         ->where('rol.slug','=','CA')
                         ->get();
        return $admindata;
    }
    public function InsertCompanyData($post)
    {
        if(!isset($post['company_logo'])) {
            $post['company_logo'] = '';
        }
        $new_post = array('address'=>$post['address'],'city'=>$post['city'],'state'=>$post['state'],'country'=>$post['country'],'zip'=>$post['zip'],'url'=>$post['url'],'company_logo'=>$post['company_logo']);
        
        unset($post['address']);
        unset($post['city']);
        unset($post['state']);
        unset($post['country']);
        unset($post['zip']);
        unset($post['url']);
        unset($post['company_logo']);

    	$result = DB::table('users')->insert($post);
        $companyid = DB::getPdo()->lastInsertId();

       

        $companyid_array = array('company_id'=>$companyid);
            $result_array = array_merge((array)$new_post, (array)$companyid_array); 



        $result_company_detail = DB::table('company_detail')->insert($result_array);

    	return $result;
    }
    public function GetCompanybyId($id,$company_id)
    {
    	$admindata = DB::table('users as usr')
        				 ->leftJoin('roles as rol', 'usr.role_id', '=', 'rol.id')
                         ->leftJoin('company_detail as com', 'usr.id', '=', 'com.company_id')
        				 ->select('usr.name','usr.user_name','usr.email','usr.password','usr.remember_token','usr.status','usr.id','usr.role_id','com.address','com.city','com.state','com.country','com.zip','com.url','com.company_logo')
        				 ->where('usr.id','=',$id)
        				 ->where('usr.is_delete','=','1')
                         ->where('com.is_delete','=','1')
        				 ->where('usr.role_id','=',$company_id)
        				 ->get();
        return $admindata;
    }
    public function SaveCompanyData($post)
    {


        $new_post = array('address'=>$post['address'],'city'=>$post['city'],'state'=>$post['state'],'country'=>$post['country'],'zip'=>$post['zip'],'url'=>$post['url'],'company_logo'=>$post['company_logo']);
    	
        unset($post['address']);
        unset($post['city']);
        unset($post['state']);
        unset($post['country']);
        unset($post['zip']);
        unset($post['url']);
        unset($post['company_logo']);
        
        if(isset($post['company_url_photo'])){
         unset($post['company_url_photo']);
        }

         
        if(!empty($post['id']))
    	{
    		$result = DB::table('users')->where('id','=',$post['id'])->update($post);
            $result_address = DB::table('company_detail')->where('company_id','=',$post['id'])->update($new_post);
    		return $result;

           
    	}
    	else
    	{
    		return 0;
    	}
    }
    public function DeleteCompanyData($id)
    {
    	if(!empty($id))
    	{
    		$result = DB::table('users')->where('id','=',$id)->update(array("is_delete" => '0'));
    		$result = DB::table('users')->where('parent_id','=',$id)->update(array("is_delete" => '0'));
            $result = DB::table('company_detail')->where('company_id','=',$id)->update(array("is_delete" => '0'));
    		return 1;
    	}
    	else
    	{
    		return false;
    	}
    }


}
