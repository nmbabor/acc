@extends('layout.app')
	@section('content')
    <style type="text/css">
        .modal-dialog{width: 900px;}
        .invoice-note {margin-top: 35px;}
        .printable {display: none;}
        .customerInfo p{margin-bottom: 2px;}
        .customerInfo, .invoiceInfo{float: left;padding: 0 10px;}
        .table{margin-bottom: 0px;}
        .invoice>div:not(.invoice-footer){margin-bottom: 5px;}
        @media print {
            .col-md-6{width: 50%;float: left;}
            .alert {display: none;}
            .reflink {display: none;}
            .refId {display: inline-block;}
            .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{padding: 2px;}
          @page {
              size: auto;   /* auto is the initial value */
              margin: 5mm;  /* this affects the margin in the printer settings */
            }
        }
    </style>
		<!-- begin #content -->
		<div id="content" class="content">
			
			<!-- begin invoice -->
			<div class="invoice">
                <div class="invoice-company">
                    <span class="pull-right hidden-print">
                   <a href="javascript:;" onclick="printPage('print_body')" class="printbtn btn btn-sm btn-success m-b-10"><i class="fa fa-print m-r-5"></i> Print</a>
                    <a href="{{URL::to('inventory-return/create')}}" class="btn btn-sm btn-info m-b-10"><i class="fa fa-plus m-r-5"></i>New <Return></Return></a>
                    </span>
                </div>
                <div id="print_body" style="width: 100%;overflow: hidden; padding: 10px 20px;">
                

             @include('pad.header')

                    <div id="customer_info">
                    <div class="row">
                        <div class="customerInfo" style="width: 60%;float: left;">
                            <p><b><u>Supplier Information</u></b></p>
                            <p><b>Organization Name : </b>{{$data->purchase->supplier->company_name}}</p>
                            <p><b>Address : </b> {{$data->purchase->supplier->address}}</p>
                            <p><b>Mobile No :</b> {{$data->purchase->supplier->mobile_no}}</p>
                            <p><b>Supplier Id :</b> {{$data->purchase->supplier->suppllier_id}}</p>

                        </div>
                        <div class="invoiceInfo" style="width: 40%;float: left;">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Invoice #: </th>
                                    <th>{{$data->purchase->inventory_order_id}} </th>
                                </th>
                                </tr>
                                 <tr>
                                    <td>Return Date : </td>
                                    <td>{{date('d-m-Y',strtotime($data->date))}}</td>
                                </tr>
                                <tr>
                                    <td>Total Amount :</td>
                                    <td>{{round($data->total_amount,3)}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div> 
                
                <div class="invoice-content">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">SL</th>
                                    <th>Product</th>
                                    <th>Total Qty</th>
                                    <th>Return Qty</th>
                                    <th class="text-right">Return Amount</th>

                                </tr>
                            </thead>
                            <tbody>
                            
                            <? $i=0; ?>
                                @if(isset($data->items))
                                @foreach($data->items as $product)
                                <? $i++; ?>
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$product->items->product->product_name}}</td>
                                    <td>{{$product->items->qty+$product->qty}}</td>
                                    <td>{{$product->qty}}</td>
                                    <td class="text-right">{{round($product->sub_total,2)}}</td>
                                </tr>
                                @endforeach

                                @endif

                            </tbody>
                        </table>
                        <div class="col-md-4 col-md-offset-2 no-padding summary-table" style="width:40%;float: right;">
                            <table class="table table-bordered table-striped">
                                <thead>

                                </thead>
                                <tbody>

                                <tr>
                                    <th width="40%" class="text-right">Total Return:</th>
                                    <td class="text-right">{{round($data->total_return,3)}}</td>
                                </tr>

                                <tr>
                                    <th class="text-right">Back Amount :</th>
                                    <td class="text-right">{{round($data->back_amount,3)}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
                <div class="printFooter" style="overflow: hidden;
    width: 100%;">
                   @include('pad.footer')
               </div>
               </div>
                
            </div>

			<!-- end invoice -->
		</div>
		<!-- end #content -->
    
@endsection
@section('script')

<script src="{{asset('public/custom_js/printThis.js')}}"></script>
<script type="text/javascript">
    function printPage(id){
        $('#'+id).printThis();
    }
</script>
@endsection