    
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
                                <a class="btn btn-info btn-xs" href="<?php echo e(URL::to('/due-deposit/create')); ?>">Add New</a>
                            </div>
                            <h4 class="panel-title">General Income Due Receive</h4>
                        </div>
                        <div class="panel-body">
                            <table id="all_data" class="table table-striped table-bordered nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Ref Invoice.</th>
                                        <th>Client Name</th>
                                        <th>Date</th>
                                        <th>Total Amount</th>
                                        <th>Total Paid</th>
                                        <th>Total Due</th>
                                        <th>Branch</th>

                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end #content -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>

<script type="text/javascript">
    $(function() {
        $('#all_data').DataTable( {
            processing: true,
            serverSide: true,
            ajax: '<?php echo URL::to("due-deposit/show"); ?>',
            columns: [
                { data: 'invoice_id',name:'deposit_history.invoice_id'},
                { data: 'invoice_no',name:'deposit.invoice_no'},
                { data: 'client_name',name:'clients.client_name'},
                { data: 'payment_date'},
                { data: 'last_total_due'},
                { data: 'total_paid'},
                { data: 'due'},
                { data: 'branch_name',name:'inventory_branch.branch_name'},
                { data: 'action'}
            ]
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>