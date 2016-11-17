<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Common;
use DateTime;

class Labor extends Model {

  public function __construct(Common $common) 
  {
      $this->common = $common;
  }
	
	public function laborList($post)
    {

        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $result = DB::table('labor as l')
                    ->select('l.*')
                    ->where('l.is_delete','=','1')
                    ->where('l.company_id','=',$post['company_id']);

                    if($search != '')               
                    {
                      $result = $result->Where(function($query) use($search)
                      {
                          $query->orWhere('l.shift_name', 'LIKE', '%'.$search.'%')
                                ->orWhere('l.total_shift_hours','LIKE', '%'.$search.'%');
                      });
                    }

                     if(isset($post['filter']['filter_days']) && $post['filter']['filter_days'] != 0)
                        {
                          $filter_day = $post['filter']['filter_days'];

                          $result = $result->whereRaw('FIND_IN_SET('.$filter_day.',l.apply_days)');
                        }


                 $result = $result->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                 ->skip($post['start'])
                 ->take($post['range'])
                 ->get();
        
       /* //echo "<pre>"; print_r($result); echo "</pre>"; die;
        if(count($result)>0)
        {
            foreach ($result as $key=>$value) 
            {
                $value->note_date = ($value->note_date=='0000-00-00' || empty($value->note_date))?date("m/d/Y"):date('m/d/Y',strtotime($value->note_date));
                $value->artapproval_display = ($value->artapproval_display=='0')? false: true;
            }
        }*/
        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
        $returnData = array();
        $returnData['allData'] = $result;
        $returnData['count'] = $count[0]->Totalcount;       
        //echo "<pre>"; print_r($result); die();
        return $returnData;
    }

    /**
* Labor Detail           
* @access public laborDetail
* @param  int $laborId and $clientId
* @return array $combine_array
*/  

    public function laborDetail($data) {

      
        $whereConditions = ['labor.is_delete' => "1",'labor.id' => $data['id']];
        
        $listArray = ['labor.*'];

        $laborDetailData = DB::table('labor as labor')
                        
                         ->select($listArray)
                         ->where($whereConditions)
                         ->get();

        $combine_array = array();
        $combine_array['labor'] = $laborDetailData;
        return $combine_array;
    }
 }   
