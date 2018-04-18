
@extends('layout.app')
	@section('content')
	<!-- begin #content -->
	<div id="content" class="content">
		<div class="row">
		    <div class="col-md-12">
                <div class="panel panel-inverse">
                    <div class="panel-heading"> 
                        <h4 class="panel-title">Return sales product</h4>
                    </div>
                    <div class="panel-body">
                        {!! Form::open(array('route' =>'inventory-purchase-return.store','class'=>'form-horizontal sales-form','method'=>'POST','role'=>'form','data-parsley-validate novalidate')) !!}
                        	<div class="form-group">
								<input type="hidden" name="id" value="{{$data->id}}" />
							</div>
							
                            <div class="col-md-12">
                                <!-- info -->
                                <div class="row">
                                    <div id="customer_info" style="width: 100%">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                 <tr>
                                                    <td width="45%">Invoice : </td>
                                                    <td>{{$data->invoice_id}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Organization Name :</td>
                                                    <td>{{$data->supplier->company_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Mobile No :</td>
                                                    <td>{{$data->supplier->mobile_no}}</td>
                                                </tr>
                                                
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td width="45%">Email : </td>
                                                    <td>{{$data->supplier->email_id}}</td>
                                                </tr>
                                                <tr>
                                                    <td width="30%">Representative : </td>
                                                    <td>{{$data->supplier->representative}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Return Date * :</td>
                                                    <td><input class="form-control datepicker" type="text"  name="date" value="{{date('d-m-Y',strtotime($data->date))}}" placeholder="t_date" data-parsley-required="true" /></td>
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
                                                    <th width="22%">Product Name</th>
                                                    <th width="15%" colspan="2">Quantity</th>
                                                    <th width="10%">Unit Price</th>
                                                    <th width="20%">Sub Total Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <? $i=0; ?>
                                            @foreach($data->items as $sales)
                                            <? $i++; ?>
                                            <input type="hidden" name="fk_purchasae_item_id[]" value="{{$sales->id}}">
                                            <input type="hidden" name="product_id[]" value="{{$sales->fk_product_id}}">
                                            <input type="hidden" name="fk_inventory_item_id[]" value="{{$sales->fk_inventory_id}}">
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>{{$sales->product->product_name}}  ({{$sales->model->model_name}})</td>

                                                    <td><input type="number" value="{{$sales->qty}}" min="0" max="{{$sales->qty}}" step="any" name="qty[]" id="quantity2_{{$i}}" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>
                                                    <td>{{$sales->small_unit_name}}</td>
                                                    <td>
                                                        <input type="number" min="0" readonly step="any" name="cost_per_unit[]" id="hidden_{{$i}}" class="form-control changesNo" value="{{round($sales->cost_per_unit,2)}}"></td>

                                                    <td>
                                                        <input type="number" value="{{round($sales->payable_amount,2)}}" min="0" step="any" name="payable_amount[]" id="total_{{$i}}" class="form-control totalLinePrice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly="readonly">

                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class='row'>   
                                    <div class='col-xs-12 col-sm-7 col-md-7 col-lg-7'>
                                    
                                    </div>
                                    <?
                                    $due=$data->total_amount-$data->total_paid;
                                    ?>
                                    <div class='col-xs-12 col-sm-5 col-md-5 col-lg-5'>
                                        <span class="form-inlines">
                                            <?
                                            $paid_amount=isset($payment->paid)?$payment->paid:$data->total_paid;
                                            ?>
                                            <input type="hidden" name="payment_id" value="{{isset($payment->id)?$payment->id:''}}">
                                            <!-- new -->
                                            <div class="form-group">
                                                <label class="col-md-4 control-label"> Total Amount:</label>
                                                <div class="input-group col-md-8">
                                                    <div class="input-group-addon currency">৳</div>
                                                    <input tabindex="-1" name="total_amount" value="{{round($data->total_amount,2)}}" type="number" min="0" step="any" class="form-control" id="subTotal" placeholder="Sub Total" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly>
                                                </div>
                                            </div>


                                            <input type="hidden" name="fk_account_id" value="1">
                                            <input type="hidden" name="fk_method_id" value="3">
                                                <!--  -->
                                        </span>
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
@endsection
@section('script')
<script src="{{asset('public/custom_js/script_sales.js')}}"></script> 
@endsection
