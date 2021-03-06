
	@extends('layout.app')
		@section('content')
		<!-- begin #content -->
		<div id="content" class="content">
			 
			<div class="row">
			    <div class="col-md-12 no-padding">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">
                            <div class="panel-heading-btn">
                           
                                <a href="javascript:;" onclick="printPage('print_body')" class="printbtn btn btn-xs btn-success"><i class="fa fa-print"></i> Print</a>

                                
                            </div>
                            <h4 class="panel-title">Employee Salary</h4>
                        </div>
                        <div class="panel-body min-padding">
                            <form action="{{URL::to('employe-salary')}}" method="GET">
                                <div class="form-group col-md-3">
                                    <label class="col-md-12" for="Date">Select Section * :</label>
                                    <div class="col-md-12">
                                        {{Form::select('section',$section,'',['class'=>'form-control select','placeholder'=>'Select Section','id'=>'section'])}}
                                        <span class="help-text" id="section_error"></span>
                                    </div>
                                </div>
                            @if(Auth::user()->isRole('administrator'))
                                <div class="form-group col-md-3">
                                    <label class="col-md-12" for="Date">Select Branch * :</label>
                                    <div class="col-md-12">
                                        {{Form::select('branch',$branch,'',['class'=>'form-control select','placeholder'=>'All Branch','id'=>'branch'])}}
                                        <span class="help-text" id="section_error"></span>
                                    </div>
                                </div>
                            @endif
                                <div id="loadEmploye"><!-- Load Employee --></div>
                                <div class="form-group col-md-4">
                                    <label class="col-md-12" for="Date">Select Year and Month * :</label>
                                    <div class="col-md-5">
                                        <input class="form-control" type="number" min="2000" name="year" value="{{date('Y')}}" placeholder="year" data-parsley-required="true" id="year" />
                                    </div>
                                    <div class="col-md-7">
                                        {{Form::select('month',$month,date('n'),['class'=>'form-control select','required','id'=>'month'])}}
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-2">
                                    <label class="col-md-12">Click</label>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        <div id="print_body" style="width: 100%;overflow: hidden; padding:0px;">
                    @include('pad.header')

                            <div id="salary">
                                <h5 style="text-align: center">Salary for the moth of {{date('F')}}-{{date('Y')}} in all Section.</h5>
                                <table class="table table-bordered">
                                <tr class="active">
                                    <th>SL</th>
                                    <th>ID</th>
                                    <th>Employee Name</th>
                                    <th>Designation</th>
                                    <th>Section</th>
                                    <th>Salary</th>
                                    <th>Deduction</th>
                                    <th>Net Payable</th>
                                    <th class="paid_amount">Paid <button class="btn btn-xs btn-link no-print" title="Print or No print" id="paid_amount" data='1'><i class="fa fa-check text-success"></i></button></th>
                                    <th ><span class="no-print"> Action</span> <span class="printable" >Signature</span> </th>
                                </tr>
                                <? $i=0; ?>
                                @foreach($salary as $data)
                                <? $i++; ?>
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$data->employe_id}}</td>
                                    <td>{{$data->employe_name}}</td>
                                    <td>{{$data->designation}}</td>
                                    <td>{{$data->section_name}}</td>
                                    <td>{{$data->total_amount}}</td>
                                    <td>{{$data->deduction}}</td>
                                    <td>{{$data->payable_amount}}</td>
                                    <td class="paid_amount">
                                        @if($data->paid_amount!=null)
                                        {{$data->paid_amount}}
                                        @else
                                            <button type="button" class="btn btn-xs btn-warning no-print paid_btn" data-toggle="modal" data-target="#myModal-{{$data->id}}">Paid Now</button>

