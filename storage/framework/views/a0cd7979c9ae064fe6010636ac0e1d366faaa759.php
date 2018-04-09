	
		<?php $__env->startSection('content'); ?>
		<!-- begin #content -->
		<div id="content" class="content">
			
			<div class="row">
			    <div class="col-md-12">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">
                            <div class="panel-heading-btn">
                                <?php if(isset($client)): ?>
                                    <a href="javascript:;" onclick="printPage('print_body')" class="btn btn-xs btn-success"><i class="fa fa-print"></i> Print</a>
                                <?php else: ?>
                                	<a class="btn btn-info btn-xs" href="<?php echo e(URL::to('/inventory-return')); ?>">View All</a>
                                <?php endif; ?>
                            </div>
                            <h4 class="panel-title">Product Return</h4>
                        </div>
                        <div class="panel-body">
                           
                            <?php echo Form::open(array('url' =>'inventory-return/create','class'=>'form-horizontals','method'=>'GET','role'=>'form','data-parsley-validate novalidate')); ?>

                                <div class="col-md-4 no-padding">
                                    <div class="form-group">
                                        <label class="col-md-12" for="client">Select Organization :</label>
                                        <div class="col-md-12">
                                            <? $id= isset($client)?$client->id:''?>
                                            <?php echo e(Form::select('id',$clients,$id,['class'=>'form-control select','placeholder'=>'Select Organization','required'])); ?>

                                        </div>
                                    </div>                                    
                                </div>
                                <div class="col-md-3 no-padding">
                                    <div class="form-group">
                                        <label class="col-md-12" for="from">From :</label>
                                        <div class="col-md-12">
                                            
                                            <?php echo e(Form::text('from',date('d-m-Y'),['class'=>'form-control datepicker'])); ?>

                                        </div>
                                    </div>                                    
                                </div>
                                <div class="col-md-3 no-padding">
                                    <div class="form-group">
                                        <label class="col-md-12" for="to">To :</label>
                                        <div class="col-md-12">
                                            <?php echo e(Form::text('to',date('d-m-Y'),['class'=>'form-control datepicker'])); ?>

                                        </div>
                                    </div>                                    
                                </div>
                                <div class="col-md-2 no-padding">
                            	    <div class="form-group">
                                        <label class="col-md-12" for="submit">&nbsp;</label>
                                        <div class="col-md-12">
                                            <button class="btn btn-success">Submit</button>
                                        </div>
                                    </div>                                    
                                </div>
                        <?php echo e(Form::close()); ?>

                            <div class="col-md-12">
                                <br>
                                <hr class="min">
                            </div>
                        <?php if(isset($client)): ?>
                        <?php if(count($sales)>0): ?>
                        <?php echo Form::open(array('route' => 'inventory-return.store','class'=>'form-horizontal author_form','method'=>'POST','files'=>'true', 'id'=>'commentForm','role'=>'form','data-parsley-validate novalidate')); ?>

                        <div id="print_body" style="width: 100%;overflow: hidden; padding: 10px 20px;">
                          <?php echo $__env->make('pad.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <table width="100%">
                                <tr>
                                    <td><b>Organization Name : </b> <? echo $client->company_name ?></td>
                                    <td><b>Mobile : </b> <? echo $client->mobile_no ?></td>
                                    <td><b>Address : </b> <? echo $client->address ?></td>
                                    <td><b>Email : </b> <? echo $client->email_id ?></td>
                                </tr>
                            </table>
                            <br>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="success">
                                        <th width="5%">SL</th>
                                        <th>Invoice ID</th>
                                        <th>Date</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Due</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <? 
                                $i=0;
                                ?>
                                <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <? 
                                $i++;
                                ?>
                                <tr>
                                    <td><?php echo e($i); ?></td>
                                    <td><a href='<?php echo e(URL::to("inventory-sales-invoice/$data->invoice_id")); ?>'><?php echo e($data->invoice_id); ?></a> </td>
                                    <td><?php echo e($data->date); ?></td>
                                    <td><?php echo e(round($data->total_amount,3)); ?>

                                    </td>
                                    <td><?php echo e(round($data->paid_amount,3)); ?></td>
                                    <td><?php echo e(round($data->total_amount-$data->paid_amount,3)); ?></td>
                                    <td>
                                        <a href='<?php echo e(URL::to("inventory-return/$data->id")); ?>' class="btn btn-xs btn-info"> <i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                                <input type="hidden" name="fk_sales_id[]" value="<?php echo e($data->id); ?>">
                                <input type="hidden" name="sales_last_due[]" value="<?php echo e($data->total_amount-$data->paid_amount); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                               
                            </tbody>
                        </table>
                        <div class="printFooter" style="overflow: hidden;width: 100%;">
                           <?php echo $__env->make('pad.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                       </div>
                        </div><!-- / print-body -->
                      
                        <input type="hidden" name="fk_client_id" value="<?php echo e($client->id); ?>">
                        <div class="col-md-6"></div>
                        <div class="col-md-6"></div>
                        <?php echo e(Form::close()); ?>

                        <?php else: ?>
                        <table width="100%">
                            <tr>
                                <td><b>Name : </b> <? echo $client->client_name ?></td>
                                <td><b>Mobile : </b> <? echo $client->mobile_no ?></td>
                                <td><b>Address : </b> <? echo $client->address ?></td>
                                <td><b>Email : </b> <? echo $client->email_id ?></td>
                            </tr>
                        </table>
                        <h2 class="text-center text-success">Here has no invoice.</h2>
                        <?php endif; ?>
                        <?php endif; ?>
                        </div>
                    </div>
			    </div>
			</div>
		</div>
		<!-- end #content -->
		
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('script'); ?>

<script src="<?php echo e(asset('public/custom_js/printThis.js')); ?>"></script>
<script type="text/javascript">
    $('#amountPaid').on('change keyup blur',function(){
        var total=$('#subTotal').val();
        var paid =$(this).val();
        $('#amountDue').val(total-paid);
    })
    function printPage(id){
        $('#'+id).printThis();
    }
    /*function loadDue(id){
        window.location='<?php echo e(URL::to("/inventory-return")); ?>/'+id;
    }*/
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>