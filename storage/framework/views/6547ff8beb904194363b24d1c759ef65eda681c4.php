	
		<?php $__env->startSection('content'); ?>
        <style type="text/css">
            form{display: inline;}
            .form-group{width: 100%;height: auto; overflow: hidden; display: block !important; margin: 5px;}
            .form-control{width: 100% !important;}
        </style>
		<!-- begin #content -->
		<div id="content" class="content">
			
			<div class="row">
			    <div class="col-md-12">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">
                            <div class="panel-heading-btn">
                                <a class="btn btn-xs btn-info" href="<?php echo e(URL::to('/sub-category/create')); ?>">Add New Sector</a>
                            </div>
                            <h4 class="panel-title">All Sector </h4>
                        </div>
                        <div class="panel-body">
                            <table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Sector Name</th>
                                        <th>Sector Type</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i=0; ?>
                                <?php $__currentLoopData = $categoryWiseSubCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $i++; ?>
                                    <tr class="odd gradeX">
                                        <td><?php echo e($i); ?></td>
                                        <td><?php echo e($subCategory->sub_category_name); ?></td> 
                                        <td><?php echo e(($subCategory->type==1)?'Expense Sector':'Incoming Sector'); ?></td> 
                                        <td><?php echo e($subCategory->asset->name); ?></td>
                                        <td><?php echo e(($subCategory->status==1)?'Active':'Inactive'); ?></td>
                                        <td>
                                        <!-- edit section -->
                                            <a href="ui_modal_notification.html#modal-dialog<?php echo $subCategory->id;?>" class="btn btn-sm btn-success" data-toggle="modal"><i class="fa fa-pencil-square-o" aria-hidden="true" style="font-size: 18px;"></i></a>
                                            <!-- #modal-dialog -->
                                            <div class="modal fade" id="modal-dialog<?php echo $subCategory->id;?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                    <?php echo Form::open(array('route' => ['sub-category.update',$subCategory->id],'class'=>'form-horizontal author_form','method'=>'PUT','files'=>'true', 'id'=>'commentForm','role'=>'form','data-parsley-validate novalidate')); ?>

                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h4 class="modal-title">Edit Sector</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4 col-sm-4" for="company_name"></label>
                                                                <div class="col-md-8 col-sm-8">
                                                                    <input class="form-control" type="hidden" id="company_name" name="company_name" value="<?php echo $subCategory->company_name; ?>" data-parsley-required="true" />
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4 col-sm-4" for="sub_category_name">Sector Name * :</label>
                                                                <div class="col-md-8 col-sm-8">
                                                                    <input class="form-control" type="text" id="subcategory_name" name="sub_category_name" value="<?php echo $subCategory->sub_category_name; ?>" data-parsley-required="true" />
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4 col-sm-4" for="sub_category_name">Sector Type * :</label>
                                                                <div class="col-md-8 col-sm-8">
                                                                    <?php echo e(Form::select('type',['1'=>'Expense sector','2'=>'Incoming sector'],$subCategory->type,['class'=>'form-control','placeholder'=>'Select Type','required'])); ?>

                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4 col-sm-4" for="sub_category_name">Sector Type * :</label>
                                                                <div class="col-md-8 col-sm-8">
                                                                    <?php echo e(Form::select('asset_type_id',$type,$subCategory->asset_type_id,['class'=>'form-control','placeholder'=>'Select Option','required'])); ?>

                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4 col-sm-4">Status :</label>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <div class="radio">
                                                                        <label>
                                                                            <input type="radio" name="status" value="1" id="radio-required" data-parsley-required="true" <?php if($subCategory->status=="1"): ?><?php echo e("checked"); ?><?php endif; ?>> Active
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4 col-sm-4">
                                                                    <div class="radio">
                                                                        <label>
                                                                            <input type="radio" name="status" id="radio-required2" value="0" <?php if($subCategory->status=="0"): ?><?php echo e("checked"); ?><?php endif; ?>> Inactive
                                                                        </label>
                                                                    </div>
                                                                </div> 
                                                            </div>
                                                           
                                                             
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Close</a>
                                                            <button type="submit" class="btn btn-sm btn-success">Update</button>
                                                        </div>
                                                    <?php echo Form::close();; ?>

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end edit section -->

                                            <!-- delete section -->
                                            <?php echo Form::open(array('route'=> ['sub-category.destroy',$subCategory->id],'method'=>'DELETE')); ?>

                                                <?php echo e(Form::hidden('id',$subCategory->id)); ?>

                                                <button type="submit" onclick="return confirmDelete();" class="btn btn-danger">
                                                  <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </button>
                                            <?php echo Form::close(); ?>

                                            <!-- delete section end -->
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
			    </div>
			</div>
		</div>
		<!-- end #content -->
		
    <script src="<?php echo e(asset('public/plugins/jquery/jquery-1.9.1.min.js')); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            App.init();
            TableManageResponsive.init();
        });
    </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>