<!-- Modal -->
{!! Form::open(array('url' => ['employe-salary-paid',$data->id],'class'=>'form-horizontal author_form','method'=>'POST','id'=>'commentForm','role'=>'form','data-parsley-validate novalidate')) !!}
<div class="modal fade" id="myModal-{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">{{$data->employe_name}}</h4>
      </div>
      <div class="modal-body">
        <div class="col-md-6">
            <h4><u>Payment Method</u></h4>
            <div class="form-group">
                <label class="col-md-12">Select Account :</label>
                <div class="col-md-12">
                    {{Form::select('fk_account_id',$account,$data->fk_account_id,['class'=>'form-control','placeholder'=>'Select Account','required'])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-12">Select Method :</label>
                <div class="col-md-12">
                    {{Form::select('fk_method_id',$method,$data->fk_method_id,['class'=>'form-control','placeholder'=>'Select Method','required'])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-12">Ref ID # :</label>
                <div class="col-md-12">
                    <input type="text" name="ref_id" class="form-control" placeholder="Ref ID" value="{{$data->ref_id}}">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h4><u>Total Amount</u></h4>
            <? $total=$data->basic_pay+$data->house_rent+$data->medical_allowance; ?>
            <div class="form-group">
                <label class="col-md-12">Salary &amp; Benefits :</label>
                <div class="col-md-12">
                    <input type="number" name="total_amount" class="form-control" id="total" readonly value="{{$data->total_amount}}">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-12">Deduction Amount :</label>
                <div class="col-md-12">
                    <input type="number" name="deduction" class="form-control" id="deduction" value="{{$data->deduction}}" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-12">Net Payable Amount :</label>
                <div class="col-md-12">
                    <input type="number" readonly min="0" step="any" name="paid_amount" class="form-control" id="paid" required value="{{$data->payable_amount}}">
                </div>
            </div>
            
            <input type="hidden" name="fk_employe_id" value="{{$data->fk_employe_id}}">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button class="btn btn-primary">Paid</button>
      </div>
    </div>
  </div>
</div>
{!! Form::close() !!}
                                        @endif
                                    </td>

                                    <td>
                                    <div class="no-print">  
                                    <a href='{{URL::to("employe-salary/$data->id")}}' class="btn btn-success btn-xs"> <i class="fa fa-eye"></i></a>

                                    <a href='{{URL::to("employe-salary/$data->id/edit")}}' class="btn btn-primary btn-xs"> <i class="fa fa-pencil-square"></i></a>
                                    {!! Form::open(array('route'=> ['employe-salary.destroy',$data->id],'method'=>'DELETE')) !!}
                                        {{ Form::hidden('id',$data->id)}}
                                        <button type="submit" onclick="return confirmDelete();" class="btn btn-xs btn-danger">
                                          <i class="fa fa-trash-o"></i>
                                        </button>
                                    {!! Form::close() !!}

                                    </div>
                                    </td>
                                </tr>
                                @endforeach
                                    
                                </table>
                            </div><!-- /Salary -->
                            <div class="printFooter" style="overflow: hidden;width: 100%;">
                                @include('pad.footer')
                            </div>
                        </div><!-- /Print Body -->

                        </div>
                    </div>
			    </div>
			</div>
		</div>
		<!-- end #content -->

    @endsection
@section('script')
<script type="text/javascript">
   $(document).ready(function(){
        $('#signature3').html("Managing Director");
    })
    function salary(){
        var section=$('#section').val();
        var year=$('#year').val();
        var month=$('#month').val();
        var branch=$('#branch').val();

        $('#salary').load('{{URL::to("employe-salary-sheet")}}?section='+section+'&year='+year+'&month='+month+'&branch='+branch);
            
        
    }
    $(document).on('click','#paid_amount',function(){
        var data = $(this).attr('data');
        if(data==1){
            $(this).attr('data',0);
            $(this).html('<i class="fa fa-times text-danger"></i>');
            $('.paid_amount').each(function(){
                $(this).addClass('no-print');
            });

        }else{
            $(this).attr('data',1);
            $(this).html('<i class="fa fa-check text-success"></i>');
        }
    });
    //$(".paid_btn").fadeIn(500).fadeOut(500).fadeIn(500).fadeOut(500).fadeIn(500);
</script>
<script src="{{asset('public/custom_js/printThis.js')}}"></script>
<script type="text/javascript">
    function printPage(id){
        $('#'+id).printThis();
    }
</script>
<style type="text/css">
.paid_btn {
  -moz-animation: flash 1s ease-out;
  -moz-animation-iteration-count: infinite;

  -webkit-animation: flash 1s ease-out;
  -webkit-animation-iteration-count: infinite;

  -ms-animation: flash 1s ease-out;
  -ms-animation-iteration-count: infinite;
}

@keyframes flash {
    0% { color: #fff; }
    10% { color: #fff; }
    20% { color: #fff; }
    30% { color: #fff; }
    40% { color: #fff; }
    50% { color: #fff;}
    60% { color: #fff;}
    70% { color: #fff;}
    80% { color: red;}
    90% { color: red;}
    100% { color: red; }
}
</style>
@endsection
