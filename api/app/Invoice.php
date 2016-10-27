<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Common;
use DateTime;

class Invoice extends Model {

	public function __construct(Common $common) 
    {
        $this->common = $common;
    }
    public function listInvoice($post)
    {
        $search = '';
        if(isset($post['filter']['name'])) {
            $search = $post['filter']['name'];
        }

        $this->common->getDisplayNumber('invoice',$post['company_id'],'company_id','id','yes');

        $listArray = [DB::raw('SQL_CALC_FOUND_ROWS o.id as order_id,o.display_number,i.id,i.display_number as invoice_display_number,i.qb_id,o.grand_total,o.in_hands_by,i.created_date,misc_type.value as approval,o.approval_id')];

        $invoiceData = DB::table('invoice as i')
                        ->leftJoin('orders as o', 'o.id', '=', 'i.order_id')
                        ->leftJoin('misc_type as misc_type','o.approval_id','=',DB::raw("misc_type.id AND misc_type.company_id = ".$post['company_id']))
                        ->select($listArray)
                        ->where('o.company_id', '=', $post['company_id']);

                        if($search != '')
                        {
                          $invoiceData = $invoiceData->Where(function($query) use($search)
                          {
                              $query->orWhere('o.display_number', 'LIKE', '%'.$search.'%')
                                    ->orWhere('i.created_date', 'LIKE', '%'.$search.'%')
                                    ->orWhere('o.grand_total', 'LIKE', '%'.$search.'%')
                                    ->orWhere('misc_type.value', 'LIKE', '%'.$search.'%')
                                    ->orWhere('o.in_hands_by', 'LIKE', '%'.$search.'%');
                          });
                        }
                        $invoiceData = $invoiceData->orderBy($post['sorts']['sortBy'], $post['sorts']['sortOrder'])
                        ->GroupBy('o.id')
                        ->skip($post['start'])
                        ->take($post['range'])
                        ->get();

        $count  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );

