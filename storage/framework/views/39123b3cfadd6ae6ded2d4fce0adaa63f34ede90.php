	<?php $__env->startSection('content'); ?>
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
                            <a class="btn btn-info btn-xs" href="<?php echo e(URL::to('/payment')); ?>">View All Payment List</a>
                            <a class="btn btn-success btn-xs" href="<?php echo e(URL::to('/payment/create')); ?>">Add New Payment</a>
                        </div>
                        <h4 class="panel-title">Payment Page</h4>
                    </div>
                    <div class="panel-body">
                        <?php echo Form::open(array('route' => ['payment.update',$data->id],'class'=>'form-horizontal author_form','method'=>'PUT','files'=>'true', 'id'=>'commentForm','role'=>'form','data-parsley-validate novalidate')); ?>

                        	
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4" for="Date">Date * :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control" value="<?php echo $data->t_date;?>" type="date" id="Date" name="t_date" placeholder="t_date" data-parsley-required="true" />
                                    </div>
                                </div>
                                <?php if($data->fk_loan_id!=null): ?>
                                <input name="fk_loan_id" type="hidden" value="<?php echo e($data->fk_loan_id); ?>">
                                <?php endif; ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4">Client Name :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <select name="fk_client_id" data-placeholder="- Select Client-" class="select form-control" tabindex="10" required="required">
                                            <?php $__currentLoopData = $getClientData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($client->id); ?>" <?php if($client->id == $data->fk_client_id): ?><?php echo e("selected"); ?> <?php endif; ?>><?php echo e($client->client_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                        <select name="fk_account_id" id="fk_account_id" data-placeholder="- Select account-" class="select form-control" tabindex="10" required="required">
                                            <?php $__currentLoopData = $getAccountData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($account->id); ?>" <?php if($account->id == $data->fk_account_id): ?><?php echo e("selected"); ?> <?php endif; ?>><?php echo e($account->account_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4">Receive Method :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <select name="fk_method_id" data-placeholder="- Select method-" class="select form-control" tabindex="10" required="required">
                                            <?php $__currentLoopData = $getMethodData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($method->id); ?>" <?php if($method->id == $data->fk_method_id): ?><?php echo e("selected"); ?> <?php endif; ?>><?php echo e($method->method_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                                <input type="hidden" name="updated_by" value="<?php echo e(Auth::user()->id); ?>">
                                <input type="hidden" name="fk_payment_id" value="<?php echo e($data->id); ?>">
                                <!-- transition -->
                                <div class="view_transition_table">
                                    <div class='row'>
                                        <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                                            <table class="table table-bordered table-hover" id="table_auto">
                                                <thead>
                                                    <tr>
                                                        <th width="18%">Select Sector</th>
                                                        <?php if($data->fk_loan_id==null): ?>
                                                        <th width="15%">Asset Age</th>
                                                        <?php endif; ?>
                                                        <th width="35%">Description</th>
                                                        <th width="15%">Total</th>
                                                        <th width="15%">Paid</th>
                                                        <th width="5%"><i class="fa fa-trash text-danger"></i></th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if(isset($data->items)): ?>
                                                    <?php $i=0; ?>
                                                    <?php $__currentLoopData = $data->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php $i++; ?>

                                                    <tr>
                                                        <td>
                                                        <?php if($data->fk_loan_id!=null): ?>
                                                            
                                                           <h5 class="text-center"> <?php echo e($item->subCategory->sub_category_name); ?></h5>
                                                           <input type="hidden" name="fk_sub_category_old_id[]" value="<?php echo e($item->fk_sub_category_id); ?>">
                                                        <?php else: ?>
                                                            <select  id="sector_<?php echo e($i); ?>" name="fk_sub_category_old_id[]" class=" form-control sectorSelect" required="required" >
                                                                <?php 
                                                                foreach ($subCategories as $subCategory) {
                                                                 ?>
                                                                <option value="<?php echo $subCategory->id; ?>" <?php if($item->fk_sub_category_id == $subCategory->id): ?><?php echo e("selected"); ?> <?php endif; ?>><?php echo $subCategory->sub_category_name; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        <?php endif; ?>
                                                            <input type="hidden" name="payment_item_old_id[]" id="<?php echo $item->id;?>" value="<?php echo $item->id;?>" class="payment_item_old_id">
                                                        </td>
                                                        <?php if($data->fk_loan_id==null): ?>
                                                        <td>
                                                            <input  id="asset_<?php echo e($i); ?>" type="number" min="0" step="any" name="asset_age_old[]" placeholder="Asset age" value="<?php echo e($item->asset_age); ?>" <?php echo e(($item->asset->type==1)?'':'readonly'); ?> class="form-control">
                                                        </td>
                                                        <?php endif; ?>
                                                        <td>

                                                            <input type="hidden" value="<?php echo e($item->asset_type_id); ?>" name="asset_type_id_old[]" id="asset_type_<?php echo e($i); ?>">
                                                            <input type="hidden" value="<?php echo e($item->history['id']); ?>" name="payment_history_id_old[]" >
                                                            <input type="text" data-type="description" name="description_old[]" id="description_1" value="<?php echo e($item->description); ?>" class="form-control autocomplete_txt" autocomplete="off">
                                                            
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="total_old[]" id="price_1" class="form-control onChangeAmount" value="<?php echo e($item->total_amount); ?>" onKeyPress="amount()" onKeyUp="amount()">
                                                            
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="paid_old[]" id="paid_1" class="form-control onChangeAmountPaid" value="<?php echo e($item->paid_amount); ?>"  autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;">
                                                            
                                                        </td>
                                                        <td>
                                                            <i class="fa fa-trash"></i>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="deleteItemList"></div>
                                    <div class='row'>
                                        <div class='col-xs-12'>
                                        <?php if($data->fk_loan_id==null): ?>
                                            <button class="pull-right btn btn-success btn-sm addmore" type="button">+ Add More</button>
                                        <?php endif; ?>
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
                                                    <input value="<?php echo intval($data->total_paid);?>" type="number" min="0" max="<?php echo e(Help::account($data->fk_account_id)->balance+$data->total_paid); ?>" step="0.1" class="form-control" name="total_paid" id="amountPaid" placeholder="Paid Amount" readonly>
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
                        <?php echo Form::close();; ?>

                    </div>
                </div>
		    </div>
		</div>
	</div>
	<!-- end #content -->
	

<input type="hidden" value="<?php echo e(URL::to('')); ?>" id="rootUrl">

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<?php echo $__env->make('payment.payment_js_setting', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<script>
    $(document).on('change','#fk_account_id',function(){
        var id = $(this).val();
        $.ajax({
            url:'<?php echo e(URL::to("account-balance")); ?>/'+id,
            type:"GET",
            success:function(result){
                $('#amountPaid').attr('max',result);

            }
        })

    })
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layout.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>