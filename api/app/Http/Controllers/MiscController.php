<?php

namespace App\Http\Controllers;

require_once(app_path() . '/constants.php');

use App\Misc;
use Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use DB;
use Image;
use Request;

class MiscController extends Controller {  

/**
* Create a new controller instance.      
* @return void
*/
    public function __construct(Misc $misc) {

        parent::__construct();
        $this->misc = $misc;
       
    }

/** 
 * @SWG\Definition(
 *      definition="miscSave",
 *      type="object",
 *      required={"value", "id"},
 *      @SWG\Property(
 *          property="id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="value",
 *          type="string"
 *      )
 * )
 */

 /**
 * @SWG\Post(
 *  path = "/api/public/admin/miscSave",
 *  summary = "Misc save data",
 *  tags={"Misc"},
 *  description = "Misc save data",
 *  @SWG\Parameter(
 *     in="body",
 *     name="body",
 *     description="Misc save data",
 *     required=true,
 *     @SWG\Schema(ref="#/definitions/miscSave")
 *  ),
 *      @SWG\Parameter(
*          description="Authorization token",
*          type="string",
*          name="Authorization",
*          in="header",
*          required=true
*      ),
*      @SWG\Parameter(
*          description="Authorization User Id",
*          type="integer",
*          name="AuthUserId",
*          in="header",
*          required=true
*      ),
 *  @SWG\Response(response=200, description="Misc save data"),
 *  @SWG\Response(response="default", description="Misc save data"),
 * )
 */



    public function miscSave() {

          $data = Input::all();
          $result = $this->misc->miscSave($data);
         
          if (count($result) > 0) {
            $response = array('success' => 1, 'message' => UPDATE_RECORD,'records' => $result);
        } 
        
        return response()->json(["data" => $response]);

    }





}