        $returnData = array();
        $returnData['allData'] = $invoiceData;
        $returnData['count'] = $count[0]->Totalcount;
        return $returnData;
    }

    public function getShippingByOrder($order_id)
    {
        
    }

    public function getInvoiceTotal($order_id){
        $retArray = DB::table('payment_history as p')
            ->select(DB::raw('SUM(p.payment_amount) as totalAmount'), 'o.grand_total')
            ->leftJoin('orders as o','o.id','=','p.order_id')
            ->where('p.order_id','=',$order_id)
            ->where('p.is_delete','=',1)
            ->get();
        return $retArray;
    }

    public function getInvoiceClient($order_id){
        $retArray = DB::table('orders as o')
            ->select('c.client_company', 'c.client_id', 'c.billing_email')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->where('o.id','=',$order_id)
            ->where('o.is_delete','=','1')
            ->get();
        return $retArray;
    }

    public function getInvoiceHistory($post,$invoice_id){
        $retArray = DB::table('payment_history as ph')
            ->select('ph.payment_id', 'ph.payment_amount', 'ph.payment_date', 'ph.payment_method')
            ->leftJoin('orders as o','o.id','=','ph.order_id')
            ->leftJoin('invoice as i','i.order_id','=','o.id')
            ->where('i.id','=',$invoice_id)
            ->where('ph.is_delete','=',1)
            ->get();

        return $retArray;
    }

    public function getTransactionDetail($payment_id){
        $retArray = DB::table('payment_history')
            ->select('payment_card', 'payment_amount', 'authorized_TransId', 'payment_date', 'authorized_AuthCode')
            ->where('payment_id','=',$payment_id)
            ->where('is_delete','=','1')
            ->get();
        return $retArray;
    }

    public function getLinkToPayDetail($token){
        $retArray = DB::table('link_to_pay as lp')
            ->select('lp.session_link', 'lp.ltp_id', 'lp.created_date', 'o.balance_due', 'lp.order_id', 'u.id as company_id', 'i.id as invoice_id', 'i.payment_terms')
            ->leftJoin('orders as o','o.id','=',"lp.order_id")
            ->leftJoin('invoice as i','i.order_id','=',"o.id")
            ->leftJoin('client as c','c.client_id','=',"o.client_id")
            ->leftJoin('users as u','u.id','=',"c.company_id")
            ->where('lp.session_link','=',$token)
            ->where('lp.payment_flag','=','0')
            ->get();
        return $retArray;
    }

    public function getOrderSalesAccount($order_id){
        $retArray = DB::table('orders as o')
            ->select('s.sales_name', 's.sales_email' , 's.sales_phone', 's.sales_web', 'u.name' , 'u.email' , 'u.phone')
            ->leftJoin('sales as s','s.id','=', 'o.sales_id')
            ->leftJoin('users as u','u.id','=', 'o.account_manager_id')
            ->where('o.id','=',$order_id)
            ->get();
        return $retArray;
    }

    public function getInvoiceCards($post,$invoice_id){

        $retArray = DB::table('invoice as i')
            ->select('cppd.payment_profile_id', 'cppd.card_number', 'cppd.expiration')
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client_payment_profiles as cpp','cpp.client_id','=','o.client_id')
            ->leftJoin('client_payment_profiles_detail as cppd','cppd.cpp_id','=','cpp.cpp_id')
            ->where('i.id','=',$invoice_id)
            ->get();

        return $retArray;
    }

    public function getSalesPersons($post){
        $client_id=$post['company_id'];

        $retArray = DB::table('sales as s')
            ->select('s.sales_name', 's.id as sales_id')
            ->leftJoin('users as u','u.id','=','s.company_id')
            //->leftJoin('orders as o','o.sales_id','=','s.id')
            //->leftJoin('client as c','c.client_id','=','o.client_id')
            //->leftJoin('users as u1','u1.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('s.sales_delete','=','1')
            ->get();

        return $retArray;
    }

    public function getNoQuickbook($post){
        $client_id=$post['company_id'];

        $retArray = DB::table('invoice as i')
            ->select(DB::raw('COUNT(i.id) as totalInvoice'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.parent_order_id','=',0)
            ->where('o.is_delete','=','1')
            ->where('i.qb_id','=',0)
            ->get();

        return $retArray;
    }

    public function getSalesClosed($post){
        $client_id=$post['company_id'];

        $retArray = DB::table('invoice as i')
        ->select(DB::raw('SUM(o.grand_total) as totalSales'))
        ->leftJoin('orders as o','o.id','=','i.order_id')
        ->leftJoin('client as c','c.client_id','=','o.client_id')
        ->leftJoin('users as u','u.id','=','c.company_id')
        ->where('u.id','=',$client_id)
        ->where('o.parent_order_id','=',0)
        ->where('o.is_delete','=','1');
        if(isset($post['sales_id']) && $post['sales_id']!=0){
            $sales_id=$post['sales_id'];
            $retArray = $retArray->where('o.sales_id','=',$sales_id);
        }
        $retArray = $retArray->get();

        return $retArray;
    }

    public function getUnpaid($post){
        $client_id=$post['company_id'];

        $retArray = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.balance_due) as totalUnpaid'), DB::raw('COUNT(i.id) as totalInvoice') )
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.is_paid','=','0')
            ->where('o.parent_order_id','=',0)
            ->where('o.is_delete','=','1')
            ->where('o.grand_total','>','o.total_payments')
            ->get();

        return $retArray;
    }

    public function getAverageOrders($post){
        $client_id=$post['company_id'];

        // Fetching average amount of order per invoiced
        $retArray = DB::table('invoice as i')
        ->select(DB::raw('AVG(o.grand_total) as avgOrderAmount'))
        ->leftJoin('orders as o','o.id','=','i.order_id')
        ->leftJoin('client as c','c.client_id','=','o.client_id')
        ->leftJoin('users as u','u.id','=','c.company_id')
        ->where('u.id','=',$client_id)
        ->where('o.parent_order_id','=',0)
        ->where('o.is_delete','=','1');
        if(isset($post['sales_id']) && $post['sales_id']!=0){
            $sales_id=$post['sales_id'];
            $retArray = $retArray->where('o.sales_id','=',$sales_id);
        }
        $retArray = $retArray->get();

        if(empty($retArray))
        {
           $response = array(
                'success' => 0, 
                'message' => NO_RECORDS
            ); 
           return response()->json(["data" => $response]);
        }
        $retArray[0]->avgOrderAmount=round($retArray[0]->avgOrderAmount, 0);
        //$tempFigure=explode(".", $retArray[0]->avgOrderAmount);
        //$retArray[0]->avgOrderAmount=$tempFigure;

        // Fetching average number of items per invoiced
        $order_design_data = DB::table('invoice as i')
        ->select('od.id as design_id', 'o.id as order_id')
        ->leftJoin('orders as o','o.id','=','i.order_id')
        ->leftJoin('order_design as od','od.order_id','=','o.id')
        ->leftJoin('client as c','c.client_id','=','o.client_id')
        ->leftJoin('users as u','u.id','=','c.company_id')
        ->where('u.id','=',$client_id)
        ->where('o.parent_order_id','=',0)
        ->where('o.is_delete','=','1')
        ->where('od.status','=','1')
        ->where('od.is_delete','=','1');

        if(isset($post['sales_id']) && $post['sales_id']!=0){
            $sales_id=$post['sales_id'];
            $order_design_data = $order_design_data->where('o.sales_id','=',$sales_id);
        }

        $order_design_data = $order_design_data->get();

        if(!empty($order_design_data))
        {
            $size_data = array();
            $order_design = array();
            $orderIDs = array();
            $total_unit = 0;

            foreach ($order_design_data as $design)
            {
                $size_data = DB::table('purchase_detail')
                ->select('*')
                ->where('design_id','=',$design->design_id)
                ->where('is_delete','=','1')
                ->get();

                $total_qnty = 0;
                foreach ($size_data as $size)
                {
                    $total_qnty += $size->qnty;
                }
                $total_unit += $total_qnty;
                $design->size_data = $size_data;
                $design->total_qnty = $total_qnty;
                $orderIDs[]=$design->order_id;
                //$order_design['all_design'][] = $design;
            }

            if($total_unit > 0)
            {
                $order_design['total_unit'] = $total_unit;
                $countOrders = count(array_unique($orderIDs));
                $retArray[0]->avgOrderItems=round($order_design['total_unit']/$countOrders,0);
                //$tempAvg=explode(".", $retArray[0]->avgOrderItems);
                //$retArray[0]->avgOrderItems=$tempAvg;
            }
        }

        return $retArray;
    }

    public function getLatestOrders($post){

        $client_id=$post['company_id'];

        $retArray = DB::table('orders as o')
            ->select('o.display_number as order_id', 'c.client_company', 'o.grand_total')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.parent_order_id','=',0)
            ->where('o.is_delete','=','1')
            ->orderBy('o.created_date','desc')
            ->take(5)
            ->get();

        return $retArray;
    }

    public function getEstimates($post,$estimate_id){
        $client_id=$post['company_id'];

        $retArray = DB::table('orders as o')
        ->select(DB::raw('SUM(o.grand_total) as totalEstimated'), DB::raw('COUNT(o.id) as totalInvoice') )
        ->leftJoin('client as c','c.client_id','=','o.client_id')
        ->leftJoin('users as u','u.id','=','c.company_id')
        ->where('u.id','=',$client_id)
        ->where('o.parent_order_id','=',0)
        ->where('o.is_delete','=','1');

        if( (isset($post['sales_id']) && $post['sales_id']!=0) || (isset($post['duration']) && $post['duration']!=0) ){
            $sales_id=$post['sales_id'];
            if(isset($post['sales_id']) && $post['sales_id']!=0){
                $retArray = $retArray->where('o.sales_id','=',$sales_id);
            }

            if(isset($post['duration']) && $post['duration']!=0){
                if($post['duration']=='1'){
                    $retArray = $retArray->where(DB::raw('o.created_date'), '=', DB::raw('CURDATE()'));
                }else if($post['duration']=='2'){
                    $retArray = $retArray->where(DB::raw('WEEK(o.created_date)'), '=', DB::raw('WEEK(CURDATE())-1'));
                }else if($post['duration']=='3'){
                    $retArray = $retArray->where(DB::raw('MONTH(o.created_date)'), '=', DB::raw('MONTH(CURDATE())-1'));
                }else if($post['duration']=='4'){
                    $retArray = $retArray->where(DB::raw('YEAR(o.created_date)'), '=', DB::raw('YEAR(CURDATE())-1'));
                }
            }
        }
        $retArray = $retArray->where('o.approval_id','=',$estimate_id)
        ->get();

        return $retArray;
    }

    public function getComparison($post,$year2){

        $year1=$post['comparisonPeriod1'];
        $client_id=$post['company_id'];

        $retArray = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.grand_total) as totalEstimated'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.parent_order_id','=',0)
            ->where('o.is_delete','=','1')
            //->where(YEAR('i.created_date'),'=',YEAR(CURDATE()))
            ->where(DB::raw('YEAR(i.created_date)'), '=', DB::raw('YEAR(CURDATE())'))
            //->whereRaw('YEAR(i.created_date)' <= 'YEAR(CURDATE()')
            ->get();

        $amountCurrent=round($retArray[0]->totalEstimated, 0);
        //$tempFigure=explode(".", $amountCurrent);
        //$retArray[0]->totalEstimated=$tempFigure;
        $retArray[0]->totalEstimated=$amountCurrent;
        $retArray[0]->year2=$year2;

        $retArrayPrevious = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.grand_total) as totalEstimatedPrevious'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.parent_order_id','=',0)
            ->where('o.is_delete','=','1')
            ->where(DB::raw('YEAR(i.created_date)'), '=',$year2)
            ->get();

        if(!empty($retArrayPrevious)){
            $amountPrevious=round($retArrayPrevious[0]->totalEstimatedPrevious, 0);
            $retArray[0]->totalEstimatedPrevious=$amountPrevious;
            if($amountPrevious!='0.00'){
                $retArray[0]->percentDifference = round((($amountCurrent*100) / $amountPrevious),0)-100;    
            }else{
                $retArray[0]->percentDifference = 0;
            }
        }else{
            $retArray[0]->totalEstimatedPrevious = 0;
            $retArray[0]->percentDifference = 0;
        }

        return $retArray;
    }

    public function getUnshipped($post){
        $client_id=$post['company_id'];

        /*$retArray = DB::table('invoice as i')
            ->select(DB::raw('SUM(o.balance_due) as totalUnshipped'), DB::raw('COUNT(i.id) as totalInvoice') )
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('shipping as s','s.order_id','=','o.id')   
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.parent_order_id','=',0)
            ->where('o.is_delete','=','1')
            ->where('s.shipping_status','!=','3')
            ->get();*/

        $retArrayTemp = DB::table('invoice as i')
            ->select(DB::raw('DISTINCT(o.id) as totalInvoice'))
            ->leftJoin('orders as o','o.id','=','i.order_id')
            ->leftJoin('shipping as s','s.order_id','=','o.id')   
            ->leftJoin('client as c','c.client_id','=','o.client_id')
            ->leftJoin('users as u','u.id','=','c.company_id')
            ->where('u.id','=',$client_id)
            ->where('o.parent_order_id','=',0)
            ->where('o.is_delete','=','1')
            ->where('s.shipping_status','!=','3')
            ->get();
        $array = json_decode(json_encode($retArrayTemp), true);
        $arrayOrder=array();
        foreach ($array as $key => $value) {
            $arrayOrder[]=$value['totalInvoice'];
        }
        if(count($arrayOrder)>0){
            $retArray = DB::table('orders')
                    ->select(DB::raw('count(id) as totalInvoice'),DB::raw('sum(balance_due) as totalUnshipped'))
                    ->whereIn('id',$arrayOrder)
                    ->get();
        }else{
            $retArray[0] = (object) null;
            $retArray[0]->totalInvoice=0;
            $retArray[0]->totalUnshipped=0;
        }
       return $retArray;
    }

    public function getFullShipped($post){
        $client_id=$post['company_id'];

        $retArray = DB::table('invoice as i')
        ->select(DB::raw('COUNT(i.id) as totalShipped') )
        ->leftJoin('orders as o','o.id','=','i.order_id')
        ->leftJoin('shipping as s','s.order_id','=','o.id')   
        ->leftJoin('client as c','c.client_id','=','o.client_id')
        ->leftJoin('users as u','u.id','=','c.company_id')
        ->where('u.id','=',$client_id)
        ->where('o.parent_order_id','=',0)
        ->where('o.is_delete','=','1')
        ->where('o.date_shipped','>','s.fully_shipped');

        if((isset($post['duration']) && $post['duration']!=0)){
            if($post['duration']=='1'){
                $retArray = $retArray->where(DB::raw('i.created_date'), '=', DB::raw('CURDATE()'));
            }else if($post['duration']=='2'){
                $retArray = $retArray->where(DB::raw('WEEK(i.created_date)'), '=', DB::raw('WEEK(CURDATE())-1'));
            }else if($post['duration']=='3'){
                $retArray = $retArray->where(DB::raw('MONTH(i.created_date)'), '=', DB::raw('MONTH(CURDATE())-1'));
            }else if($post['duration']=='4'){
                $retArray = $retArray->where(DB::raw('YEAR(i.created_date)'), '=', DB::raw('YEAR(CURDATE())-1'));
            }
        }
        $retArray = $retArray->where('s.shipping_status','=','2')
        ->get();

        return $retArray;
    }

    public function getProduction($post,$production_id){
        $client_id=$post['company_id'];

        $retArray = DB::table('invoice as i')
        ->select(DB::raw('COUNT(i.id) as totalProduction'))
        ->leftJoin('orders as o','o.id','=','i.order_id')
        ->leftJoin('client as c','c.client_id','=','o.client_id')
        ->leftJoin('users as u','u.id','=','c.company_id')
        ->where('u.id','=',$client_id)
        ->where('o.parent_order_id','=',0)
        ->where('o.is_delete','=','1');

        if(isset($post['sales_id']) && $post['sales_id']!=0){
            $sales_id=$post['sales_id'];
            $retArray = $retArray->where('o.sales_id','=',$sales_id);
        }

        $retArray = $retArray->where('o.approval_id','=',$production_id)
        ->get();

        return $retArray;
    }
}