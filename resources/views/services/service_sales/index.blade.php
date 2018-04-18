@extends('layout.app')
	@section('content')
    <style type="text/css">
        .transition_cul_section{margin-left: 0 !important; margin-right: 0 !important;}
        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{padding: 5px;}
    </style>
	<!-- begin #content -->
	<div id="content" class="content" style="min-height: 730px;">
		<div class="row">
		    <div class="col-md-12">
                <div class="panel panel-inverse">
                    <div class="panel-heading"> 
                    <div class="panel-heading-btn">
                     <a class="btn btn-info btn-xs" href="{{URL::to('/services-sales')}}"><i class="fa fa-bars"></i> All Sales</a>
                    </div>
                        <h4 class="panel-title">Service Sales</h4>
                    </div>
                    <div class="panel-body">
                        {!! Form::open(array('route' => 'services-sales.store','class'=>'form-horizontal author_form','method'=>'POST','files'=>'true', 'id'=>'commentForm','role'=>'form','data-parsley-validate novalidate')) !!}
                        	<div class="form-group">
								
								<input class="form-control" type="hidden" id="client" name="company_name" placeholder="Company Name" data-parsley-required="true" />

                                <input type="hidden" name="invoice_id" value="">
							</div>
							
                            <div class="">
                                <!-- info -->
                                <div class="row">
                                   <div class="form-group col-md-6 {{ $errors->has('client_name') ? 'has-error' : '' }}">
                                    <label class="col-md-4" for="client_name">Client Name * :</label>
                                    <div class="col-md-8">
                                        <input class="form-control" type="text" id="client_name" name="client_name" placeholder="Client Name" onfocus="autoComplete(this.id)" required />
                                        @if ($errors->has('client_name'))
                                            <span class="help-block" style="display:block">
                                                <strong>{{ $errors->first('client_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                    <div class="form-group col-md-6">
                                        <label class=" col-md-4" for="Date">Date * :</label>
                                        <div class="col-md-8">
                                            <input class="form-control datepicker" type="text" name="date" value="<?php echo date('d-m-Y'); ?>" placeholder="t_date" data-parsley-required="true" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div id="loadClientInfo">
                                       <!-- Load Client Info -->
                                    </div>
                                </div>
                                
                                <input type="hidden" name="created_by" value="{{ Auth::user()->id }}">
                                <!-- transition -->
                                <div class="view_center_folwchart">
                                <div class='row'>
                                    <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                                        <table class="table table-bordered table-hover" id="table_auto">
                                            <thead>
                                                <tr>
                                                    <th width="1%"><input id="check_all" type="checkbox"/></th>
                                                    <th width="20%">Service Name</th>
                                                    <th width="10%">Service Price</th>
                                                    <th width="10%">Service Qty</th>
                                                    <th width="10%">Discount</th>
                                                    <th width="19%">Sub Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input class="case" type="checkbox"/></td>

                                                    <td><input type="text" data-type="product_name" name="product_name[]" id="itemName_1" class="form-control autocomplete_txt" autocomplete="off"><input type="hidden" data-type="product_name" name="fk_product_id[]" id="itemId_1" class="form-control autocomplete_txt" autocomplete="off"></td>
                                                    <td><input tabindex="-1" type="text" name="product_price_amount[]" id="hidden_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly="readonly"></td>
                                                    <td><input type="number" min="0" step="any" name="qty[]" id="qty_1" class="form-control changesNo qty" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>

                                                    <td><input type="number" min="0" step="any" name="product_wise_discount[]" id="benefit_1" class="form-control changesNo benefit" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>

                                                    
                                                    
                                                    <td><input tabindex="-1" type="text" min="0" step="any" name="product_paid_amount[]" id="total_1" class="form-control totalLinePrice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly="readonly">

                                                    <input type="hidden" min="0" step="any" name="price[]" id="cost_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-xs-12 col-sm-4 col-md-4 col-lg-4'>
                                        <button class="btn btn-danger delete btn-xs" type="button">- Delete</button>
                                        <button class="btn btn-success addmore btn-xs" type="button">+ Add More</button>
                                    </div>
                                </div>
                                <br>
                                <div class='row'>   
                                    <div class='col-xs-12 col-sm-7 col-md-7 col-lg-7'>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Summary:</label>
                                            <div class="col-md-8">
                                          <textarea name="summary" rows="3" class="form-control "></textarea>  
                                            </div>
                                        </div>

                                    </div>
                                    <div class='col-xs-12 col-sm-5 col-md-5 col-lg-5'>
                                        <div class="form-inlines">
                                            <div class="form-group aside_system">
                                                <label class="col-md-4">Total Amount:</label>
                                                <div class="input-group col-md-8">
                                                    <div class="input-group-addon currency">৳</div>
                                                    <input value="" type="number" min="0" step="any" class="form-control" name="total_amount" id="subTotal" placeholder="Total Amount" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group aside_system">
                                                <label class="col-md-4">Paid Amount :</label>
                                                <div class="input-group col-md-8">
                                                    <div class="input-group-addon currency">৳</div>
                                                    <input value="0" type="number" min="0" step="any" class="form-control" name="paid_amount" id="amountPaid" placeholder="Paid Amount" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" required>
                                                </div>
                                            </div>
                                            <div class="form-group aside_system">
                                                <label class="col-md-4">Amount Due :</label>
                                                <div class="input-group col-md-8">
                                                    <div class="input-group-addon currency">৳</div>
                                                    <input value="" type="number" min="0" step="any" class="form-control amountDue" name="total"  id="amountDue" placeholder="Amount Due" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>

                            </div>
                            </div>

							<div class="form-group">
								<label class="control-label col-md-2 col-sm-2"></label>
								<div class="col-md-8 col-sm-8">
                                <br>
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
<script src="{{asset('public/custom_js/script_service_sales.js')}}"></script> 

<script type="text/javascript">
    function client_info($id){
        $('#loadClientInfo').load("{{URL::to('loadClientInfo')}}/"+$id);
        $('#client_help').html('');
    }
    function autoComplete(id) {

        $('#' + id).autocomplete({
            source: function (request, response) {
                $.ajax({

                    url: '{{URL::to("search-client/")}}',
                    type: "GET",
                    dataType: "json",
                    data: {
                        name: request.term,
                    },
                    success: function (data) {
                        //console.log(data);
                        response($.map(data, function (item) {
                            var code = item.split("|");
                            return {
                                label: code[0],
                                value: code[0],
                                data: item
                            }
                        }));
                    }
                });


            },

            autoFocus: true,
            minLength: 0,
            select: function (event, ui) {
                var names = ui.item.data.split("|");
                var id = $(this).attr('id');

            }
        });
    }


  
</script>

@stop