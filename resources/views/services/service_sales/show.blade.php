
@extends('layout.app')
	@section('content')
    <style type="text/css">
        .transition_cul_section{margin-left: 0 !important; margin-right: 0 !important;}
        .aside_system{width: 100%; overflow: hidden; margin: 10px 0px;}
        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{padding: 5px;}
    </style>
	<!-- begin #content -->
	<div id="content" class="content">
		<div class="row">
		    <div class="col-md-12">
                <div class="panel panel-inverse">
                    <div class="panel-heading"> 
                        <h4 class="panel-title">Sales Delivery</h4>
                    </div>
                    <div class="panel-body">
                    @if(count($oldChallan)>0)
                    <div class="col-md-12">
                        <ul class="old_challan">
                            <li><b>Challan : </b></li>
                            @foreach($oldChallan as $challan)
                            <li><a class="btn btn-xs btn-success" href='{{URL::to("inventory-sales-challan/$challan->id")}}'>{{$challan->challan_id}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if($getInvoiceData->total_amount>0)
                        {!! Form::open(array('route' => 'inventory-sales-challan.store','class'=>'form-horizontal author_form','method'=>'POST', 'id'=>'commentForm','role'=>'form','data-parsley-validate novalidate')) !!}
                        	<div class="form-group">
								
								<input class="form-control" type="hidden" id="client" name="company_name" placeholder="Company Name" data-parsley-required="true" />

                                <input type="hidden" name="fk_sales_id" value="{{$id}}">
							</div>
							
                            <div class="col-md-12">
                                <!-- info -->
                                <div class="row">
                                    <div id="customer_info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table">
                                                 <tr>
                                                    <td width="30%">Sales Invoice : </td>
                                                    <td>{{$getInvoiceData->invoice_id}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Customer Name :</td>
                                                    <td>{{$getInvoiceData->client_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Mobile No :</td>
                                                    <td>{{$getInvoiceData->mobile_no}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Customer Id :</td>
                                                    <td>{{$getInvoiceData->client_id}}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table">
                                                 <tr>
                                                    <td width="30%">Sales Order Date : </td>
                                                    <td>{{$getInvoiceData->date}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Email : </td>
                                                    <td>{{$getInvoiceData->email_id}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Company name : </td>
                                                    <td>{{$getInvoiceData->company_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Payment Date * :</td>
                                                    <td><input class="form-control" type="text" id="datepicker-autoClose" name="date" value="<?php echo date('m/d/Y'); ?>" placeholder="t_date" data-parsley-required="true" /></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div> 
                                </div>
                                <input type="hidden" name="created_by" value="{{ Auth::user()->id }}">
                                <!-- transition -->
                                <div class="view_center_folwchart">
                                <div class='row'>
                                    <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                                        <table class="table table-bordered table-hover" id="table_auto">
                                            <thead>
                                                <tr class="active">
                                                <th width="5%"><i class="fa fa-trash text-danger"></i></th>
                                                    <th width="20%">Product Name</th>
                                                    <th width="10%">Unit Price</th>
                                                    <th width="15%">Discount For Big Qty</th>
                                                    <th width="15%" colspan="2">Big Quantity</th>
                                                    <th width="15%" colspan="2">Small Quantity</th>
                                                    <th width="20%">Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <? $i=0; ?>
                                            @foreach($getProductData as $sales)
                                            <? $i++; ?>
                                            
                                                <tr id="row{{$i}}">
                                                <td><button type="button" class="fa fa-trash btn btn-danger btn-xs"  onclick="removeRow({{$i}})"></button>
                                                </td>
                                                    <td>{{$sales->product_name}}
                                                        <input type="hidden" name="fk_sales_item_id[]" value="{{$sales->id}}">
                                                    </td>
                                                    
                                                    <td>{{round($sales->product_price_amount,2)}}
                                                    <input type="hidden" name="product_price_amount[]" id="hidden_{{$i}}" class="form-control changesNo" value="{{round($sales->product_price_amount,2)}}"></td>
                                                    <td>{{round($sales->product_wise_discount,2)}}
                                                    <input type="hidden" min="0" step="any" name="product_wise_discount[]" id="benefit_{{$i}}" class="form-control changesNo benefit" value="{{round($sales->product_wise_discount,2)}}"></td>
                                                    <td><input type="number" value="{{$sales->sales_qty}}" min="0" max="{{$sales->sales_qty}}" step="any" name="sales_qty[]" id="quantity2_{{$i}}" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>
                                                    <td>{{$sales->uom_name}}</td>
                                                    <td><input type="number" value="{{$sales->small_qty}}" min="0" max="{{($sales->small_unit>0)?$sales->small_unit-1:0}}" step="any" name="small_qty[]" id="smallQty_{{$i}}" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>
                                                    <td>{{$sales->small_unit_name}}</td>
                                                    <td>
                                                    <input type="number" value="{{round($sales->product_paid_amount,2)}}" min="0" step="any" name="product_paid_amount[]" id="total_{{$i}}" class="form-control totalLinePrice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly="readonly">

                                                    <input type="hidden" value="{{round($sales->product_price_amount,2)-round($sales->product_wise_discount,2)}}" min="0" step="any" name="price[]" id="cost_{{$i}}" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" >
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class='row'>   
                                    <div class='col-xs-12 col-sm-7 col-md-7 col-lg-7'>
                                      Note: <? echo $getInvoiceData->summary ?> 
                                        
                                    </div>
                                    <?
                                    $due=$getInvoiceData->total_amount-$getInvoiceData->paid_amount;
                                    ?>
                                    <div class='col-xs-12 col-sm-5 col-md-5 col-lg-5'>
                                        <span class="form-inline">
                                            <div class="form-group aside_system">
                                                    <label class="col-md-4">Total Amount :</label>
                                                    <div class="col-md-8 input-group">
                                                        <div class="input-group-addon currency">৳</div>
                                                        <input value="{{round($getInvoiceData->total_amount,2)}}" type="number" min="0" step="any" class="form-control" name="total_amount" id="subTotal" placeholder="Total Amount" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly>
                                                    </div>
                                                </div>
                                            <div class="form-group aside_system">
                                                <label class="col-md-4"> Delivery Date :</label>
                                                <div class="input-group col-md-8">
                                                    <div class="input-group-addon currency">৳</div>
                                                    <input value="{{date('Y-m-d')}}" type="date" name="received_date" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group aside_system">
                                                <label class="col-md-4"></label>
                                                <div class="input-group col-md-8">
                                                    <button type="submit" class="btn btn-success" style="width: 100%;">Submit</button>
                                                </div>
                                            </div>
                                        </span>

                                    </div>
                                </div>

                            </div><br>
                            </div>

							
                        {!! Form::close(); !!}
                        @else
                        <h2 class="text-success">This sales order delivery is successfully completed.</h2>
                        @endif
                    </div>
                </div>
		    </div>
		</div>
	</div>
	<!-- end #content -->
	
<script src="{{asset('public/plugins/jquery/jquery-1.9.1.min.js')}}"></script>
<script src="{{asset('public/custom_js/script_sales.js')}}"></script> 

<script type="text/javascript">
     function removeRow(id){
            var sub_total = $('#total_'+id).val();
            var total = $('#subTotal').val()-sub_total;
            $('#subTotal').val(total);
            $('#row'+id).html('<td colspan="9" class="text-warning">Pending for next challan</td>');
        }
  
</script>

@endsection
