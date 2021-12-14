<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class SalesExport implements FromQuery, WithHeadings, ShouldAutoSize
{
	use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    function __construct($c_item,$c_filter,$c_item2,$c_filter2,$c_datefrom,$c_dateto) {
            $this->item = $c_filter;
            $this->filter = $c_item;
            $this->filter2 = $c_filter2;
            $this->item2 = $c_item2;
            $this->datefrom = $c_datefrom;
            $this->dateto = $c_dateto;
    }

    public function query()
    {   
        $filter1 = $this->filter;
        $filter2 = $this->filter2;
        $search1 = $this->item;
        $search2 = $this->item2;
        $startdate = $this->datefrom;
        $enddate = $this->dateto;


        $desc2 = '';

        //dd($filter1,$filter2,$search1,$search2,$startdate,$enddate);

        if($startdate == ''){
            $startdate = '2000-12-01';
        }

        if($enddate == ''){
            $enddate = '3000-12-01';
        }

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
            $desc1 = 'brand_desc';
        }

        $query = 'date_trans BETWEEN "'.$startdate.'" AND "'.$enddate.'"';
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

        //if(($filter1 == 'idgroup' || $filter1 == 'idsubgroup' || $filter1 == 'cust_region') && $filter2 == ''){
        if($filter1 != '' && $filter2 == ''){
            if($desc2 == ''){
               return DB::table('transaksi_hist')
                            ->join('customers','transaksi_hist.cust_code','=','customers.cust_id')
                            ->join('items','items.itemcode','=','transaksi_hist.item_code')
							->join('brands','items.brand_code','=','brands.brand_code')
                            ->join('groups','groups.id','=','items.idgroup')
                            ->join('sub_groups','sub_groups.id','=','items.idsubgroup')
                            //->selectRaw(''.$descript.' as "id", date_trans, sum(total) as total')
                            ->selectRaw($desc1.',date_trans,sum(qty_paid) as qty, sum(total_paid) as total')
                            ->whereRaw($query)
							->whereNotNull('qty_paid')
                            ->groupBy($filter1,'date_trans')
                            ->orderBy('date_trans','asc');

            }else{
               return DB::table('transaksi_hist')
                            ->join('customers','transaksi_hist.cust_code','=','customers.cust_id')
                            ->join('items','items.itemcode','=','transaksi_hist.item_code')
							->join('brands','items.brand_code','=','brands.brand_code')
                            ->join('groups','groups.id','=','items.idgroup')
                            ->join('sub_groups','sub_groups.id','=','items.idsubgroup')
                            ->selectRaw($desc1.', '.$desc2.', date_trans,sum(qty_paid) as qty, sum(total_paid) as total')
                            //->selectRaw($desc1.', '.$desc2.',date_trans, total')
                            ->whereRaw($query)
							->whereNotNull('qty_paid')
                            ->groupBy($filter1,'date_trans')
                            ->orderBy('date_trans','asc');
            }

        }else{
            if($desc2 == ''){
                return  DB::table('transaksi_hist')
                            ->join('customers','transaksi_hist.cust_code','=','customers.cust_id')
                            ->join('items','items.itemcode','=','transaksi_hist.item_code')
							->join('brands','items.brand_code','=','brands.brand_code')
                            ->join('groups','groups.id','=','items.idgroup')
                            ->join('sub_groups','sub_groups.id','=','items.idsubgroup')
                            ->selectRaw(''.$desc1.' as "id", date_trans, sum(qty_paid) as qty, sum(total_paid) as total')
                            ->whereRaw($query)
							->whereNotNull('qty_paid')
                            ->groupBy($filter1,$filter2,'date_trans')
                            ->orderBy('date_trans','asc');


            }else{
                return DB::table('transaksi_hist')
                            ->join('customers','transaksi_hist.cust_code','=','customers.cust_id')
                            ->join('items','items.itemcode','=','transaksi_hist.item_code')
							->join('brands','items.brand_code','=','brands.brand_code')
                            ->join('groups','groups.id','=','items.idgroup')
                            ->join('sub_groups','sub_groups.id','=','items.idsubgroup')
                            ->selectRaw($desc1.', '.$desc2.',date_trans, sum(qty_paid) as qty, sum(total_paid) as total')
                            ->whereRaw($query)
							->whereNotNull('qty_paid')
                            ->groupBy($filter1,$filter2,'date_trans')
                            ->orderBy('date_trans','asc');
            }
        }

    }					

    public function headings(): array
    {
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
            return [
                $col1,
                'Date',
                'Qty',
                'Jumlah',
            ];
        }else{
            return [
                $col1,
                $col2,
                'Date',
                'Qty',
                'Jumlah',
            ];
        }
    }
}
