<?
$month=$input['month'];
$year=$input['year'];
?>
<h5 style="text-align: center">Salary for the moth of {{date('F',strtotime("$year-$month-01"))}}-{{$year}} in {{($input['section']!=null)?$input['section']:'all Section'}} .</h5>
<table class="table table-bordered">
    <tr class="active">
        <th>SL</th>
        <th>ID</th>
        <th>Employe Name</th>
        <th>Designation</th>
        <th>Section</th>
        <th>Salary</th>
        <th>Deduction</th>
        <th>Net Palyable</th>
        <th  class="no-print">Action</th>
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
        <td class="no-print"><a href='{{URL::to("employe-salary/$data->id")}}' class="btn btn-success btn-xs"> <i class="fa fa-eye"></i></a>

        <a href='{{URL::to("employe-salary/$data->id/edit")}}' class="btn btn-primary btn-xs"> <i class="fa fa-pencil-square"></i></a>
        {!! Form::open(array('route'=> ['employe-salary.destroy',$data->id],'method'=>'DELETE')) !!}
                    {{ Form::hidden('id',$data->id)}}
                    <button type="submit" onclick="return confirmDelete();" class="btn btn-xs btn-danger">
                      <i class="fa fa-trash-o"></i>
                    </button>
                {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
        
    </table>