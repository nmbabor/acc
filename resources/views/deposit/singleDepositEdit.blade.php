
@extends('layout.app')
	@section('content')
    <style type="text/css">
        .transition_cul_section{margin-left: 0 !important; margin-right: 0 !important;}
    </style>
	<!-- begin #content -->
	<div id="content" class="content">
		
		<div class="row">
		    <div class="col-md-12">
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a class="btn btn-info btn-xs" href="{{URL::to('/deposit')}}"> View All Deposit List </a>
                            <a class="btn btn-success btn-xs" href="{{URL::to('/deposit/create')}}"> Add New Create Deposit </a>
                        </div>
                        <h4 class="panel-title">Deposit Page </h4>
                    </div>
                    <div class="panel-body">
                        {!! Form::open(array('route' => ['deposit.update',$data->id],'class'=>'form-horizontal author_form','method'=>'PUT', 'id'=>'commentForm','role'=>'form','data-parsley-validate novalidate')) !!}
                        	
							
                            <div class="row">
                                <div class="col-md-6">
                                @if($data->fk_loan_id!=null)
                                <input name="fk_loan_id" type="hidden" value="{{$data->fk_loan_id}}">
                                @endif  
                                
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4" for="Date">Date * :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control" value="<?php echo $data->t_date;?>" type="date" id="Date" name="t_date" placeholder="t_date" data-parsley-required="true" />
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4">Client Name :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <select name="fk_client_id" data-placeholder="- Select Client-" class="select form-control" tabindex="10" required="required">
                                            @foreach($getClientData as $client)
                                                <option value="{{$client->id}}" @if($client->id == $data->fk_client_id){{ "selected" }} @endif>{{$client->client_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4" for="number">Ref(#ID). :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control" type="number" id="number" value="<?php echo $data->ref_id;?>" name="ref_id" data-parsley-type="number" placeholder="Ref. Id" />
                                    </div>
                                    <input type="hidden" name="invoice_no" value="<?php echo $data->invoice_no;?>">
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4">Account :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <select name="fk_account_id" data-placeholder="- Select account-" class="chosen-select-account form-control" tabindex="10" required="required">
                                            @foreach($getAccountData as $account)
                                            <option value="{{$account->id}}" @if($account->id == $data->fk_account_id){{ "selected" }} @endif>{{$account->account_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4">Receive Method :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <select name="fk_method_id" data-placeholder="- Select method-" class="chosen-select-method form-control" tabindex="10" required="required">
                                            @foreach($getMethodData as $method)
                                            <option value="{{$method->id}}" @if($method->id == $data->fk_method_id){{ "selected" }} @endif>{{$method->method_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                                <input type="hidden" name="updated_by" value="{{ Auth::user()->id }}">
                                <input type="hidden" name="fk_deposit_id" value="{{ $data->id }}">
                                <!-- transition -->
                                <div class="view_transition_table">
                                    <div class='row'>
                                        <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                                            <table class="table table-bordered table-hover" id="table_auto">
                                                <thead>
                                                    <tr>
                                                        <th width="18%">Select Sub Category</th>
                                                        <th width="50%">Description</th>
                                                        <th width="15%">Total</th>
                                                        <th width="15%">Paid</th>
                                                        <td>
                                                             <i class="fa fa-trash text-danger"></i> 
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @if(isset($data->items))
                                                    <?php $i=0; ?>
                                                    @foreach($data->items as $item)
                                                        <?php $i++; ?>

                                                    <tr>
                                                        <td>
                                                        @if($data->fk_loan_id!=null)
                                                            
                                                           <h5 class="text-center"> {{$item->subCategory->sub_category_name}}</h5>
                                                           <input type="hidden" name="fk_sub_category_old_id[]" value="{{$item->fk_sub_category_id}}">
                                                        @else
                                                            <select name="fk_sub_category_old_id[]" data-placeholder="- Select -" class="select form-control" tabindex="10" required="required">
                                                                <?php 
                                                                foreach ($subCategories as $subCategory) {
                                                                 ?>
                                                                <option value="<?php echo $subCategory->id; ?>" @if($item->fk_sub_category_id == $subCategory->id){{ "selected" }} @endif><?php echo $subCategory->sub_category_name; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        @endif
                                                            <input type="hidden" name="deposit_item_old_id[]" id="<?php echo $item->id;?>" value="<?php echo $item->id;?>" class="deposit_item_old_id">
                                                            <input type="hidden" value="{{$item->history['id']}}" name="deposit_history_id_old[]" >
                                                        </td>
                                                        
                                                        <td>
                                                            <input type="text" data-type="description" name="description_old[]" id="description_1" value="{{$item->description}}" class="form-control autocomplete_txt" autocomplete="off" >
                                                        </td>
                                                        
                                                        <td>
                                                            <input type="number" min="0" name="total_old[]" id="price_1" class="form-control onChangeAmount" value="{{$item->total_amount}}" onKeyPress="amount()" onKeyUp="amount()">
                                                            
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="paid_old[]" id="paid_1" class="form-control onChangeAmountPaid" value="{{$item->paid_amount}}"  autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;">
                                                            
                                                        </td>
                                                        <td>
                                                            <i class="fa fa-trash"></i>
                                                        </td>
                                                        
                                                    </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="deleteItemList"></div>
                                    <div class='row'>
                                        <div class='col-xs-12'>
                                            @if($data->fk_loan_id==null)
                                                <button class="pull-right btn btn-success btn-sm addmore" type="button">+ Add More</button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class='row'>   
                                        <div class='col-xs-12 col-sm-8 col-md-8 col-lg-8'>
                                            
                                            
                                        </div>
                                        <div class='col-xs-12 col-sm-4 col-md-4 col-lg-4'>
                                            
                                            <div class="form-group transition_cul_section">
                                                <label>Total Amount: &nbsp;</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon currency">৳</div>
                                                    <input value="<?php echo intval($data->amount);?>" type="number" class="form-control" name="amount" id="total_amount" placeholder="Tatal Amount" readonly>
                                                    
                                                </div>
                                            </div>
                                            <div class="form-group transition_cul_section">
                                                <label>Paid Amount: &nbsp;</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon currency">৳</div>
                                                    <input value="<?php echo intval($data->total_paid);?>" type="number" min="0" step="0.1" class="form-control" name="total_paid" id="amountPaid" placeholder="Paid Amount" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group transition_cul_section">
                                                <label>Due Amount: &nbsp;</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon currency">৳</div>
                                                    
                                                    <input value="<?php echo intval($data->amount)-intval($data->total_paid);?>" type="number" step="0.1" class="form-control" name="due_amount" id="total-due-amount" placeholder="Due Amount" readonly>
                                                </div>
                                            </div>
                                        </span>

                                        </div>
                                    </div>
                                </div>
                            </div>
							<div class="form-group col-md-12">
                                <label class="control-label col-md-2 col-sm-2"></label>
                                <div class="col-md-8 col-sm-8">
                                    <br>
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">Update</button>
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
	
<input type="hidden" value="{{URL::to('')}}" id="rootUrl">   
@endsection
@section('script')

@include('deposit.deposit_js_setting')
@endsection
