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
                                <a class="btn btn-info btn-xs" href="{{URL::to('/deposit/create')}}">Add New</a>
                            </div>
                            <h4 class="panel-title">Payable Loan</h4>
                        </div>
                        <div class="panel-body">
                            <table id="all_data" class="table table-striped table-bordered nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Organization</th>
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
            ajax: '{!! URL::to("payable-loan-all") !!}',
            columns: [
                { data: 'invoice_no',name:'deposit.invoice_no'},
                { data: 'client_name',name:'clients.client_name'},
                { data: 't_date'},
                { data: 'total_paid'},
                { data: 'loan_paid'},
                { data: 'loan_due'},
                { data: 'branch_name',name:'inventory_branch.branch_name'},
                { data: 'action'}
            ]
        });
    });
</script>
@endsection
