<?php $__env->startSection('content'); ?>
<div id="content" class="content">
	<div class="row">
	    <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a class="btn btn-info btn-xs" href="<?php echo e(URL::to('new-sms')); ?>"> Manually sms send </a>
                        <button class="btn btn-warning btn-xs" type="button">
                          Credit <span class="badge"><?php echo e($quantity); ?></span>
                        </button>
                        
                    </div>
                    <h4 class="panel-title">Branch wise sms send </h4>
                </div>
                <div class="panel-body">
                    <?php echo Form::open(['route'=>'sms.store','class'=>'from-horizontals','method'=>'POST','role'=>'form','data-toggle'=>'validator']); ?>

                        <div class="report_filler">
                            <div class="row">
                                <div class="form-group col-md-12 no-padding">
                                     <label class="control-label col-md-2" for="Date">Select Area :</label>
                                    <div class="col-md-4">
                                       <?php echo e(Form::select('branch',$branch,'',['class'=>'form-control select','placeholder'=>'Select a branch','onchange'=>"loadClient(this.value)",'required'])); ?>

                                       <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        
                        <div id="loadData">
                            <?php if(isset($customers)): ?>
                                <table id="data-table" class="table table-striped table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Customer ID</th>
                                            <th>Customer Name</th>
                                            <th>Mobile Number</th>
                                            <th><input type="checkbox" id="ckbCheckAll"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <? $i=0; ?>
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <? $i++; ?>
                                        <tr>
                                            <td><?php echo e($i); ?></td>
                                            <td><?php echo e($data->client_id); ?></td>
                                            <td><?php echo e($data->company_name); ?></td>
                                            <td><?php echo e($data->mobile_no); ?></td>
                                            <td><input type="checkbox" class="checkBoxClass"  name="fk_client_id[]" value="<?php echo e($data->id); ?>">
                                            <input type="hidden" name="number[<?php echo e($data->id); ?>]" value="<?php echo e($data->mobile_no); ?>">

                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2" for="message">Message :</label>
                            <div class="col-md-9">
                                <textarea class="form-control" id="message" name="message" placeholder="Type your message here." rows="3" required></textarea>
                                <p class="text-right"><small><span id="smsLenght">0</span> / 640</small></p>
                                        <input type="hidden" name="length" id="inputLenght">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-md-2"></label>
                            <div class="col-md-9">
                                <button type="submit" class="btn btn-warning btn-lg">Send</button>
                            </div>
                        </div>
                    <?php echo e(Form::close()); ?>


                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script type="text/javascript">
    function loadClient(id){
        var path='<?php echo e(Request::path()); ?>';
        var url='<?php echo e(URL::to("")); ?>/'+path+"?branch="+id;
        window.location.replace(url);
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#ckbCheckAll").click(function () {
            $(".checkBoxClass").prop('checked', $(this).prop('checked'));
        });
    });
    $(document).on('keypress blur keyup keydown','#message',function(){
        var value = $(this).val().length;
        $('#smsLenght').html(value);
        $('#inputLenght').val(value);
    })
    $("textarea#message[maxlength]").on("propertychange input", function() {
        if (this.value.length > this.maxlength) {
            this.value = this.value.substring(0, this.maxlength);
        }  
    });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>