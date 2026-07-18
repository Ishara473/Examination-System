{{ Form::open(['url'=>url('/admin/student/update-al-details'),'method'=>'post','id'=>'frmPersonal']) }}
{!! Form::hidden('id', $student->id) !!} 
<h5 class="alert bg-gray-700 text-white">A/L Information</h5>
<div class="row">
    <div class="col-lg-3">
        <div class="form-group"> 
                {!!Form::label('index_no', 'Index No')!!}
                {!! Form::text('index_no', $student->AL->index_no, ['class'=>'form-control','id'=>'alindex_no','disabled'=>'disabled']) !!}              
        </div>  
    </div>
    <div class="col-lg-3">
        <div class="form-group"> 
                {!!Form::label('medium', 'Medium')!!}
                {!! Form::text('medium', $student->medium, ['class'=>'form-control','id'=>'medium','disabled'=>'disabled']) !!}              
        </div>  
    </div>
    <div class="col-lg-3">
        <div class="form-group"> 
                {!!Form::label('attempt', 'Attempt')!!}
                {!! Form::select('attempt',[1=>1,2=>2,3=>3], $student->AL->attempt, ['class'=>'form-control','id'=>'alattempt']) !!}              
        </div>  
    </div>
    <div class="col-lg-3">
        <div class="form-group"> 
                {!!Form::label('zscore', 'Z-Score')!!}
                {!! Form::text('zscore', $student->AL->zscore, ['class'=>'form-control','id'=>'alzscore','disabled'=>'disabled']) !!}              
        </div>  
    </div>
</div>
<div class="row">
    <div class="col-lg-9">
        <table>
            <tr>
                <th width="10%"></th>
                <th width="75%">Subject</th>
                <th>Results</th>
            </tr>
            @for($i = 1; $i<7; $i++)
                <tr class="text-center">
                    <td>{{$i}}</td>
                    <td>{!! Form::select('subject'.$i,$alSubjects, isset($student->AL->{'subject'.$i})?$student->AL->{'subject'.$i}:'-1', ['class'=>'form-control','id'=>'alsubject'.$i]) !!}</td>
                    <td>{!! Form::text('result'.$i, $student->AL->{'result'.$i}, ['class'=>'form-control','id'=>'alresult'.$i]) !!}</td>
                </tr>
            @endfor
        </table>
    </div>
    <div class="col-lg-12 text-right">
        @if(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin'))
        <button type="submit" class="btn btn-sm btn-primary btn-icon-split" id="frmALSubmit" name="frmALSubmit"><span class="icon"><i class="fa fa-paper-plane"></i></span> <span class="text">Update</span></button>
        @endif
    </div>
</div>



{{ Form::close() }}
