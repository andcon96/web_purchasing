<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class SalesMenuReport implements FromQuery, WithHeadings, ShouldAutoSize
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    function __construct($c_item,$c_filter,$c_item2,$c_filter2,$c_tipemenu) {
            $this->item = $c_filter;
            $this->filter = $c_item;
            $this->filter2 = $c_filter2;
            $this->item2 = $c_item2;
            $this->tipemenu= $c_tipemenu;
    }

    public function query()
    {   
        $filter1 = $this->filter;
        $filter2 = $this->filter2;
        $search1 = $this->item;
        $search2 = $this->item2;
        $tipemenu = $this->tipemenu;

        $yearnow = date('Y');
        $yearold = date('Y') - 1;


        $desc2 = '';

        if($filter1 == 'item_code'){
            $desc1 = 'itemdesc';
		}else if($filter1 == 'cust_code'){
			$desc1 = 'cust_desc';
		}else if($filter1 == 'cust_region'){
			$desc1 = 'cust_region';
		}else if($filter1 == 'idgroup'){
			$desc1 = 'group_name';
		}else if($filter1 == 'idsubgroup'){
			$desc1 = 'description';
        }else if($filter1 == 'brands.brand_code'){
            $desc1 = 'brand_desc';
        }

        if($filter2 == 'item_code'){
            $desc2 = 'itemdesc';
		}else if($filter2 == 'cust_code'){
			$desc2 = 'cust_desc';
		}else if($filter2 == 'cust_region'){
			$desc2 = 'cust_region';
		}else if($filter2 == 'idgroup'){
			$desc2 = 'group_name';
		}else if($filter2 == 'idsubgroup'){
			$desc2 = 'description';
        }else if($filter1 == 'brands.brand_code'){
            $desc2 = 'brand_desc';
        }

        $query = 'transaksi_sum.id >= 1';
        $descript = '';

        if($filter1 != '' && $filter2 != ''){
            $descript = 'concat('.$desc1.'," - ",'.$desc2.')';
        }else{
            if($filter1 != ''){
                $descript = $desc1;
            }else{
                $descript = $desc2;
            }
        }


        if($filter1 != '' && $search1 != ''){
            $query .= ' AND '.$filter1.' = "'.$search1.'"';
        }

        if($filter2 != '' && $search2 != ''){
            $query .= ' AND '.$filter2.' = "'.$search2.'"';
        }

        

		if($tipemenu == '1'){
			// Table Bulan
			if($desc2 == ''){
				return  DB::table("transaksi_sum")
							->join('customers','transaksi_sum.cust_code','=','customers.cust_id')
							->join('items','items.itemcode','=','transaksi_sum.item_code')
							->join('brands','items.brand_code','=','brands.brand_code')
							->join('groups','groups.id','=','items.idgroup')
							->join('sub_groups','sub_groups.id','=','items.idsubgroup')
							->selectRaw(''.$desc1.' as "id",
								sum(CASE WHEN month(date_trans) = 1 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "JanNewQty",
								sum(CASE WHEN month(date_trans) = 1 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "JanNew",
								sum(CASE WHEN month(date_trans) = 1 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "JanOldQty",
								sum(CASE WHEN month(date_trans) = 1 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "JanOld",
								sum(CASE WHEN month(date_trans) = 2 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "FebNewQty",
								sum(CASE WHEN month(date_trans) = 2 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "FebNew",
								sum(CASE WHEN month(date_trans) = 2 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "FebOldQty",
								sum(CASE WHEN month(date_trans) = 2 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "FebOld",
								sum(CASE WHEN month(date_trans) = 3 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "MarNewQty",
								sum(CASE WHEN month(date_trans) = 3 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "MarNew",
								sum(CASE WHEN month(date_trans) = 3 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "MarOldQty",
								sum(CASE WHEN month(date_trans) = 3 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "MarOld",
								sum(CASE WHEN month(date_trans) = 4 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "AprNewQty",
								sum(CASE WHEN month(date_trans) = 4 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "AprNew",
								sum(CASE WHEN month(date_trans) = 4 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "AprOldQty",
								sum(CASE WHEN month(date_trans) = 4 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "AprOld",
								sum(CASE WHEN month(date_trans) = 5 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "MayNewQty",
								sum(CASE WHEN month(date_trans) = 5 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "MayNew",
								sum(CASE WHEN month(date_trans) = 5 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "MayOldQty",
								sum(CASE WHEN month(date_trans) = 5 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "MayOld",
								sum(CASE WHEN month(date_trans) = 6 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "JunNewQty",
								sum(CASE WHEN month(date_trans) = 6 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "JunNew",
								sum(CASE WHEN month(date_trans) = 6 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "JunOldQty",
								sum(CASE WHEN month(date_trans) = 6 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "JunOld",
								sum(CASE WHEN month(date_trans) = 7 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "JulNewQty",
								sum(CASE WHEN month(date_trans) = 7 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "JulNew",
								sum(CASE WHEN month(date_trans) = 7 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "JulOldQty",
								sum(CASE WHEN month(date_trans) = 7 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "JulOld",
								sum(CASE WHEN month(date_trans) = 8 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "AugNewQty",
								sum(CASE WHEN month(date_trans) = 8 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "AugNew",
								sum(CASE WHEN month(date_trans) = 8 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "AugOldQty",
								sum(CASE WHEN month(date_trans) = 8 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "AugOld",
								sum(CASE WHEN month(date_trans) = 9 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "SepNewQty",
								sum(CASE WHEN month(date_trans) = 9 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "SepNew",
								sum(CASE WHEN month(date_trans) = 9 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "SepOldQty",
								sum(CASE WHEN month(date_trans) = 9 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "SepOld",
								sum(CASE WHEN month(date_trans) = 10 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "OktNewQty",
								sum(CASE WHEN month(date_trans) = 10 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "OktNew",
								sum(CASE WHEN month(date_trans) = 10 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "OktOldQty",
								sum(CASE WHEN month(date_trans) = 10 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "OktOld",
								sum(CASE WHEN month(date_trans) = 11 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "NovNewQty",
								sum(CASE WHEN month(date_trans) = 11 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "NovNew",
								sum(CASE WHEN month(date_trans) = 11 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "NovOldQty",
								sum(CASE WHEN month(date_trans) = 11 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "NovOld",
								sum(CASE WHEN month(date_trans) = 12 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "DecNewQty",
								sum(CASE WHEN month(date_trans) = 12 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "DecNew",
								sum(CASE WHEN month(date_trans) = 12 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "DecOldQty",
								sum(CASE WHEN month(date_trans) = 12 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "DecOld",
								sum(CASE WHEN year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "TotNew",
								sum(CASE WHEN year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "TotOld"
								')
							->whereRaw($query)
							->groupBy($filter1,$filter2)
							->orderBy($filter1);
			}else{
				return DB::table("transaksi_sum")
						->join('customers','transaksi_sum.cust_code','=','customers.cust_id')
						->join('items','items.itemcode','=','transaksi_sum.item_code')
						->join('groups','groups.id','=','items.idgroup')
						->join('sub_groups','sub_groups.id','=','items.idsubgroup')
						->join('brands','items.brand_code','=','brands.brand_code')
						->selectRaw(''.$desc1.', '.$desc2.',
								sum(CASE WHEN month(date_trans) = 1 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "JanNewQty",
								sum(CASE WHEN month(date_trans) = 1 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "JanNew",
								sum(CASE WHEN month(date_trans) = 1 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "JanOldQty",
								sum(CASE WHEN month(date_trans) = 1 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "JanOld",
								sum(CASE WHEN month(date_trans) = 2 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "FebNewQty",
								sum(CASE WHEN month(date_trans) = 2 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "FebNew",
								sum(CASE WHEN month(date_trans) = 2 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "FebOldQty",
								sum(CASE WHEN month(date_trans) = 2 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "FebOld",
								sum(CASE WHEN month(date_trans) = 3 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "MarNewQty",
								sum(CASE WHEN month(date_trans) = 3 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "MarNew",
								sum(CASE WHEN month(date_trans) = 3 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "MarOldQty",
								sum(CASE WHEN month(date_trans) = 3 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "MarOld",
								sum(CASE WHEN month(date_trans) = 4 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "AprNewQty",
								sum(CASE WHEN month(date_trans) = 4 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "AprNew",
								sum(CASE WHEN month(date_trans) = 4 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "AprOldQty",
								sum(CASE WHEN month(date_trans) = 4 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "AprOld",
								sum(CASE WHEN month(date_trans) = 5 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "MayNewQty",
								sum(CASE WHEN month(date_trans) = 5 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "MayNew",
								sum(CASE WHEN month(date_trans) = 5 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "MayOldQty",
								sum(CASE WHEN month(date_trans) = 5 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "MayOld",
								sum(CASE WHEN month(date_trans) = 6 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "JunNewQty",
								sum(CASE WHEN month(date_trans) = 6 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "JunNew",
								sum(CASE WHEN month(date_trans) = 6 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "JunOldQty",
								sum(CASE WHEN month(date_trans) = 6 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "JunOld",
								sum(CASE WHEN month(date_trans) = 7 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "JulNewQty",
								sum(CASE WHEN month(date_trans) = 7 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "JulNew",
								sum(CASE WHEN month(date_trans) = 7 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "JulOldQty",
								sum(CASE WHEN month(date_trans) = 7 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "JulOld",
								sum(CASE WHEN month(date_trans) = 8 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "AugNewQty",
								sum(CASE WHEN month(date_trans) = 8 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "AugNew",
								sum(CASE WHEN month(date_trans) = 8 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "AugOldQty",
								sum(CASE WHEN month(date_trans) = 8 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "AugOld",
								sum(CASE WHEN month(date_trans) = 9 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "SepNewQty",
								sum(CASE WHEN month(date_trans) = 9 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "SepNew",
								sum(CASE WHEN month(date_trans) = 9 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "SepOldQty",
								sum(CASE WHEN month(date_trans) = 9 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "SepOld",
								sum(CASE WHEN month(date_trans) = 10 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "OktNewQty",
								sum(CASE WHEN month(date_trans) = 10 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "OktNew",
								sum(CASE WHEN month(date_trans) = 10 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "OktOldQty",
								sum(CASE WHEN month(date_trans) = 10 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "OktOld",
								sum(CASE WHEN month(date_trans) = 11 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "NovNewQty",
								sum(CASE WHEN month(date_trans) = 11 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "NovNew",
								sum(CASE WHEN month(date_trans) = 11 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "NovOldQty",
								sum(CASE WHEN month(date_trans) = 11 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "NovOld",
								sum(CASE WHEN month(date_trans) = 12 AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "DecNewQty",
								sum(CASE WHEN month(date_trans) = 12 AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "DecNew",
								sum(CASE WHEN month(date_trans) = 12 AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "DecOldQty",
								sum(CASE WHEN month(date_trans) = 12 AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "DecOld",
								sum(CASE WHEN year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "TotNew",
								sum(CASE WHEN year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "TotOld"
								')
							->whereRaw($query)
							->groupBy($filter1,$filter2)
							->orderBy($filter1);
			}
		}else if($tipemenu == '2'){
			// Table Kuartil
			if($desc2 == ''){
				return DB::table("transaksi_sum")
						->join('customers','transaksi_sum.cust_code','=','customers.cust_id')
						->join('items','items.itemcode','=','transaksi_sum.item_code')
						->join('groups','groups.id','=','items.idgroup')
						->join('sub_groups','sub_groups.id','=','items.idsubgroup')
						->join('brands','items.brand_code','=','brands.brand_code')
						->selectRaw(''.$desc1.' as "id",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 3  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "Q1NewQty",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 3  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "Q1New",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 3  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "Q1OldQty",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 3  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "Q1Old",
								sum(CASE WHEN month(date_trans) >= 4 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "Q2NewQty",
								sum(CASE WHEN month(date_trans) >= 4 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "Q2New",
								sum(CASE WHEN month(date_trans) >= 4 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "Q2OldQty",
								sum(CASE WHEN month(date_trans) >= 4 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "Q2Old",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 9  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "Q3NewQty",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 9  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "Q3New",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 9  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "Q3OldQty",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 9  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "Q3Old",
								sum(CASE WHEN month(date_trans) >= 10 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "Q4NewQty",
								sum(CASE WHEN month(date_trans) >= 10 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "Q4New",
								sum(CASE WHEN month(date_trans) >= 10 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "Q4OldQty",
								sum(CASE WHEN month(date_trans) >= 10 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "Q4Old",
								sum(CASE WHEN year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "TotNew",
								sum(CASE WHEN year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "TotOld"
								')
							->whereRaw($query)
							->groupBy($filter1,$filter2)
						->orderBy($filter1);
			}else{
				return DB::table("transaksi_sum")
						->join('customers','transaksi_sum.cust_code','=','customers.cust_id')
						->join('items','items.itemcode','=','transaksi_sum.item_code')
						->join('groups','groups.id','=','items.idgroup')
						->join('sub_groups','sub_groups.id','=','items.idsubgroup')
						->join('brands','items.brand_code','=','brands.brand_code')
						->selectRaw(''.$desc1.' as "id", '.$desc2.' ,
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 3  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "Q1NewQty",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 3  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "Q1New",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 3  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "Q1OldQty",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 3  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "Q1Old",
								sum(CASE WHEN month(date_trans) >= 4 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "Q2NewQty",
								sum(CASE WHEN month(date_trans) >= 4 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "Q2New",
								sum(CASE WHEN month(date_trans) >= 4 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "Q2OldQty",
								sum(CASE WHEN month(date_trans) >= 4 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "Q2Old",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 9  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "Q3NewQty",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 9  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "Q3New",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 9  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "Q3OldQty",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 9  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "Q3Old",
								sum(CASE WHEN month(date_trans) >= 10 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "Q4NewQty",
								sum(CASE WHEN month(date_trans) >= 10 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "Q4New",
								sum(CASE WHEN month(date_trans) >= 10 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "Q4OldQty",
								sum(CASE WHEN month(date_trans) >= 10 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "Q4Old",
								sum(CASE WHEN year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "TotNew",
								sum(CASE WHEN year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "TotOld"
								')
							->whereRaw($query)
							->groupBy($filter1,$filter2)
						->orderBy($filter1);
			}
			
		}else if($tipemenu == '3'){
			// Table Semester
			if($desc2 != ''){
				return DB::table("transaksi_sum")
						->join('customers','transaksi_sum.cust_code','=','customers.cust_id')
						->join('items','items.itemcode','=','transaksi_sum.item_code')
						->join('groups','groups.id','=','items.idgroup')
						->join('sub_groups','sub_groups.id','=','items.idsubgroup')
						->join('brands','items.brand_code','=','brands.brand_code')
						->selectRaw(''.$desc1.' as "id",'.$desc2.',
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "S1NewQty",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "S1New",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "S1OldQty",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "S1Old",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "S2NewQty",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "S2New",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "S2OldQty",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "S2Old",
								sum(CASE WHEN year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "TotNew",
								sum(CASE WHEN year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "TotOld"
								')
							->whereRaw($query)
							->groupBy($filter1,$filter2)
							->orderBy($filter1);
			}else{
				return DB::table("transaksi_sum")
							->join('customers','transaksi_sum.cust_code','=','customers.cust_id')
							->join('items','items.itemcode','=','transaksi_sum.item_code')
							->join('groups','groups.id','=','items.idgroup')
							->join('sub_groups','sub_groups.id','=','items.idsubgroup')
							->join('brands','items.brand_code','=','brands.brand_code')
							->selectRaw(''.$desc1.' as "id",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "S1NewQty",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "S1New",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "S1OldQty",
								sum(CASE WHEN month(date_trans) >= 1 AND month(date_trans) <= 6  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "S1Old",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearnow.' THEN qty_paid ELSE 0 END) as "S2NewQty",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "S2New",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearold.' THEN qty_paid ELSE 0 END) as "S2OldQty",
								sum(CASE WHEN month(date_trans) >= 7 AND month(date_trans) <= 12  AND year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "S2Old",
								sum(CASE WHEN year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "TotNew",
								sum(CASE WHEN year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "TotOld"
								')
							->whereRaw($query)
							->groupBy($filter1,$filter2)
							->orderBy($filter1);
			}
			
		}else if($tipemenu == '4'){
			// Table Tahun
			if($desc2 == ''){
				return DB::table("transaksi_sum")
							->join('customers','transaksi_sum.cust_code','=','customers.cust_id')
							->join('items','items.itemcode','=','transaksi_sum.item_code')
							->join('groups','groups.id','=','items.idgroup')
							->join('sub_groups','sub_groups.id','=','items.idsubgroup')
							->join('brands','items.brand_code','=','brands.brand_code')
							->selectRaw(''.$desc1.' as "id",
								sum(CASE WHEN year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "TotNew",
								sum(CASE WHEN year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "TotOld"
								')
							->groupBy($filter1,$filter2)
							->orderBy($filter1);
			}else{
				return DB::table("transaksi_sum")
							->join('customers','transaksi_sum.cust_code','=','customers.cust_id')
							->join('items','items.itemcode','=','transaksi_sum.item_code')
							->join('groups','groups.id','=','items.idgroup')
							->join('sub_groups','sub_groups.id','=','items.idsubgroup')
							->join('brands','items.brand_code','=','brands.brand_code')
							->selectRaw(''.$desc1.' as "id",'.$desc2.',
								sum(CASE WHEN year(date_trans) = '.$yearnow.' THEN total_paid ELSE 0 END) as "TotNew",
								sum(CASE WHEN year(date_trans) = '.$yearold.' THEN total_paid ELSE 0 END) as "TotOld"
								')
							->groupBy($filter1,$filter2)
							->orderBy($filter1);
			}
		}
    }					

    public function headings(): array
    {
    	$yearnow = date('Y');
    	$yearold = date('Y') - 1;
    	

        if($this->filter == 'item_code'){
            $col1 = 'Item Desc';
        }else if($this->filter == 'cust_code'){
            $col1 = 'Cust Desc';
        }else if($this->filter == 'cust_region'){
            $col1 = 'Region';
        }else if($this->filter == 'idgroup'){
            $col1 = 'Group Desc';
        }else if($this->filter == 'idsubgroup'){
            $col1 = 'Sub Group Desc';
        }else if($this->filter == 'brands.brand_code'){
            $col1 = 'Brand Desc';
        }

        if($this->filter2 == 'item_code'){
            $col2 = 'Item Desc';
        }else if($this->filter2 == 'cust_code'){
            $col2 = 'Cust Desc';
        }else if($this->filter2 == 'cust_region'){
            $col2 = 'Region';
        }else if($this->filter2 == 'idgroup'){
            $col2 = 'Group Desc';
        }else if($this->filter2 == 'idsubgroup'){
            $col2 = 'Sub Group Desc';
        }else if($this->filter2 == 'brands.brand_code'){
            $col2 = 'Brand Desc';
        }

        if($this->filter2 == ''){
            if($this->tipemenu == '1'){
            	// header buat bulan
            	return [
            		[
		                ' ',
		                'Januari '.$yearnow,' ',
		                'Januari '.$yearold,' ',
		                'Febuari '.$yearnow,' ',
		                'Febuari '.$yearold,' ',
		                'Maret '.$yearnow,' ',
		                'Maret '.$yearold,' ',
		                'April '.$yearnow,' ',
		                'April '.$yearold,' ',
		                'May '.$yearnow,' ',
		                'May '.$yearold,' ',
		                'Juni '.$yearnow,' ',
		                'Juni '.$yearold,' ',
		                'July '.$yearnow,' ',
		                'July '.$yearold,' ',
		                'Agustus '.$yearnow,' ',
		                'Agustus '.$yearold,' ',
		                'September '.$yearnow,' ',
		                'September '.$yearold,' ',
		                'Oktober '.$yearnow,' ',
		                'Oktober '.$yearold,' ',
		                'November '.$yearnow,' ',
		                'November '.$yearold,' ',
		                'Desember '.$yearnow,' ',
		                'Desember '.$yearold,' ',
		                'Total '.$yearnow,
		                'Total '.$yearold,
            		],[
            			$col1,
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Total',
            			'Total',
            		],
	            ];
            }else if($this->tipemenu == '2'){
            	// header buat kuartil
            	return [
            		[
		                ' ',
		                'Kuartil 1 '.$yearnow,' ',
		                'Kuartil 1 '.$yearold,' ',
		                'Kuartil 2 '.$yearnow,' ',
		                'Kuartil 2 '.$yearold,' ',
		                'Kuartil 3 '.$yearnow,' ',
		                'Kuartil 3 '.$yearold,' ',
		                'Kuartil 4 '.$yearnow,' ',
		                'Kuartil 4 '.$yearold,' ',
		                'Total '.$yearnow,
		                'Total '.$yearold

            		],[
            			$col1,
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Total',
            			'Total',
            		],
	            ];
            }else if($this->tipemenu == '3'){
            	// header buat semester
	            return [
	            	[
		                ' ',
		                'Semester 1 '.$yearnow,' ',
		                'Semester 1 '.$yearold,' ',
		                'Semester 2 '.$yearnow,' ',
		                'Semester 2 '.$yearold,' ',
		                'Total '.$yearnow,
		                'Total '.$yearold,
	            	],[
	            		$col1,
	            		'Qty','Total',
	            		'Qty','Total',
	            		'Qty','Total',
	            		'Qty','Total',
	            		'Total',
	            		'Total',
	            	]
	            ];
            }else if($this->tipemenu == '4'){
            	// header buat tahun
	            return [
	                $col1,
	                'Total '.$yearnow,
	                'Total '.$yearold,
	            ];
            }
        }else{
        	if($this->tipemenu == '1'){
            	// header buat bulan
            	return [
            		[
		                ' ',' ',
		                'Januari '.$yearnow,' ',
		                'Januari '.$yearold,' ',
		                'Febuari '.$yearnow,' ',
		                'Febuari '.$yearold,' ',
		                'Maret '.$yearnow,' ',
		                'Maret '.$yearold,' ',
		                'April '.$yearnow,' ',
		                'April '.$yearold,' ',
		                'May '.$yearnow,' ',
		                'May '.$yearold,' ',
		                'Juni '.$yearnow,' ',
		                'Juni '.$yearold,' ',
		                'July '.$yearnow,' ',
		                'July '.$yearold,' ',
		                'Agustus '.$yearnow,' ',
		                'Agustus '.$yearold,' ',
		                'September '.$yearnow,' ',
		                'September '.$yearold,' ',
		                'Oktober '.$yearnow,' ',
		                'Oktober '.$yearold,' ',
		                'November '.$yearnow,' ',
		                'November '.$yearold,' ',
		                'Desember '.$yearnow,' ',
		                'Desember '.$yearold,' ',
		                'Total '.$yearnow,
		                'Total '.$yearold,
            		],[
            			$col1,$col2,
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Total',
            			'Total',
            		],
	            ];
            }else if($this->tipemenu == '2'){
            	// header buat kuartil
            	return [
	                [
		                ' ',' ',
		                'Kuartil 1 '.$yearnow,' ',
		                'Kuartil 1 '.$yearold,' ',
		                'Kuartil 2 '.$yearnow,' ',
		                'Kuartil 2 '.$yearold,' ',
		                'Kuartil 3 '.$yearnow,' ',
		                'Kuartil 3 '.$yearold,' ',
		                'Kuartil 4 '.$yearnow,' ',
		                'Kuartil 4 '.$yearold,' ',
		                'Total '.$yearnow,
		                'Total '.$yearold

            		],[
            			$col1,$col2,
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Qty','Total',
            			'Total',
            			'Total',
            		],
	            ];
            }else if($this->tipemenu == '3'){
            	// header buat semester
	            return [
	            	[
		                ' ',' ',
		                'Semester 1 '.$yearnow,' ',
		                'Semester 1 '.$yearold,' ',
		                'Semester 2 '.$yearnow,' ',
		                'Semester 2 '.$yearold,' ',
		                'Total '.$yearnow,
		                'Total '.$yearold,
	            	],[
	            		$col1,$col2,
	            		'Qty','Total',
	            		'Qty','Total',
	            		'Qty','Total',
	            		'Qty','Total',
	            		'Total',
	            		'Total',
	            	]
	            ];
            }else if($this->tipemenu == '4'){
            	// header buat tahun
	            return [
	                $col1,
                	$col2,
	                'Total '.$yearnow,
	                'Total '.$yearold,
	            ];
            }
        }
    }
}
