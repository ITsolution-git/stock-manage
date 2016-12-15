<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Distribution extends Model {

	public function getAllDustributionProducts($order_id)
	{
		$listArr = ['p.name','p.id as product_id',DB::raw('SUM(pas.distributed_qnty) as distributed'),'pd.is_distribute','pd.design_product_id'];
		$where = ['po.order_id' => $order_id,'po.complete' => '1'];

		$result = DB::table('purchase_order as po')
					->Join('purchase_order_line as pol','pol.po_id','=','po.po_id')
					->Join('purchase_detail as pd','pol.purchase_detail','=','pd.id')
					->leftJoin('product_address_size_mapping as pas','pol.purchase_detail','=','pas.purchase_detail_id')
					->Join('products as p','p.id','=','pd.product_id')
					->select($listArr)
					->where($where)
					->GroupBy('p.id')
					->get();

		return $result;
	}

	public function getDistSizeByProduct($product_id,$design_product_id)
	{
		$listArr = ['pd.id','pd.size','pd.qnty as total_qnty','pd.distributed_qnty',DB::raw('SUM(pol.qnty_purchased - pol.short) as qnty_purchased'),'pd.remaining_qnty','pas.product_address_id'];
		$where = ['pd.product_id' => $product_id];

		$result = DB::table('purchase_detail as pd')
					->leftJoin('purchase_order_line as pol','pol.purchase_detail','=','pd.id')
					->leftJoin('product_address_size_mapping as pas','pol.purchase_detail','=','pas.purchase_detail_id')
					->select($listArr)
					->where('pd.product_id','=',$product_id)
					->where('pd.design_product_id','=',$design_product_id)
					->where('pol.qnty_purchased','>','0')
					->GroupBy('pd.id')
					->get();

		return $result;
	}

	public function getDistAddress($data)
	{
		$result = DB::table('order_shipping_address_mapping as oa')
					->leftJoin('client_distaddress as cd','oa.address_id','=','cd.id')
					->select('cd.*','oa.id as order_adress_id','oa.shipping_type_id')
					->where('oa.order_id','=',$data['id'])
					->where('is_deleted','=',1);
					if(isset($data['search']))
                    {
                      $search = $data['search'];
                      $result = $result->Where(function($query) use($search)
                      {
                          $query->orWhere('description', 'LIKE', '%'.$search.'%')
                                ->orWhere('address', 'LIKE', '%'.$search.'%')
                                ->orWhere('address2', 'LIKE', '%'.$search.'%')
                                ->orWhere('attn', 'LIKE', '%'.$search.'%')
                                ->orWhere('city', 'LIKE', '%'.$search.'%')
                                ->orWhere('zipcode', 'LIKE', '%'.$search.'%')
                                ->orWhere('state', 'LIKE', '%'.$search.'%')
                                ->orWhere('country', 'LIKE', '%'.$search.'%');
                      });
                    }
					$result = $result->get();

		return $result;
	}

	public function getProductByAddress($id)
	{
		$listArr = ['pd.id','pd.size','pas.distributed_qnty','pd.remaining_qnty','pas.product_address_id',DB::raw('SUM(pol.qnty_purchased - pol.short) as qnty_purchased')];

		$result = DB::table('purchase_detail as pd')
					->leftJoin('purchase_order_line as pol','pol.purchase_detail','=','pd.id')
					->leftJoin('product_address_size_mapping as pas','pd.id','=','pas.purchase_detail_id')
					->select($listArr)
					->where('pas.product_address_id','=',$id)
					->GroupBy('pd.id')
					->get();

		return $result;
	}

	public function getSingleSizeTotal($data)
	{
		$listArr = [DB::raw('SUM(pas.distributed_qnty) as distributed_qnty'),'pol.qnty_purchased'];

		$result = DB::table('purchase_detail as pd')
					->leftJoin('product_address_size_mapping as pas','pd.id','=','pas.purchase_detail_id')
					->leftJoin('purchase_order_line as pol','pol.purchase_detail','=','pd.id')
					->select($listArr)
					->where('pd.id','=',$data['id'])
//					->where('pas.product_address_id','!=',$data['product_address_id'])
					->get();

		return $result;
	}

	public function getTotalAllocated($order_id,$product_id)
	{
		$listArr = [DB::raw('SUM(pol.qnty_purchased - pol.short) as total')];
		$where = ['po.order_id' => $order_id,'po.complete' => '1','pd.product_id' => $product_id];

		$result = DB::table('purchase_order as po')
					->Join('purchase_order_line as pol','pol.po_id','=','po.po_id')
					->Join('purchase_detail as pd','pol.purchase_detail','=','pd.id')
					->Join('products as p','p.id','=','pd.product_id')
					->select($listArr)
					->where($where)
					->GroupBy('pd.product_id')
					->get();

		return $result[0]->total;
	}

	public function getTotalRecieved($order_id)
	{
		$total_assigned = DB::table('purchase_order as po')
                        ->leftJoin('purchase_order_line as pol','pol.po_id','=','po.po_id')
                        ->select(DB::raw('SUM(pol.qnty_purchased - pol.short) as total'))
                        ->where('po.order_id','=',$order_id)
                        ->get();
                
        return $total_assigned[0]->total;
	}

	public function getTotalDistributed($order_id)
	{
		$total_distributed = DB::table('shipping as s')
                            ->leftJoin('product_address_mapping as pam','s.id','=','pam.shipping_id')
                            ->leftJoin('product_address_size_mapping as pas','pam.id','=','pas.product_address_id')
                            ->select(DB::raw('SUM(pas.distributed_qnty) as distributed'))
                            ->where('s.order_id','=',$order_id)
                            ->get();
                
       return $total_distributed[0]->distributed;
	}
}	
?>