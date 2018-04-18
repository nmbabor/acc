
@extends('layout.app')
	@section('content')
	<!-- begin #content -->
	<div id="content" class="content">
		<div class="row">
		    <div class="col-md-12">
                <div class="panel panel-inverse">
                    <div class="panel-heading"> 
                        <h4 class="panel-title">Due Paid</h4>
                    </div>
                    <div class="panel-body">
                        {!! Form::open(array('url' =>[ 'hospital-services-sales-due-paid',$getInvoiceData->id],'class'=>'form-horizontal author_form','method'=>'POST', 'id'=>'commentForm','role'=>'form','data-parsley-validate novalidate')) !!}
							
                            <div class="col-md-12">
                                <!-- info -->
                                <div class="row">
                                    <div id="customer_info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table">
                                                 <tr>
                                                    <td width="30%">Invoice : </td>
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
                                                    <td width="30%">Payment Date : </td>
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
                                                    <th width="20%">Service Name</th>
                                                    <th width="10%">Price
                                                    <th width="10%">Discount</th>
                                                    <th width="19%">Sub Total Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($getProductData as $sales)
                                                <tr>
                                                    <td>{{$sales->product_name}}</td>
                                                    <td>{{round($sales->product_price_amount,2)}}</td>
                                                    <td>{{round($sales->product_wise_discount,2)}}</td>
                                                    <td>{{round($sales->product_paid_amount,2)}}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="success">
                                                <th colspan="3">Total=</th>
                                                <th>{{round($getInvoiceData->total_amount,2)}}</th>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class='row'>   
                                    <div class='col-xs-12 col-sm-7 col-md-7 col-lg-7'>
                                      <textarea name="summary" rows="3" class="form-control " placeholder="Write something..."><? echo $getInvoiceData->summary ?></textarea>  
                                        
                                    </div>
                                    <?
                                    $due=$getInvoiceData->total_amount-$getInvoiceData->paid_amount;
                                    ?>
                                    <div class='col-xs-12 col-sm-5 col-md-5 col-lg-5'>
                                        <div class="form-inlines">
                                            <div class="form-group aside_system">
                                                    <label class="col-md-4">Previous Paid: &nbsp;</label>
                                                    <div class="input-group col-md-8">
                                                        <div class="input-group-addon currency">৳</div>
                                                        <input value="{{round($getInvoiceData->paid_amount,2)}}" type="number" min="0" step="any" class="form-control" name="last_paid"  readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group aside_system">
                                                    <label class="col-md-4">Previous Due: &nbsp;</label>
                                                    <div class="input-group col-md-8">
                                                        <div class="input-group-addon currency">৳</div>
                                                        <input value="{{round($due,2)}}" type="number" min="0" step="any" class="form-control" name="last_due" id="subTotal" placeholder="Total Amount" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly>
                                                    </div>
                                                </div>
                                                <!--  -->
                                            
                                            <div class="form-group aside_system">
                                                <label class="col-md-4">Paid Amount: &nbsp;</label>
                                                <div class="input-group col-md-8">
                                                    <div class="input-group-addon currency">৳</div>
                                                    <input value="" type="number" min="0" step="any" max="{{$due}}" class="form-control" name="paid_amount" id="amountPaid" placeholder="Paid Amount" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" {{($due>0)?'':'readonly'}}>
                                                </div>
                                            </div>
                                            <div class="form-group aside_system">
                                                <label class="col-md-4">Amount Due: &nbsp;</label>
                                                <div class="input-group col-md-8">
                                                    <div class="input-group-addon currency">৳</div>
                                                    <input value="" type="number" min="0" step="any" class="form-control amountDue" name="total"  id="amountDue" placeholder="Amount Due" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4">Reference By :</label>
                                                <div class="input-group col-md-8">
                                                {{Form::select('fk_reference_id',$reference,$history->fk_reference_id,['class'=>'form-control select','placeholder'=>'Select Receiver'])}}
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
	
<script src="{{asset('public/plugins/jquery/jquery-1.9.1.min.js')}}"></script>
<script src="{{asset('public/custom_js/script_sales.js')}}"></script> 

@endsection
