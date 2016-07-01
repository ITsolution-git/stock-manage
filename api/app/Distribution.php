<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Distribution extends Model {

	public function getAllDustributionProducts($order_id)
	{
		$listArr = ['p.name','p.id as product_id',DB::raw('SUM(pol.qnty_purchased) as total'),DB::raw('SUM(pd.distributed_qnty) as distributed')];
		$where = ['po.order_id' => $order_id, 'pd.is_distribute' => '0', 'po.complete' => '1'];

		$result = DB::table('purchase_order as po')
					->leftJoin('purchase_order_line as pol','pol.po_id','=','po.po_id')
					->leftJoin('purchase_detail as pd','pol.purchase_detail','=','pd.id')
					->leftJoin('products as p','p.id','=','pd.product_id')
					->select($listArr)
					->where($where)
					->GroupBy('pd.product_id')
					->get();

		return $result;
	}

	public function getDistSizeByProduct($product_id)
	{
		$listArr = ['pd.id','pd.size','pd.distributed_qnty','pol.qnty_purchased'];
		$where = ['pd.product_id' => $product_id, 'pd.is_distribute' => '0'];

		$result = DB::table('purchase_detail as pd')
					->leftJoin('purchase_order_line as pol','pol.purchase_detail','=','pd.id')
					->select($listArr)
					->where('pd.product_id','=',$product_id)
					->where('pd.is_distribute','=','0')
					->where('pol.qnty_purchased','>','0')
					->get();

		return $result;
	}
}
?>