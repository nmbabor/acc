	@extends('layout.app')
		@section('content')
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
                                <a class="btn btn-info btn-xs" href="{{URL::to('/payment/create')}}">Add New Create Payment</a>
                            </div>
                            <h4 class="panel-title">Payment Page </h4>
                        </div>
                        <div class="panel-body">
                            <table id="all_data" class="table table-striped table-bordered nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
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
@endsection

@section('script')

<script type="text/javascript">
    $(function() {
        $('#all_data').DataTable( {
            processing: true,
            serverSide: true,
            ajax: '{!! URL::to("payment-all") !!}',
            columns: [
                { data: 'invoice_no',name:'payment.invoice_no'},
                { data: 'client_name',name:'clients.client_name'},
                { data: 't_date'},
                { data: 'amount'},
                { data: 'total_paid'},
                { data: 'due'},
                { data: 'branch_name',name:'inventory_branch.branch_name'},
                { data: 'action'}
            ]
        });
    });
</script>
@endsection
