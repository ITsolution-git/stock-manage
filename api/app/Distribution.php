<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Distribution extends Model {

	public function getAllDustributionProducts($order_id)
	{
		$listArr = ['*'];
		$where = ['po.order_id' => $order_id];

		$result = DB::table('purchase_order as po')
					->JOIN('purchase_order_line as pol','pol.po_id','=','po.po_id')
					->JOIN('purchase_detail as pd','pol.purchase_detail','=','pd.id')
					->JOIN('products as p','p.id','=','pd.product_id')
					->select($listArr)
					->where($where)
					->get();

		print_r($result);exit;
	}
}
?>