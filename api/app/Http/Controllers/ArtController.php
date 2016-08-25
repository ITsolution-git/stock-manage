<?php

namespace App\Http\Controllers;
require_once(app_path() . '/constants.php');
use App\Login;
use Input;
use Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Common;
use App\Art;
use DB;
use File;
use PDF;
use Request;
use Response;

class ArtController extends Controller { 

    public function __construct(Art $art,Common $common) 
    {
        $this->art = $art;
        $this->common = $common;
    }

    // ART LISTING PAGE


 /**
 * @SWG\Get(
 *  path = "/api/public/art/listing/{company_id}",
 *  summary = "Art LIsting",
 *  tags={"Art"},
 *  description = "Art LIsting",
 *  @SWG\Parameter(
 *     in="path",
 *     name="company_id",
 *     description="Art LIsting",
 *     type="integer",
 *     required=true
 *  ),
 *  @SWG\Response(response=200, description="Art LIsting"),
 *  @SWG\Response(response="default", description="Art LIsting"),
 * )
 */

    public function listing($company_id)
    {
        if(!empty($company_id)  && $company_id != 'undefined')
        {
           
            $result = $this->art->listing($company_id);
            if(count($result)>0)
            {
                $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $result);
            }
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS);
            }
            
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS,'records' => $result);
        }
        return  response()->json(["data" => $response]);
    }

    //ARTJOB-  ART DETAIL TAB WITH POSITION AND ORDERLINE TAB DATA.
    public function Art_detail($art_id,$company_id)
    {
        if(!empty($company_id) && !empty($art_id)   && $company_id != 'undefined')
        {
            $art_position = $this->art->art_position($art_id,$company_id);
            $art_orderline = $this->art->art_orderline($art_id,$company_id);
            $artjobscreen_list = $this->art->artjobscreen_list($art_id,$company_id);  // SCREEN LISTING DATA
            $artjobgroup_list = $this->art->artjobgroup_list($art_id,$company_id); // GROUP LIST DATA

            $art_worklist = $this->art->art_worklist($art_id,$company_id);  // ART WORK LISTING DATA

            $graphic_size = $this->common->GetMicType('graphic_size',$company_id);
            //$allcolors = $this->common->getAllColorData();
            $wp_position = $this->common->GetMicType('position',$company_id);
            $art_approval = $this->common->GetMicType('approval',$company_id);

            $art_array  = array('art_position'=>$art_position,'art_orderline'=>$art_orderline,'artjobscreen_list'=>$artjobscreen_list,'graphic_size'=>$graphic_size,'artjobgroup_list'=>$artjobgroup_list,'art_worklist'=>$art_worklist,'wp_position'=>$wp_position,'art_approval'=>$art_approval);
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $art_array);
        }
        else 
        {
            $response = array('success' => 2, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }

    //ARTJOB-  ART WORKPROOF POPUP DATA RETRIVE
    public function artworkproof_data($wp_id, $company_id)
    {
        if(!empty($company_id) && !empty($wp_id)    && $company_id != 'undefined')
        {
            $art_workproof = $this->art->artworkproof_data($wp_id,$company_id);

            if(count($art_workproof)>0)
            {
                
                $art_id = $art_workproof[0]->art_id;
                $get_artworkproof_placement = $this->art->get_artworkproof_placement($art_id,$company_id);


                $ret_array = array('art_workproof'=>$art_workproof,'get_artworkproof_placement'=>$get_artworkproof_placement);
                $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $ret_array);
            }
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS);
            }
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }

    // ARTJOB-  SCREEN SETS TAB DATA LISTING
    public function artjobscreen_list($art_id, $company_id)
    {
        if(!empty($company_id) && !empty($art_id)   && $company_id != 'undefined')
        {
            $artjobscreen_list = $this->art->artjobscreen_list($art_id,$company_id);
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $artjobscreen_list);
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    // ARTJOB-  GROUP TAB DATA LISTING
    public function artjobgroup_list($art_id, $company_id)
    {
        if(!empty($company_id) && !empty($art_id)   && $company_id != 'undefined')
        {
            $artjobgroup_list = $this->art->artjobgroup_list($art_id,$company_id);
            $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $artjobgroup_list);
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function update_orderScreen()
    {
        $post = Input::all();
        if(!empty($post['data']) && !empty($post['cond']))
        {
            $artjobgroup_list = $this->art->update_orderScreen($post);
            $response = array('success' => 1, 'message' => UPDATE_RECORD);
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);

    }
    public function ScreenSets()
    {
        $post = Input::all();
        if(!empty($post['company_id']) && !empty($post['order_id']))
        {
            $scren_listing = $this->art->ScreenSets($post);
            if(count($scren_listing)>0)
            {
                $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $scren_listing);
            }
            else
            {
                $response = array('success' => 0, 'message' => 'No Positions are assign to this Order.');
            }
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function SaveArtWorkProof()
    {
        $post = Input::all();
        //echo "<pre>"; print_r($post); echo "</pre>"; die;
        if(!empty($post['wp_id']))
        {
            
            $val = array_filter($post['wp_placement']);
            $post['wp_placement'] = implode(",", $val);
            //echo "<pre>"; print_r($post['wp_placement']); echo "</pre>"; die;
            //echo FILEUPLOAD; die;
            $post['save_image'] = $this->Ret_imageUrl($post['wp_image'],'Artwork-logo','art/'.$post['art_id']);

            $this->art->SaveArtWorkProof($post);
            $response = array('success' => 1, 'message' => UPDATE_RECORD);
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }

    public function Ret_imageUrl($image_array,$image_name,$path)
    {
        $png_url='';
        if(!empty($image_array['base64'])){

                $split = explode( '/',$image_array['filetype'] );
                $type = $split[1]; 

                $png_url = $image_name."-".time().".".$type;
                $path = FILEUPLOAD.$path;
                
                if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    } else {
                     exec("chmod $path 0777");
                       // chmod($dir_path, 0777);
                    }
                $path = $path."/".$png_url;     
                $img = $image_array['base64'];
                $data = base64_decode($img);
                $success = file_put_contents($path, $data);
            }
            return $png_url;
    }
    public function Client_art_screen($client_id,$company_id)
    {
        if(!empty($company_id) && !empty($client_id)    && $company_id != 'undefined')
        {
            $Client_art_screen = $this->art->Client_art_screen($client_id,$company_id);
            if(count($Client_art_screen)>0)
            {
                $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $Client_art_screen);
            }
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $Client_art_screen);
            }
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
 
    public function screen_colorpopup ($screen_id,$company_id)
    {
        if(!empty($company_id) && !empty($screen_id)    && $company_id != 'undefined')
        {
            $screen_colorpopup = $this->art->screen_colorpopup($screen_id,$company_id);
            
            $graphic_size = $this->common->GetMicType('graphic_size',$company_id);
            $screen_arts = $this->art->screen_arts($screen_id,$company_id);
            $screen_garments = $this->art->screen_garments($screen_id,$company_id);
            $art_approval = $this->common->GetMicType('approval',$company_id);
           
            $color_array= array();
            $allcolors = $this->common->getAllColorData();
            foreach ($allcolors as $key => $value) 
            {
                $color_array[$value->id]= $value->name;
                $allcolors[$key]->name = strtolower($value->name);
            }
            
            //echo "<pre>"; print_r($allcolors); echo "</pre>"; die;
            if(count($screen_colorpopup)>0)
            {
                foreach ($screen_colorpopup as $key => $value) 
                {
                    $screen_colorpopup[$key]->color_name = (!empty($value->color_name))? $color_array[$value->color_name]:'';
                    $screen_colorpopup[$key]->thread_color = (!empty($value->thread_color))? $color_array[$value->thread_color]:'';
                }
            }
            if(count($screen_garments)>0)
            {
                foreach ($screen_garments as $key => $value) 
                {
                    $screen_garments[$key]->color_id = (!empty($value->color_id))? $color_array[$value->color_id]:'';
                }
            }   

            $ret_array = array('screen_colorpopup'=>$screen_colorpopup,'screen_arts'=>$screen_arts,'graphic_size'=>$graphic_size,'screen_garments'=>$screen_garments,'art_approval'=>$art_approval,'allcolors'=>$allcolors);
            
            if(count($screen_colorpopup)>0)
            {
                $response = array('success' => 1, 'message' => GET_RECORDS,'records' => $ret_array);
            }
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS,'records' => $ret_array);
            }

        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function create_screen()
    {
        $post = Input::all();

        if(!empty($post['alldata']['id']))
        {
            $this->art->create_screen($post);
            $response = array('success' => 1, 'message' => UPDATE_RECORD);
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);

    }
 

    public function GetScreenset_detail($position_id)
    {
        if(!empty($position_id))
        {
            $result = $this->art->GetScreenset_detail($position_id);
            if(count($result)>0)
            {
                $color_array= array();
                $allcolors = $this->common->getAllColorData();
                $getColors = $this->common->GetTableRecords('artjob_screencolors',array('screen_id' => $result[0]->id),array(),'head_location','asc');

                foreach ($allcolors as $key => $value) 
                {
                    $color_array[$value->id]= $value->name;
                    $allcolors[$key]->name = strtolower($value->name);
                }
                if(count($getColors)>0)
                {
                    foreach ($getColors as $value) 
                    {
                        $value->color_display_name = $color_array[$value->color_name];
                    }
                }
            
            }
            $response = array('success' => 1, 'message' => GET_RECORDS,'records'=>$result,'getColors'=>$getColors,'allcolors'=>$allcolors);
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }

    public function GetscreenColor($screen_id)
    {
        if(!empty($screen_id))
        {
            $result = $this->art->GetscreenColor($screen_id);
            $allcolors = array();
            if(count($result)>0)
            {
                $color_array= array();
                $allcolors = $this->common->getAllColorData();
                foreach ($allcolors as $key => $value) 
                {
                    $color_array[$value->id]= $value->name;
                    $allcolors[$key]->name = strtolower($value->name);
                }
               
                foreach ($result as $value) 
                {
                    if(!empty($value->color_name))
                    {
                        $value->color_name = $color_array[$value->color_name];
                    }
                    $value->mokup_image_url = (!empty($value->mokup_image))?UPLOAD_PATH.$value->company_id.'/art/'.$value->order_id."/".$value->mokup_image:'';
                    $value->mokup_logo_url = (!empty($value->mokup_logo))?UPLOAD_PATH.$value->company_id.'/art/'.$value->order_id."/".$value->mokup_logo:'';

                    if(!empty($value->thread_color))
                    {
                        $value->thread_display = $color_array[$value->thread_color];
                    }
                }
                $response = array('success' => 1, 'message' => GET_RECORDS,'records'=>$result,'allcolors'=>$allcolors);            
            }
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS);
            }
            
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function UpdateColorScreen()
    {
        $post = Input::all();

        //echo "<pre>"; print_r($post); echo "</pre>"; die;

        if(!empty($post['id']))
        {
            $result = $this->art->UpdateColorScreen($post);
            $response = array('success' => 1, 'message' => UPDATE_RECORD);
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);        
    }
    public function getScreenSizes($company_id)
    {
        if($company_id)
        {
            $result = $this->art->getScreenSizes($company_id);
            if(count($result)>0)
            {
                $response = array('success' => 1, 'message' => GET_RECORDS,'records'=>$result);
            }
            else 
            {
                $response = array('success' => 0, 'message' => NO_RECORDS);
            }
        }
        else 
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
        }
        return  response()->json(["data" => $response]);
    }
    public function change_sortcolor()
    {
        $post = Input::all();
        if(count($post)>0)
        {
             $result = $this->art->change_sortcolor($post);
        }
    }
    public function change_sortscreen()
    {
         $post = Input::all();
        if(count($post)>0)
        {
             $result = $this->art->change_sortscreen($post);
        }
    }

       /**
   * Save Color size.
   * @return json data
    */
    public function ArtApprovalPDF()
    {

        $screenArray= json_decode($_POST['art']);
        
        if(count($screenArray)>0)
        {
            $pdf_data = $this->art->getArtApprovalPDFdata($screenArray->order_id,$screenArray->company_id);
            if(!empty($pdf_data[0]))
            {
                //echo "<pre>"; print_r($pdf_data); echo "</pre>"; die;
                $file_path =  FILEUPLOAD.$screenArray->company_id."/art/".$screenArray->order_id;
               
                if (!file_exists($file_path)) { mkdir($file_path, 0777, true); } 
                else { exec("chmod $file_path 0777"); }
                
                PDF::AddPage('P','A4');
                PDF::writeHTML(view('pdf.screenset',array('data'=>$pdf_data,'company'=>$pdf_data[0][0]))->render());
           
                $pdf_url = "ScreenApproval-".$screenArray->order_id.".pdf"; 
                $filename = $file_path."/". $pdf_url;
                PDF::Output($filename);

                if(!empty($screenArray->mail) && $screenArray->mail=='1' && !empty($pdf_data[0][0]->billing_email))
                {
                    Mail::send('emails.artapproval', ['email'=>$pdf_data[0][0]->billing_email], function($message) use ($pdf_data,$filename)
                    {
                         $message->to($pdf_data[0][0]->billing_email)->subject('Art Approval for the order '.$pdf_data[0][0]->order_name);
                         $message->attach($filename);
                    });
                }

                //return Response::download($filename);

            }
            else
            {
                $response = array('success' => 0, 'message' => NO_RECORDS);
                return  response()->json(["data" => $response]);
            }
        }
        else
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
            return  response()->json(["data" => $response]);
        }

    }
    public function PressInstructionPDF()
    {

        $screenArray= json_decode($_POST['art']);
        
        if(count($screenArray)>0)
        {
            $pdf_data = $this->art->getPressInstructionPDFdata($screenArray->screen_id,$screenArray->company_id);
            //echo "<pre>"; print_r($pdf_data); echo "</pre>"; die;
            if(!empty($pdf_data['size']))
            {
                
                $file_path =  FILEUPLOAD.$screenArray->company_id."/art/".$screenArray->order_id;
               
                if (!file_exists($file_path)) { mkdir($file_path, 0777, true); } 
                else { exec("chmod $file_path 0777"); }
                
                PDF::AddPage('P','A4');
                PDF::writeHTML(view('pdf.artpress',array('color'=>$pdf_data['color'],'size'=>$pdf_data['size']))->render());
           
                $pdf_url = "PresInstruction-".$screenArray->screen_id.".pdf"; 
                $filename = $file_path."/". $pdf_url;
                PDF::Output($filename, 'F');
                return Response::download($filename);
            }
            else
            {
                $response = array('success' => 0, 'message' => "Error, No Size selected.");
                return  response()->json(["data" => $response]);
            }
        }
        else
        {
            $response = array('success' => 0, 'message' => MISSING_PARAMS);
            return  response()->json(["data" => $response]);
        }

    }
    
}



