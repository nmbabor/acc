@extends('layout.app')
	@section('content')
    <style type="text/css">
        .modal-dialog{width: 900px;}
        .invoice-note {margin-top: 35px;}
        .printable {display: none;}
        .customerInfo p{margin-bottom: 0px;}
        .customerInfo, .invoiceInfo{float: left;padding: 0 10px;}
        .table{margin-bottom: 0px;}
        .invoice>div:not(.invoice-footer){margin-bottom: 5px;}
        #second_copy{display: none;}
        
    </style>
    <script type="text/javascript" src="{{asset('public/js/inWords.js')}}"></script>
		<!-- begin #content -->
		<div id="content" class="content">
			
			<!-- begin invoice -->
			<div class="invoice">
                <div class="invoice-company">
                    <span class="pull-right hidden-print">
                   <a href="javascript:;" onclick="printPage('print_body')" class="printbtn btn btn-sm btn-success m-b-10"><i class="fa fa-print m-r-5"></i> Print</a>
                    <a href="{{URL::to('services-sales/create')}}" class="btn btn-sm btn-info m-b-10"><i class="fa fa-plus m-r-5"></i> New  </a>
                    </span>
                </div>
                <div id="print_body" style="width: 100%;overflow: hidden; padding: 10px 20px;">
                
            <style type="text/css">
                @media print {
                        .col-md-6{width: 50%;float: left;}
                        .customerInfo p{margin-bottom: 0px;}
                        .alert {display: none;}
                        .reflink {display: none;}
                        .refId {display: inline-block;}
                        .table{margin-bottom: 0px;}    
                        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{padding: 0px;}
                      @page {
                      size: auto;   /* auto is the initial value */
                      margin: 5mm;  /* this affects the margin in the printer settings */
                    }
                    }
            </style>
            <div id="first_copy" style="width: 100%;overflow: hidden;">
             @include('pad.header')

                    <div id="customer_info">
                    <div class="row">
                        <div class="customerInfo" style="width: 60%;float: left;">
                            <p><b><u>Invoiced to</u></b></p>
                            <p><b>Name : </b>{{$data->client->client_name}}</p>
                            <p><b>Mobile No :</b> {{$data->client->mobile_no}}</p>
                            <p><b>Address :</b> {{$data->client->address}}</p>
                        </div>
                        <div class="invoiceInfo" style="width: 40%;float: left;">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Invoice #: </th>
                                    <th>{{$data->invoice_id}}
                                </th>
                                </tr>
                                 <tr>
                                    <td>Invoice Date : </td>
                                    <td>{{$data->date}}</td>
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
                                    <th>Service Name</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Discount</th>
                                    <th class="text-right">Sub Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                            <? $i=0; ?>
                                @if(isset($data->items))
                                @foreach($data->items as $product)
                                <? $i++; ?>
                                <tr>
                                <td>{{$i}}</td>
                                    <td>
                                        {{$product->service->product_name}}
                                    </td>
                                    <td>{{round($product->product_price_amount,2)}}</td>
                                    <td>{{round($product->qty,2)}}</td>
                                    <td>{{round($product->product_wise_discount,2)}}</td>
                                    <td class="text-right">{{round($product->product_paid_amount,2)}}</td>
                                    
                                </tr>
                                @endforeach

                                @endif

                        
                            </tbody>
                        </table>
                    </div>
                    <h5 style="float: left;">In Words :
                        <? 
                        echo App\Http\Controllers\NumberFormat::taka(intVal($data->paid_amount));
                        ?></h5>
                    <div class="invoice-calculate" style="width: 30%;float: right;">
                        <table class="table table-bordered">
                    
                        <tr>
                            <th class="text-right">Payable Amount</th>
                            <th class="text-right"><?php echo round($data->total_amount,2); ?></th>
                        </tr>
                        
                        <tr>
                            <th class="text-right">Paid Amount</th>
                            <th class="text-right">{{round($data->paid_amount,2)}}</th>
                        </tr>
                        <tr>
                            <th class="text-right">Total Due</th>
                            <th class="text-right"><?php echo round($data->total_amount,2)-round($data->paid_amount,2);?></th>
                        </tr>

                            
                        </table>
                    </div>

                    
                        <h6 style="text-transform: capitalize;text-align: left;"><b></b></h6>
                    
                </div>
               </div>
               <br>
               <hr>
               <br>
               <div id="second_copy"  style="width: 100%;overflow: hidden;"><!-- Load First Copy --></div>
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

    $('#second_copy').html($('#first_copy').html());
    
</script>
@endsection