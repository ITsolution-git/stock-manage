<?php

namespace App;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Common extends Model {

/**
* Get admin roles controller      
* @access public getAdminRoles
* @return array $roles
*/
    public function getAdminRoles() {
        $roles = DB::table('roles')->where('slug','<>','SA')->orderby('title','asc')->get();
        return $roles;
    }
    public function checkemailExist($email,$userid)
    {
    	$data = DB::table('users')->where('email','=',trim($email));

        if(!empty($userid))
            {  $data= $data->where('id','<>',trim($userid)); }
        
        $data = $data->get();
        return $data;
    }

/**
* Get type list controller      
* @access public TypeList
* @param  int $type
* @return array $typeData
*/

    public function TypeList($type) {
        $typeData = DB::table('type')->where('status','=','1')->where('type','=',$type)->get();
        return $typeData;
    }

/**
* Get staff roles,input params [7,8] 7 = SuperAdmin,8=Facility Manager
* @access public getStaffRoles
* @return array $staffRoles
*/

    public function getStaffRoles() {
        
        $staffRoles = DB::table('roles')->whereNotIn('slug', ['SA','CA'])->get();

        return $staffRoles;
    }

/**
* Get All Vendors
* @access public getAllVendors
* @return array $staffRoles
*/

    public function getAllVendors($company_id) {
        
        $whereVendorConditions = ['status' => '1','is_delete' => '1','company_id'=>$company_id];
        $vendorData = DB::table('vendors')->where($whereVendorConditions)->get();
        return $vendorData;
    }

/**
* Get All Misc type
* @access public getAllMiscData
* @return array $Misc
*/

    public function getAllMiscData($post) {
        
        $whereMiscConditions = ['status' => '1','is_delete' => '1','company_id' => $post['cond']['company_id']];
        $MiscData = DB::table('misc_type')->where($whereMiscConditions)->get();

        $allData = array ();
        foreach($MiscData as $data) {
           
           if($data->value == ''){
            $data->value = '-'; 
            $allData[$data->type][] = $data;
           } else {
            $allData[$data->type][] = $data;
           }


            

        }

          
        return $allData;
    }


    /**
* Get All Misc type
* @access public getAllMiscDataWithoutBlank
* @return array $Misc
*/

    public function getAllMiscDataWithoutBlank($post) {
        
        $whereMiscConditions = ['status' => '1','is_delete' => '1','company_id' => $post['cond']['company_id']];
        $MiscData = DB::table('misc_type')->where($whereMiscConditions)->get();

        $allData = array ();
        foreach($MiscData as $data) {
           
           if($data->value != ''){
            $allData[$data->type][$data->id] = $data;
           }

        }

        return $allData;
    }

    public function GetMicType($type,$company_id)
    {
        $whereVendorConditions = ['status' => '1','is_delete' => '1','type'=>$type,'company_id'=>$company_id];
        $misc_type = DB::table('misc_type')->where($whereVendorConditions)->where('value','!=','')->get();
        return $misc_type;
    }
    public function getStaffList($company_id) // SALES EMPLOYEE LIST
    {
        $staffData = DB::table('sales')
                         ->select('*','sales_name as label',DB::raw('DATE_FORMAT(sales_created_date, "%m/%d/%Y") as sales_created_date'))
                         ->where('sales_delete','=','1')
                         ->where('company_id','=',$company_id)
                         ->get();
        return $staffData;
    }


