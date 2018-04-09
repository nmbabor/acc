<div id="customer_info" style="padding: 0 10px;">
    <div class="row">
        <div class="invoiceInfo" style="width: 100%;">
            <p align="center"><b>Report : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><?php echo e($input['start_date']); ?></u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TO  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><?php echo e($input['end_date']); ?></u></b></p>
            <table width="100%">
                <tr>
                    <td><b>Name : </b> <? echo $client->client_name ?></td>
                    <td><b>Mobile : </b> <? echo $client->mobile_no ?></td>
                    <td><b>Address : </b> <? echo $client->address ?></td>
                    <td><b>Email : </b> <? echo $client->email_id ?></td>
                </tr>
            </table>
            <div>
            <br>
            </div>
        </div>
    </div>
</div> 
<div class="invoice-content">
    <div class="table-responsive">
        <div class="row">
            <div class="col-md-12">  
                <table class="table table-bordered smallHr">
                    <thead>
                        <tr>
                            <th width="5%">SL</th>
                            <th width="15%">Date</th>
                            <th width="15%">Invoice ID</th>
                            <th>Buy Amount</th>
                            <th>Paid Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $previous= $prev->total_amount-$prev->paid_amount;
                        ?>
                        <tr>
                            <td>1</td>
                            <th colspan="2">Previous Balance</th>
                            <th><?php echo e(($previous>0)?$previous:''); ?></th>
                            <th></th>
                        </tr>
                    <? $i=1;
                        $total=$previous;
                        $paid=0;
                     ?>
                <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <? $i++;
                        $total+=$data->total_amount;
                        $paid+=$data->paid;
                     ?>
                    <tr>
                        <td><?php echo e($i); ?></td>
                        <td><?php echo e(date('d-m-Y',strtotime($data->date))); ?></td>
                        <td><?php echo e($data->invoice_id); ?></td>
                        <td><?php echo e(round($data->total_amount,3)); ?></td>
                        <td><?php echo e(round($data->paid,3)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $payment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <? $i++;
                        $paid+=$pay->paid;
                     ?>
                    <tr>
                        <td><?php echo e($i); ?></td>
                        <td><?php echo e(date('d-m-Y',strtotime($pay->payment_date))); ?></td>
                        <td><?php echo e($pay->invoice_id); ?></td>
                        <td></td>
                        <td><?php echo e(round($pay->paid,3)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>*</td>
                        <th colspan="2">Total = </th>
                        <th><?php echo e(round($total,3)); ?></th>
                        <th><?php echo e(round($paid,3)); ?></th>
                    </tr>
                    <tr>
                        <th colspan="4" style="text-align: right;">Due Amount = </th>
                        <th><?php echo e(round($total-$paid,3)); ?></th>
                    </tr>
                    </tbody>

                </table>
            </div>
                                  
        </div>
    </div>
</div>