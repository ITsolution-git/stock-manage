<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Distribution extends Model {

	public function getAllDustributionProducts($order_id)
	{
		$listArr = ['p.name','p.id as product_id',DB::raw('SUM(pol.qnty_purchased) as total'),DB::raw('SUM(pd.distributed_qnty) as distributed'),'pd.is_distribute'];
		$where = ['po.order_id' => $order_id,'po.complete' => '1'];

		$result = DB::table('purchase_order as po')
					->Join('purchase_order_line as pol','pol.po_id','=','po.po_id')
					->Join('purchase_detail as pd','pol.purchase_detail','=','pd.id')
					->Join('products as p','p.id','=','pd.product_id')
					->select($listArr)
					->where($where)
					->GroupBy('pd.product_id')
					->get();

		return $result;
	}

	public function getDistSizeByProduct($product_id)
	{
		$listArr = ['pd.id','pd.size','pd.distributed_qnty','pol.qnty_purchased'];
		$where = ['pd.product_id' => $product_id];

		$result = DB::table('purchase_detail as pd')
					->leftJoin('purchase_order_line as pol','pol.purchase_detail','=','pd.id')
					->select($listArr)
					->where('pd.product_id','=',$product_id)
					->where('pol.qnty_purchased','>','0')
					->get();

		return $result;
	}

	public function getDistAddress($data)
	{
		$result = DB::table('client_distaddress')
					->where('client_id','=',$data['client_id']);
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
		$listArr = ['pd.id','pd.size','pas.distributed_qnty','pd.remaining_qnty'];

		$result = DB::table('purchase_detail as pd')
					->leftJoin('product_address_size_mapping as pas','pd.id','=','pas.purchase_detail_id')
					->select($listArr)
					->where('pas.product_address_id','=',$id)
					->get();

		return $result;
	}
}
?>