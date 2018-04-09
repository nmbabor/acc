	<?php $__env->startSection('content'); ?>
    <style type="text/css">
        .modal-dialog{width: 900px;}
        .invoice-note {margin-top: 35px;}
        .printable {display: none;}
        .customerInfo p{margin-bottom: 2px;}
        .customerInfo, .invoiceInfo{float: left;padding: 0 10px;}
        .table{margin-bottom: 0px;}
        .invoice>div:not(.invoice-footer){margin-bottom: 5px;}
        @media  print {
            .col-md-6{width: 50%;float: left;}
            .alert {display: none;}
            .reflink {display: none;}
            .refId {display: inline-block;}
            .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{padding: 2px;}
          @page  {
          size: auto;   /* auto is the initial value */
          margin: 5mm;  /* this affects the margin in the printer settings */
        }
        }
    </style>
		<!-- begin #content -->
		<div id="content" class="content">
			
			<!-- begin invoice -->
			<div class="invoice">
                <div class="invoice-company">
                    <span class="pull-right hidden-print">
                   <a href="javascript:;" onclick="printPage('print_body')" class="printbtn btn btn-sm btn-success m-b-10"><i class="fa fa-print m-r-5"></i> Print</a>
                    <a href="<?php echo e(URL::to('inventory-order-payment/create')); ?>" class="btn btn-sm btn-info m-b-10"><i class="fa fa-plus m-r-5"></i> New Payment </a>
                    </span>
                </div>
                <div id="print_body" style="width: 100%;overflow: hidden; padding: 10px 20px;">
                

             <?php echo $__env->make('pad.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                     <h4 style="width: 100%;text-align: center;margin-top: 15px;"><b style="padding: 10px 20px;border-radius: 10px;color: #000;border:1px solid #ddd;">Supplier Payment</b>
                        <br>
                        <br>
                        </h4>
                    <div id="customer_info">
                    <div class="row">
                        <div class="customerInfo" style="width: 60%;float: left;">
                            <p><b><u>Supplier Information</u></b></p>
                            <p><b>Supplier Name : </b><?php echo e($data->supplier->company_name); ?></p>
                            <p><b>Address : </b> <?php echo e($data->supplier->address); ?></p>
                            <p><b>Mobile No :</b> <?php echo e($data->supplier->mobile_no); ?></p>
                            <p><b>Customer Id :</b> <?php echo e($data->supplier->supplier_id); ?></p>
                            <p><b>Email : </b> <?php echo e($data->supplier->email_id); ?></p>
                            <p><b></b> </p>
                        </div>
                        <div class="invoiceInfo" style="width: 40%;float: left;">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Invoice #: </th>
                                    <th><?php echo e($data->invoice_id); ?> </th>
                                </th>
                                </tr>
                                 <tr>
                                    <td>Payment Date : </td>
                                    <td><?php echo e(date('d-m-Y',strtotime($data->payment_date))); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div> 
                
                <div class="invoice-content">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">SL</th>
                                    <th>Purchase ID</th>
                                    <th>Total Amount</th>
                                    <th>Due Amount</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                            <? $i=0; ?>
                                <?php if(isset($data->item)): ?>
                                <?php $__currentLoopData = $data->item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <? $i++; ?>
                                <tr>
                                    <td><?php echo e($i); ?></td>
                                    <td><?php echo e($product->order->inventory_order_id); ?></td>
                                    <td><?php echo e(round($product->order->total_amount,2)); ?></td>
                                    <td><?php echo e(round($product->order_last_due,2)); ?></td>
                                    <td><?php echo e(round($product->order_paid,2)); ?></td>
                                    <td><?php echo e(round($product->order_last_due-$product->order_paid,2)); ?> </td>
                                    
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php endif; ?>
                                <tr class="success">
                                    <th colspan="3" style="text-align: right;">Total=</th>
                                    <th><?php echo e(round($data->last_due,2)); ?></th>
                                    <th><?php echo e(round($data->paid,2)); ?></th>
                                    <th><?php echo e(round($data->last_due-$data->paid,2)); ?></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <div class="printFooter" style="overflow: hidden;width: 100%;">
                   <?php echo $__env->make('pad.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
               </div>
               </div>
                
            </div>

			<!-- end invoice -->
		</div>
		<!-- end #content -->
    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<script src="<?php echo e(asset('public/custom_js/printThis.js')); ?>"></script>
<script type="text/javascript">
   $(document).ready(function(){
        $('#signature1').html("Prepared By <br> <?php echo e($data->user->name); ?>");
        $('#print_body').printThis();
    })
    function printPage(id){
        $('#'+id).printThis();
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>