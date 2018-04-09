@extends('layout.app')
	@section('content')
    <style type="text/css">
        .modal-dialog{width: 900px;}
        .invoice-note {margin-top: 35px;}
        .refId {display: none;}
        .customerInfo p{margin-bottom: 2px;}
        .customerInfo, .invoiceInfo{float: left;padding: 0 10px;}
        .table{margin-bottom: 0px;}
        .invoice>div:not(.invoice-footer){margin-bottom: 5px;}
        .print-logo{width: 100px;}
        .invoice_top{display: none;}
        .print-footer{display: none;}
        .printable{display: none;}

        
    </style>
		<!-- begin #content -->
		<div id="content" class="content">
            <!-- begin invoice -->
            <div class="invoice">
                <div class="invoice-company">
                    <span class="pull-right hidden-print">
                    
                    <a href="javascript:;" onclick="printPage('print_body')" class="printbtn btn btn-sm btn-success m-b-10"><i class="fa fa-print m-r-5"></i> Print</a>
                    <a class="printbtn btn btn-sm btn-info m-b-10" href='{{URL::to("deposit")}}'><i class="fa fa-list"></i> View All</a>
                    </span>
                </div>
            <div id="print_body" style="width: 100%;overflow: hidden; padding: 10px 20px;">
                

			 @include('pad.header')
                <div id="customer_info" style="padding: 0 10px;">
                    <div class="row">
                        <h4 style="width: 100%;text-align: center;margin-top: 15px;"><b style="padding: 10px 20px;border-radius: 10px;color: #000;border:1px solid #ddd;">Diposit</b>
                        <br>
                        <br>
                        <br>
                        <br>
                        </h4>

                        <div class="customerInfo" style="width: 60%;float: left;">
                            <p><b>Name : </b>{{$data->deposit->client->client_name}}</p>
                            <p><b>Address : </b>{{$data->deposit->client->address}}</p>
                            <p><b>Mobile No :</b> {{$data->deposit->client->mobile_no}}</p>
                            <br>
                            <br>
                        </div>
                        
                        <div class="invoiceInfo" style="width: 40%;float: left;">
                            <table class="table table-bordered">
                                <tr>
                                
                                    <th>Invoice #:</th>
                                    <th>{{$data->invoice_id}} 
                               
                                </th>
                                </tr>
                                 <tr>
                                    <td>Invoice Date : </td>
                                    <td>{{$data->payment_date}}</td>
                                </tr>
                                @if(isset($loan))
                                   <tr>
                                    <td>Loan ID : </td>
                                    <td>{{$loan->invoice_no}}</td>
                                </tr> 
                                <tr>
                                    <td>Total Loan: </td>
                                    <td>{{$loan->total_paid}}</td>
                                </tr> 
                                <tr>
                                    <td> Loan Total Paid: </td>
                                    <td>{{$loan->loan_paid}}</td>
                                </tr> 
                                @endif
                               
                            </table>
                            <div>
                            <br>
                            </div>
                        </div>
                    </div>
                </div> 
                
                <div class="invoice-content">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">SL</th>
                                    <th>Sector </th>
                                    <th>Description</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                </tr>
                            </thead>
                            <tbody>
                            <? $i=0; ?>
                                <?
                            $total_amount=0;
                            $paid=0;
                            $totalDue=0;
                            ?>
                            @foreach($data->items as $task)
                            <? $i++; ?>
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>
                                        {{$task->depositItem->subCategory->sub_category_name}}<br />
                                        
                                    </td>
                                    <td><small>{{$task->depositItem->description}}</small></td>
                                    <td>{{$task->last_due}}
                                    </td>
                                    <td>{{$task->paid}}</td>
                                    <td>{{$task->last_due-$task->paid}}</td>
                                    <? 
                                        $due=$task->last_due-$task->paid;
                                        $totalDue+=$due;
                                        $total_amount+= $task->last_due;
                                        $paid+= $task->paid;

                                    ?>
                                </tr>
                            @endforeach 
                                <tr class="info">
                                    <th style="text-align: right;" colspan="3">Total = </th>
                                    <th>{{$total_amount}}</th>
                                    <th>{{$paid}}</th>
                                    <th>{{$totalDue}}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                
                </div>
               <div class="printFooter" style="overflow: hidden;width: 100%;">
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