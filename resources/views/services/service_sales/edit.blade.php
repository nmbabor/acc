
@extends('layout.app')
	@section('content')
	<!-- begin #content -->
	<div id="content" class="content">
		<div class="row">
		    <div class="col-md-12">
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a class="btn btn-info btn-xs" href="{{URL::to('/services-sales/create')}}"><i class="fa fa-plus"></i>  Add New Sales</a>
                            <a class="btn btn-info btn-xs" href="{{URL::to('/services-sales')}}"><i class="fa fa-list"></i>  All Sales</a>
                        </div>
                        <h4 class="panel-title">Edit Serevice Sales</h4>
                    </div>
                    <div class="panel-body">
                        {!! Form::open(array('route' =>[ 'services-sales.update',$data->id],'class'=>'form-horizontal author_form','method'=>'PUT', 'id'=>'commentForm','role'=>'form','data-parsley-validate novalidate')) !!}
                        	<div class="form-group">
								
								<input class="form-control" type="hidden" id="client" name="company_name" placeholder="Company Name" data-parsley-required="true" />

                                <input type="hidden" name="invoice_id" value="">
							</div>
							
                            <div class="col-md-12">
                                <!-- info -->
                                <div class="row">
                                    <div id="customer_info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                 <tr>
                                                    <td width="30%">Invoice : </td>
                                                    <td>{{$data->invoice_id}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Customer Name :</td>
                                                    <td>{{$data->client->client_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Mobile No :</td>
                                                    <td>{{$data->client->mobile_no}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Customer Id :</td>
                                                    <td>{{$data->client->client_id}}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered" width="100%">
                                                 <tr>
                                                    <td width="35%">Payment Date : </td>
                                                    <td>{{$data->date}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Email : </td>
                                                    <td>{{$data->client->email_id}}</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>Date * :</td>
                                                    <td><input class="form-control datepicker" type="text" name="date" value="<?php echo date('d-m-Y'); ?>" placeholder="t_date" data-parsley-required="true" /></td>
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
                                                <th width="3%">SL</th>
                                                    <th width="22%">Service Name</th>
                                                    <th width="10%">Price</th>
                                                    <th width="10%">Qty</th>
                                                    <th width="15%">Discount</th>
                                                    <th width="20%">Sub Total Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <? $i=0; ?>
                                            @foreach($data->items as $sales)
                                            <? $i++; ?>
                                            <input type="hidden" name="sales_item_id[]" value="{{$sales->id}}">
                                                <tr>
                                                <td>{{$i}}</td>
                                                    <td>{{$sales->product_name}}</td>
                                                    <td>{{round($sales->product_price_amount,2)}}
                                                    <input type="hidden" name="product_price_amount[]" id="hidden_{{$i}}" class="form-control changesNo" value="{{round($sales->product_price_amount,2)}}"></td>
                                                    <td><input type="number" min="0" name="qty[]" id="qty_{{$i}}" class="form-control changesNo" value="{{round($sales->qty,2)}}"></td>
                                                    <td><input type="number" value="{{round($sales->product_wise_discount,2)}}" min="0" step="any" name="product_wise_discount[]" id="benefit_{{$i}}" class="form-control changesNo benefit" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>
                                                   <td>
                                                    <input type="number" value="{{round($sales->product_paid_amount,2)}}" min="0" step="any" name="product_paid_amount[]" id="total_{{$i}}" class="form-control totalLinePrice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly="readonly">

                                                    <input type="hidden" value="{{round($sales->product_paid_amount,2)}}" min="0" step="any" name="price[]" id="cost_{{$i}}" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" >
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class='row'>   
                                    <div class='col-xs-12 col-sm-7 col-md-7 col-lg-7'>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Summery</label>
                                            <div class="col-md-8">
                                                <textarea name="summary" rows="3" class="form-control " placeholder="Write something..."><? echo $data->summary ?></textarea>
                                            </div>
                                        </div>

                                        
                                    </div>
                                    <?
                                    $due=$data->total_amount-$data->paid_amount;
                                    ?>
                                    <div class='col-xs-12 col-sm-5 col-md-5 col-lg-5'>
                                        <div class="form-inlines">
                                            <div class="form-group aside_system">
                                                    <label class="col-md-4">Total Amount: &nbsp;</label>
                                                    <div class="input-group col-md-8">
                                                        <div class="input-group-addon currency">৳</div>
                                                        <input value="{{round($data->total_amount,2)}}" type="number" min="0" step="any" class="form-control" name="total_amount" id="subTotal" placeholder="Total Amount" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly>
                                                    </div>
                                                </div>
                                                <!--  -->
                                            
                                            <div class="form-group aside_system">
                                                <label class="col-md-4">Paid Amount: &nbsp;</label>
                                                <div class="input-group col-md-8">
                                                    <div class="input-group-addon currency">৳</div>
                                                    <input value="{{round($data->paid_amount,2)}}" type="number" min="0" step="any" class="form-control" name="paid_amount" id="amountPaid" placeholder="Paid Amount" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;">
                                                    <input type="hidden" name="payment_id" value="{{$history->id}}">
                                                </div>
                                            </div>
                                            <div class="form-group aside_system">
                                                <label class="col-md-4">Amount Due: &nbsp;</label>
                                                <div class="input-group col-md-8">
                                                    <div class="input-group-addon currency">৳</div>
                                                    <input value="{{$due}}" type="number" min="0" step="any" class="form-control amountDue" name="total_due"  id="amountDue" placeholder="Amount Due" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div><br>
                            </div>

							<div class="form-group">
								<label class="control-label col-md-2 col-sm-2"></label>
								<div class="col-md-8 col-sm-8">
									<button type="submit" class="btn btn-primary" style="width: 100%;">Confirm</button>
								</div>
                                <label class="control-label col-md-2 col-sm-2"></label>
							</div>
                        {!! Form::close(); !!}
                    </div>
                </div>
		    </div>
		</div>
	</div>
	<!-- end #content -->
@stop
@section('script')	
<script src="{{asset('public/custom_js/script_service_sales.js')}}"></script> 
<script type="text/javascript">


}
</script>

@endsection
