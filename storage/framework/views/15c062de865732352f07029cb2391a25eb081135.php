	<?php $__env->startSection('content'); ?>
    <style type="text/css">
        .transition_cul_section{margin-left: 0 !important; margin-right: 0 !important;}
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            margin: 0; 
        }
    </style>
	<!-- begin #content -->
	<div id="content" class="content">
		
		<div class="row">
		    <div class="col-md-12">
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a class="btn btn-info btn-xs" href="<?php echo e(URL::to('/opening-asset')); ?>">View All</a>
                        </div>
                        <h4 class="panel-title">Opening assets </h4>
                    </div>
                    <div class="panel-body">
                        <?php echo Form::open(array('route' => 'opening-asset.store','class'=>'form-horizontal author_form','method'=>'POST','role'=>'form','data-parsley-validate novalidate')); ?>

							
                            <div class="row">
                            <div class="col-md-6">
                                
                           
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4" for="Date">Date * :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control datepicker" type="text" id="Date" name="date" placeholder="date" data-parsley-required="true" value="<?php echo e(date('d-m-Y')); ?>" />
                                    </div>
                                </div>
                             </div>
                                <!-- transition -->
                                <div class="view_transition_table" style="width: 100%">
                                    <div class='row'>
                                        <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                                            <table class="table table-bordered table-hover" id="table_auto">
                                                <thead>
                                                    <tr>
                                                        <th width="18%">Select Sector</th>
                                                        <th width="15%">Asset Age</th>
                                                        <th width="35%">Description</th>
                                                        <th width="15%">Purchase Amount</th>
                                                        <th width="15%">Current Value</th>
                                                        <th><i class="fa fa-trash text-danger"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div id="sector">
                                                               <?php echo e(Form::select('fk_sub_category_id[]',$subCategories,'',['class'=>'form-control','placeholder'=>'Select Sector','required'])); ?>

                                                            </div>
                                                        </td>
                                                        <td> 
                                                            <input  id="asset_1" type="number" min="0" step="any" name="asset_age[]" placeholder="Asset age" class="form-control">
                                                        </td>
                                                        <td>

                                                            <input type="text" data-type="description" name="description[]" id="description_1" class="form-control">
                                                            
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="total_amount[]" id="price_1" class="form-control" >
                                                            
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" name="current_amount[]" id="paid_1" class="form-control onChangeAmountPaid" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;">
                                                            
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-xs deleteBtn"> <i class="fa fa-trash"></i> </button>
                                                        </td>
                                                        
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class='col-xs-12'>
                                            <button class="pull-right btn btn-success btn-sm addmore" type="button">+ Add More</button>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

							 <div class="form-group col-md-12">
                                <label class="control-label col-md-2 col-sm-2"></label>
                                <div class="col-md-8 col-sm-8">
                                    <br>
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">Confirm</button>
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
<script type="text/javascript">
    var i=$('#table_auto tr').length;
     $(".addmore").on('click',function(){
        html = '<tr>';
        html += '<td>'+$('#sector').html()+'</td>';
        html +='<td><input  id="asset_'+i+'" type="number" min="0" step="any" name="asset_age[]" placeholder="Asset age" class="form-control"></td>';
        html += '<td><input type="text" data-type="description" name="description[]" id="description_'+i+'" class="form-control autocomplete_txt" autocomplete="off"></td>';

        html += '<td><input type="number" step="any" min="0" name="total_amount[]" id="price_'+i+'" class="form-control onChangeAmount" onKeyPress="amount(this.value,'+i+')" onKeyUp="amount(this.value,'+i+')"></td>';

        html += '<td><input type="number" step="any" min="0" name="current_amount[]" id="paid_'+i+'" class="form-control onChangeAmountPaid" autocomplete="off"></td>';
        html +='<td><button type="button" class="btn btn-danger btn-xs deleteBtn"> <i class="fa fa-trash"></i> </button></td>'
        html += '</tr>';
        $('#table_auto').append(html);
        i++;

     });
     $(document).on('click','.deleteBtn',function(){

        $(this).parents("tr").remove();
         calculateTotal();
     });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>