    public function getBrandCordinator($company_id)
    {
       


        $whereConditions = ['users.status' => '1','users.is_delete' => '1','roles.slug' => 'AM','users.parent_id' => $company_id];
        $listArray = ['users.id','users.name'];

        $brandCordinatorData = DB::table('users as users')
                         ->Join('roles as roles', 'users.role_id', '=', 'roles.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        return $brandCordinatorData;
    }


    public function InsertRecords($table,$records)
    {
        $result = DB::table($table)->insert($records);

        $id = DB::getPdo()->lastInsertId();

        return $id;
    }
    public function GetTableRecords($table,$cond,$notcond=array(),$sort=0,$sortBy=0,$select='')
    {
        $result = DB::table($table);
        if(!empty($select))
        {
            $result =$result ->select($select);
        }
        if(count($cond)>0)
        {
            foreach ($cond as $key => $value) 
            {
                if(!empty($value))
                $result =$result ->where($key,'=',$value);
            }
        }

        if(count($notcond)>0)
        {
            foreach ($notcond as $key => $value) 
            {
                
                $result =$result ->where($key,'!=',$value);
            }
        }

        if(!empty($sort) && !empty($sortBy))
        {
            $result =$result ->orderBy($sort, $sortBy);
        }

        $result=$result->get();
        return $result;
    }
    public function UpdateTableRecords($table,$cond,$data,$date_field=1)
    {
        if($date_field=='date')
        {
             foreach ($data as $key => $value) 
            {
                if(!empty($value))
                {
                    $data[$key] = date("Y-m-d", strtotime($value));
                }
                
            }
        }
        if($date_field=='web_http')
        {
             foreach ($data as $key => $value) 
            {
                if (preg_match('/http/',$value) == false) 
                {
                    $data[$key] = "http://".$value;
                }
                
            }
        }
        if(isset($data['order_sns_status']))
        {
            $data['updated_date'] = CURRENT_DATETIME;
        }
        if(isset($data['shipping_by']))
        {
            $data['shipping_by'] = date("Y-m-d", strtotime($data['shipping_by']));
        }
        if(isset($data['in_hands_by']) && $data['in_hands_by'] != '')
        {
            $data['in_hands_by'] = date("Y-m-d", strtotime($data['in_hands_by']));
        }
        if(isset($data['date_shipped']) && $data['date_shipped'] != '')
        {
            $data['date_shipped'] = date("Y-m-d", strtotime($data['date_shipped']));
        }
        if(isset($data['fully_shipped']))
        {
            $data['fully_shipped'] = date("Y-m-d", strtotime($data['fully_shipped']));
        }

        $result = DB::table($table);
        if(count($cond)>0)
        {
            foreach ($cond as $key => $value) 
            {
                if(!empty($value))
                    $result =$result ->where($key,'=',$value);
            }
        }
        $result=$result->update($data);
        return $result;
    }

     public function DeleteTableRecords($table,$cond)
    {

        $result = DB::table($table);
        if(count($cond)>0)
        {
            foreach ($cond as $key => $value) 
            {
                if(!empty($value))
                    $result =$result ->where($key,'=',$value);
            }
        }
        $result=$result->delete();
        return $result;
    }

 /**
* Get All Placement data
* @access public getAllPlacementData
* @return array $Misc
*/

    public function getAllPlacementData($post) {
        

        $listArray = ['placement.misc_id','placement.misc_value','placement.id','misc_type.value as position'];

        $wherePlacementConditions = ['placement.status' => '1','placement.is_delete' => '1','placement.company_id' => $post['cond']['company_id']];
        $placementData = DB::table('placement')
        ->leftJoin('misc_type as misc_type','placement.misc_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$post['cond']['company_id']))
        ->select($listArray)
        ->where($wherePlacementConditions)->get();          
        return $placementData;
    }

      /**
* Get All Misc type
* @access public getMiscData
* @return array $Misc
*/

    public function getMiscData($post) {
        
        $whereMiscConditions = ['status' => '1','is_delete' => '1','type'=>'position','company_id' => $post['cond']['company_id']];
        $miscData = DB::table('misc_type')->select('id','value')->where($whereMiscConditions)->where('value','!=','')->get();       
        return $miscData;
    }


/**
* Get All Color Data
* @access public getAllColorData
* @return array $colorData
*/

    public function getAllColorData() {
        
        $whereColorConditions = ['status' => '1','is_delete' => '1','is_sns'=>1];
        $colorData = DB::table('color')->select('id','name')->where($whereColorConditions)->where('name','!=','')->orderby('name')->get();       
        return $colorData;
    }



    /**
    * Get userId
    * @access All user 
    * @return array $result with user and company detail
    */

    public function CompanyService($userId)
    {
        $result = DB::table('users as us')
                ->leftJoin('users as usr','usr.id','=','us.parent_id')
                ->leftJoin('roles as r','r.id','=','us.role_id')
                ->select('r.title',DB::raw('case 
                           when r.slug = "CA" then us.user_name else usr.user_name
                            end as company
                        '),DB::raw('case 
                           when r.slug = "CA" then us.id else usr.id
                            end as company_id
                        '),DB::raw('case 
                           when r.slug = "CA" then us.name else usr.name
                            end as company_name
                        '))
                ->where('us.id','=',$userId)
                ->get();
       
        return $result;
    }


    public function getCompanyDetail($company_id)
    {
        $admindata = DB::table('users as usr')
                         ->leftJoin('roles as rol', 'usr.role_id', '=', 'rol.id')
                         ->leftJoin('staff as s', 'usr.id', '=', 's.user_id')
                         ->leftJoin('company_detail as cd', 'usr.id', '=', 'cd.company_id')
                         ->leftJoin('state as st', 'st.id', '=', 's.prime_address_state')
                         ->select('usr.name','usr.user_name','usr.email','usr.password','usr.remember_token','usr.status','usr.id','usr.role_id','s.first_name','s.last_name',
                                    's.prime_address1','s.prime_address_city','st.code as prime_address_state','s.prime_address_country','s.prime_address_zip','s.prime_phone_main as phone',
                                    's.url','s.photo','s.oversize_value','cd.company_logo','cd.address','cd.city','cd.state','cd.country','cd.zip','cd.url')
                         ->where('usr.id','=',$company_id)
                         ->where('usr.is_delete','=','1')
                         ->where('s.is_delete','=','1')
                         ->get();
        return $admindata;
    }
    public function SaveImage($post)
    {
        
        $png_url='';
         $image_array = $post['image_array'];
         $field = $post['field'];
         $table = $post['table'];
         $image_name = $post['image_name'];
         $image_path = $post['image_path'];
         $cond = $post['cond'];
         $value = $post['value'];

        if(!empty($image_array['base64'])){

                $split = explode( '/',$image_array['filetype'] );
                $type = $split[1]; 

                $png_url = $image_name."-".time().".".$type;
                $image_path = FILEUPLOAD.$image_path;
                
                if (!file_exists($image_path)) {
                        mkdir($image_path, 0777, true);
                    } else {
                     exec("chmod $image_path 0777");
                       // chmod($dir_path, 0777);
                    }
                $image_path = $image_path."/".$png_url;     
                $img = $image_array['base64'];
                $data = base64_decode($img);
                $success = file_put_contents($image_path, $data);

                $query = DB::table($table)->where($cond,'=',$value)->update(array($field=>$png_url));

                if($post['unlink_url'] != ''){
                    exec('rm -rf '.escapeshellarg($post['unlink_url']));
                }
            }
            return $png_url;
    }
    public function UpdateDate($post)
    {
        $date = date('Y-m-d',strtotime($post['date']));
        $result = DB::table($post['table'])->where($post['cond'],'=',$post['value'])->update(array($post['field']=>$date));
        return $result;
    }

    public function deleteImage($table,$cond,$data,$delete_image)
    {
        $result = DB::table($table);
        if(count($cond)>0)
        {
            foreach ($cond as $key => $value) 
            {
                if(!empty($value))
                    $result =$result ->where($key,'=',$value);
            }
        }
        $result=$result->update($data);

        if($delete_image) {
              $delete_image_url = base_path() . "/public/uploads/" . $delete_image;
             exec('rm -rf '.escapeshellarg($delete_image_url));
        }
        return $result;
    }

     public function checkExistData($email,$id,$column_name,$table_name,$company_id)
    {
        $data = DB::table($table_name)->where($column_name,'=',trim($email));

        if(!empty($id))
            {  $data= $data->where('id','<>',trim($id)); }
        if(!empty($company_id))
            {  $data= $data->where('company_id','=',trim($company_id)); }
        
        $data = $data->get();
        return $data;
    }

     public function getColorId($name) {
        
        $whereColorConditions = ['status' => '1','is_delete' => '1','name'=>$name];
        $colorData = DB::table('color')->select('id','name')->where($whereColorConditions)->get();       
        return $colorData;
    }

    public function truncateTable($table)
    {
        DB::table($table)->truncate();
    }

    public function checkImageExist($path,$image='')
    {
        if(empty($image))
        {
            return NOIMAGE;
        }
        else
        {
            return file_exists(FILEUPLOAD.$path.$image)?UPLOAD_PATH.$path.$image:NOIMAGE;
        }
        
    }
    public function GetCompanyUsers($company_id)
    {
        $whereConditions = ['usr.status' => '1','usr.is_delete' => '1','usr.parent_id' => $company_id];
        $listArray = ['usr.id','usr.name'];

        $Companyuser = DB::table('users as usr')
                         ->Join('roles as roles', 'usr.role_id', '=', 'roles.id')
                         ->select($listArray)
                         ->where($whereConditions)
                         ->where('roles.slug','<>','CA')
                         ->where('roles.slug','<>','SA')
                         ->orderby('usr.name','ASC')
                         ->get();

        return $Companyuser;
    }

    public function setDisplayNumber($table,$company_id,$comp_field="company_id",$pkey="id",$lastId)
    {
          //echo $pkey; die();

          $sec_number = DB::table($table.' as tb')
                            ->where($comp_field,$company_id)
                            ->where('display_number','=','0')
                            ->orderby($pkey,'asc')
                            ->get();

            //echo count($sec_number); die();
            if(count($sec_number)>0) // CHECK THE 0 DATA TO BE RESET
            {
                foreach ($sec_number as $key=>$value) 
                {
                   // echo $value->$pkey;
                    $inc_id = $lastId+$key+1; // SET NEW ID BY INCREMENT OF LATEST ID
                    $this->UpdateTableRecords($table,array($pkey=>$value->$pkey),array('display_number'=>$inc_id)) ;   // UPDATE NEW INCREMENTED ID     
                }
            }
            else
            {
                $inc_id = $lastId;
            }
            //$this->getDisplayNumber($table,$company_id,$comp_field,$pkey); // CHECK AGAIN TO SET NEW DISPLAY ID
            return $inc_id+1;
           // die();
    }
    public function setAffiliateDisplayNumber($table,$company_id,$comp_field="company_id",$pkey="id",$lastId)
    {
          //echo $pkey; die();

          $sec_number = DB::table($table.' as tb')
                            ->where($comp_field,$company_id)
                            ->where('affiliate_display_number','=','0')
                            ->orderby($pkey,'asc')
                            ->get();

            //echo count($sec_number); die();
            if(count($sec_number)>0) // CHECK THE 0 DATA TO BE RESET
            {
                foreach ($sec_number as $key=>$value) 
                {
                   // echo $value->$pkey;
                    $inc_id = $lastId+$key+1; // SET NEW ID BY INCREMENT OF LATEST ID
                    $this->UpdateTableRecords($table,array($pkey=>$value->$pkey),array('affiliate_display_number'=>$inc_id)) ;   // UPDATE NEW INCREMENTED ID     
                }
            }
            else
            {
                $inc_id = $lastId;
            }
            //$this->getDisplayNumber($table,$company_id,$comp_field,$pkey); // CHECK AGAIN TO SET NEW DISPLAY ID
            return $inc_id+1;
           // die();
    }
    public function getDisplayNumber($table,$company_id,$comp_field="company_id",$pkey="id",$call="no")
    {

        $sec_number = DB::table($table.' as tb')
                            ->select(DB::raw('MAX(tb.display_number) as disp_number'))
                            ->where($comp_field,$company_id)
                            ->get();
        
        if(empty($sec_number[0]->disp_number))
        {
            $lastId= 0; // THERE IS NO RECORD
        }
        else
        {
            $lastId= $sec_number[0]->disp_number+1; // THERE ARE RECORDS AND LATEST DISPLAY ID
        }    

        if($call=="yes")
        {
            $lastId = $this->setDisplayNumber($table,$company_id,$comp_field,$pkey,$sec_number[0]->disp_number); // RESET ALL UNALLOCATED ID
            return $lastId; // RETURN LATEST INCREMENTED ID
        }
        else
        {
           if($lastId==0){$lastId=1;} 
           return $lastId; // RETURN LATEST INCREMENTED ID
        }
    }
    public function getAffiliateDisplayNumber($table,$company_id,$comp_field="company_id",$pkey="id",$call="no")
    {

        $sec_number = DB::table($table.' as tb')
                            ->select(DB::raw('MAX(tb.affiliate_display_number) as disp_number'))
                            ->where($comp_field,$company_id)
                            ->get();
        
        if(empty($sec_number[0]->disp_number))
        {
            $lastId= 0; // THERE IS NO RECORD
        }
        else
        {
            $lastId= $sec_number[0]->disp_number+1; // THERE ARE RECORDS AND LATEST DISPLAY ID
        }    

        if($call=="yes")
        {
            $lastId = $this->setAffiliateDisplayNumber($table,$company_id,$comp_field,$pkey,$sec_number[0]->disp_number); // RESET ALL UNALLOCATED ID
            return $lastId; // RETURN LATEST INCREMENTED ID
        }
        else
        {
           if($lastId==0){$lastId=1;} 
           return $lastId; // RETURN LATEST INCREMENTED ID
        }
    }


/**
* Get All Misc Production Data type
* @access public getAllMiscData
* @return array $Misc
*/

    public function getAllMiscDataProduction($client_id) {

         $whereMiscConditions = ['status' => '1','is_delete' => '1','company_id' => $client_id,'type' => 'approval'];
        $MiscData = DB::table('misc_type')->where($whereMiscConditions)->whereNotIn('slug', [137,149,150,151,568,569])->get();

        $allData = array ();
        
        foreach($MiscData as $data) {
           
            $allData[] = $data->id;
          
        }
        return $allData;
    }
}
