@extends('layouts.student')

@section('custom-css')
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">
@endsection


@section('content')
<h2 class="mb-1 text-grey">Student Profile</h2>
<hr class="mt-0 mb-5"/>


{{ Form::open(['url'=>'/student/update-profile','method'=>'post']) }} 
<div class="group-section">
    <h4 class="text-primary">Personal Information</h4>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                    {!!Form::label('full_name', 'Full Name')!!}
                    {!! Form::text('full_name', $student->full_name, ['class'=>'form-control','id'=>'full_name','disabled'=>'disabled']) !!}              
            </div>  
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-group">
                    {!!Form::label('name_marking', 'Name Marking')!!}
                    {!! Form::text('name_marking', $student->name_marking, ['class'=>'form-control','id'=>'name_marking','disabled'=>'disabled']) !!}              
            </div>  
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-group">
                    {!!Form::label('initials', 'Initials')!!}
                    {!! Form::text('initials', $student->initials, ['class'=>'form-control','id'=>'initials','disabled'=>'disabled']) !!}              
            </div>  
        </div>
        <div class="col-lg-6">
            <div class="form-group"> 
                    {!!Form::label('full_name_sinhala', 'Full Name in Sinhala')!!}
                    {!! Form::text('full_name_sinhala', $student->full_name_sinhala, ['class'=>'form-control','id'=>'full_name_sinhala']) !!}              
            </div>  
        </div>
        <div class="col-lg-6">
            <div class="form-group"> 
                    {!!Form::label('full_name_tamil', 'Full Name in Tamil')!!}
                    {!! Form::text('full_name_tamil', $student->full_name_tamil, ['class'=>'form-control','id'=>'full_name_tamil']) !!}              
            </div>  
        </div>  
        <div class="col-md-6 col-lg-3">
            <div class="form-group">
                {!!Form::label('gender', 'Gender',['class'=>'d-block'])!!}
                <div class="custom-control custom-radio custom-control-inline">
                {!! Form::radio('gender','0',($student->gender==1)?true:false, ['class'=>'custom-control-input','id'=>'gender_0','disabled'=>'disabled']) !!}
                {!!Form::label('gender_0', 'Male',['class'=>'custom-control-label'])!!}
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    {!! Form::radio('gender','1',($student->gender==2)?true:false, ['class'=>'custom-control-input','id'=>'gender_1','disabled'=>'disabled']) !!}
                    {!!Form::label('gender_1', 'Female',['class'=>'custom-control-label'])!!}
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-group">
                    {!!Form::label('id_no', 'ID Number')!!}
                    {!! Form::text('id_no', $student->id_no, ['class'=>'form-control','id'=>'id_no','disabled'=>'disabled']) !!}              
            </div>  
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-group">
                    {!!Form::label('dob', 'Date of Birth')!!}
                    {!! Form::text('dob', $student->dob, ['class'=>'form-control','id'=>'dob','required'=>'required']) !!}              
            </div>  
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-group">
                {!!Form::label('writing_hand', 'Writing Hand',['class'=>'d-block'])!!}
                <div class="custom-control custom-radio custom-control-inline">
                    {!! Form::radio('writing_hand','0',($student->writing_hand==0)?true:false, ['class'=>'custom-control-input','id'=>'writing_hand_1','required'=>'required']) !!}
                    {!!Form::label('writing_hand_1', 'Right',['class'=>'custom-control-label'])!!}
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    {!! Form::radio('writing_hand','1',($student->writing_hand==1)?true:false, ['class'=>'custom-control-input','id'=>'writing_hand_2','required'=>'required']) !!}
                    {!!Form::label('writing_hand_2', 'Left',['class'=>'custom-control-label'])!!}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <div class="form-group">
                    {!!Form::label('race', 'Race')!!}
                    {!! Form::text('race', $student->race, ['class'=>'form-control','id'=>'race','required'=>'required']) !!}              
            </div>  
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-group">
                    {!!Form::label('religion', 'Religion')!!}
                    {!! Form::text('religion', $student->religion, ['class'=>'form-control','id'=>'religion','required'=>'required']) !!}              
            </div>  
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-group">
                    {!!Form::label('citizenship', 'Citizenship')!!}
                    {!! Form::text('citizenship', $student->citizenship, ['class'=>'form-control','id'=>'citizenship','required'=>'required']) !!}              
            </div>  
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-group">
                {!!Form::label('citizenship_type', 'Citizenship Type',['class'=>'d-block'])!!}
                <div class="custom-control custom-radio custom-control-inline">
                {!! Form::radio('citizenship_type','0',($student->citizenship_type==1)?true:false, ['class'=>'custom-control-input','id'=>'citizenship_type_0','required'=>'required']) !!}
                {!!Form::label('citizenship_type_0', 'By Descent',['class'=>'custom-control-label'])!!}
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    {!! Form::radio('citizenship_type','1',($student->citizenship_type==2)?true:false, ['class'=>'custom-control-input','id'=>'citizenship_type_1','required'=>'required']) !!}
                    {!!Form::label('citizenship_type_1', 'By Registration',['class'=>'custom-control-label'])!!}
                </div>
            </div>   
        </div>
    </div>
</div>
<div class="group-section">
    <h4 class="text-primary">Contact Information</h4>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                    {!!Form::label('mobile', 'Mobile Phone no')!!}
                    {!! Form::text('mobile', $student->contact->mobile, ['class'=>'form-control','id'=>'mobile','required'=>'required']) !!}              
            </div>  
        </div>
        <div class="col-md-4">
            <div class="form-group">
                    {!!Form::label('phone', 'Land Phone no')!!}
                    {!! Form::text('phone', $student->contact->phone, ['class'=>'form-control','id'=>'phone','disabled'=>'disabled']) !!}              
            </div>  
        </div>
        <div class="col-md-4">
            <div class="form-group">
                    {!!Form::label('email', 'E Mail')!!}
                    {!! Form::text('email', $student->contact->email, ['class'=>'form-control','id'=>'email','disabled'=>'disabled']) !!}              
            </div>  
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="group-section">                
                <h6 class="text-danger">Permanent Address</h6>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mt-4">
                                {!!Form::label('address1', 'Address I')!!}
                                {!! Form::text('address1', $student->contact->address1, ['class'=>'form-control','id'=>'address1','disabled'=>'disabled']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                                {!!Form::label('address2', 'Address II')!!}
                                {!! Form::text('address2', $student->contact->address2, ['class'=>'form-control','id'=>'address2','disabled'=>'disabled']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                                {!!Form::label('address3', 'Address III')!!}
                                {!! Form::text('address3', $student->contact->address3, ['class'=>'form-control','id'=>'address3','disabled'=>'disabled']) !!}              
                        </div>  
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="group-section">
                <h6 class="text-danger">Contact Address</h6>
                <div class="col-md-12 text-right">
                    <div class="custom-control custom-switch  pull-right">
                        <input type="checkbox" class="custom-control-input" id="same_address">
                        <label class="custom-control-label" for="same_address">Contact addreess same as permenant</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                                {!!Form::label('contact_address1', 'Address I')!!}
                                {!! Form::text('contact_address1', $student->contact->contact_address1, ['class'=>'form-control','id'=>'contact_address1','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                                {!!Form::label('contact_address2', 'Address II')!!}
                                {!! Form::text('contact_address2', $student->contact->contact_address2, ['class'=>'form-control','id'=>'contact_address2']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                                {!!Form::label('contact_address3', 'Address III')!!}
                                {!! Form::text('contact_address3', $student->contact->contact_address3, ['class'=>'form-control','id'=>'contact_address3']) !!}              
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                    {!!Form::label('district', 'District')!!}
                    {!! Form::text('district', $student->contact->district, ['class'=>'form-control','id'=>'district','disabled'=>'disabled']) !!}              
            </div>  
        </div>
        <div class="col-md-3">
            <div class="form-group">
                    {!!Form::label('gn_division', 'Grama Niladhari Division')!!}
                    {!! Form::text('gn_division', $student->contact->gn_division, ['class'=>'form-control','id'=>'gn_division','required'=>'required']) !!}              
            </div>  
        </div>
        <div class="col-md-3">
            <div class="form-group">
                    {!!Form::label('electorate', 'Electorate')!!}
                    {!! Form::text('electorate', $student->contact->electorate, ['class'=>'form-control','id'=>'electorate','required'=>'required']) !!}              
            </div>  
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                    {!!Form::label('moh_area', 'MOH')!!}
                    {!! Form::text('moh_area', $student->contact->moh_area, ['class'=>'form-control','id'=>'moh_area','required'=>'required']) !!}              
            </div>  
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="group-section">
                <h6 class="text-danger">Father / Mother / Guardian Details</h6>
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            {!!Form::label('gaurdian_type', 'Information for :  ',['class'=>'mr-5'])!!}
                            <div class="custom-control custom-radio custom-control-inline">
                            {!! Form::radio('gaurdian_type','1',(!empty($student->gaurdian) && $student->gaurdian->type==1)?true:false, ['class'=>'custom-control-input','id'=>'gaurdian_type_1','required'=>'required']) !!}
                            {!!Form::label('gaurdian_type_1', 'Father',['class'=>'custom-control-label'])!!}
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                {!! Form::radio('gaurdian_type','2',(!empty($student->gaurdian) &&  $student->gaurdian->type==2)?true:false, ['class'=>'custom-control-input','id'=>'gaurdian_type_2','required'=>'required']) !!}
                                {!!Form::label('gaurdian_type_2', 'Mother',['class'=>'custom-control-label'])!!}
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                {!! Form::radio('gaurdian_type','3',(!empty($student->gaurdian) &&  $student->gaurdian->type==3)?true:false, ['class'=>'custom-control-input','id'=>'gaurdian_type_3','required'=>'required']) !!}
                                {!!Form::label('gaurdian_type_3', 'Guardian',['class'=>'custom-control-label'])!!}
                            </div>
                        </div>   
                    </div>
                </div> 
                <div class="row">
                    <div class="col-md-6 col-lg-3">
                        <div class="form-group">
                            {!!Form::label('gaurdian_name', 'Name')!!}
                            {!! Form::text('gaurdian_name', (!empty($student->gaurdian)?$student->gaurdian->full_name:''), ['class'=>'form-control','id'=>'gaurdian_name','required'=>'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="form-group">
                            {!!Form::label('gaurdian_occupation', 'Occupation')!!}
                            {!! Form::text('gaurdian_occupation', (!empty($student->gaurdian)?$student->gaurdian->occupation:''), ['class'=>'form-control','id'=>'gaurdian_occupation','required'=>'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="form-group">
                            {!!Form::label('gaurdian_address', 'Work Place Address')!!}
                            {!! Form::text('gaurdian_address', (!empty($student->gaurdian)?$student->gaurdian->address:''), ['class'=>'form-control','id'=>'gaurdian_address','required'=>'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="form-group">
                            {!!Form::label('gaurdian_phone', 'Contact No')!!}
                            {!! Form::text('gaurdian_phone', (!empty($student->gaurdian)?$student->gaurdian->phone:''), ['class'=>'form-control','id'=>'gaurdian_phone','required'=>'required']) !!}
                        </div>
                    </div>
                </div>                              
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col-md-12">
        <div class="group-section">
            <h6 class="text-danger">Emergency Contact Details</h6>
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="form-group">
                        {!!Form::label('gaurdian_emergency_c_name', 'Name of Contact Person')!!}
                        {!! Form::text('gaurdian_emergency_c_name', (!empty($student->gaurdian)?$student->gaurdian->emergency_c_name:''), ['class'=>'form-control','id'=>'gaurdian_emergency_c_name','required'=>'required']) !!}
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="form-group">
                        {!!Form::label('gaurdian_emergency_c_phone', 'Phone No')!!}
                        {!! Form::text('gaurdian_emergency_c_phone', (!empty($student->gaurdian)?$student->gaurdian->emergency_c_phone:''), ['class'=>'form-control','id'=>'gaurdian_emergency_c_phone','required'=>'required']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<div class="group-section">
    <h4 class="text-primary">A/L Information</h4>
    <div class="row">
        <div class="col-md-6 col-lg-3">
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
        <div class="col-md-6 col-lg-3">
            <div class="form-group"> 
                    {!!Form::label('attempt', 'Attempt')!!}
                    {!! Form::select('attempt',[1=>1,2=>2,3=>3], $student->AL->attempt, ['class'=>'form-control','id'=>'alattempt']) !!}              
            </div>  
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="form-group"> 
                    {!!Form::label('zscore', 'Z-Score')!!}
                    {!! Form::text('zscore', $student->AL->zscore, ['class'=>'form-control','id'=>'alzscore','disabled'=>'disabled']) !!}              
            </div>  
        </div>
        <div class="col-lg-12">
            <table width="100%">
                <tr>
                    <th width="10%"></th>
                    <th width="75%">Subject</th>
                    <th>Results</th>
                </tr>
                @for($i = 1; $i<7; $i++)
                    <tr class="text-center">
                        <td>{{$i}}</td>
                        <td>{!! Form::select('subject'.$i,$alSubjects, isset($student->AL->{'subject'.$i})?$student->AL->{'subject'.$i}:'-1', ['class'=>'form-control','id'=>'alsubject'.$i]) !!}</td>
                        <td>{!! Form::select('result'.$i, [''=>'','A'=>'A','B'=>'B','C'=>'C','D'=>'D','S'=>'S','F'=>'F','W'=>'W','AB'=>'AB'], $student->AL->{'result'.$i}, ['class'=>'form-control','id'=>'alresult'.$i]) !!}</td>
                    </tr>
                @endfor
            </table>
        </div>
    </div>

</div>
<div class="text-right">
    <button type="submit" class="btn btn-success">Update Profile</button>
</div>
{{ Form::close() }}        
@endsection


@section('custom-js')
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#dob').datepicker({
        viewMode: "years", 
        format:'yyyy-mm-dd'
    });

    $('#same_address').on('change',function(e){
        e.preventDefault();
        if($(this).prop('checked')){
            $('#contact_address1').val($('#address1').val());
            $('#contact_address2').val($('#address2').val());
            $('#contact_address3').val($('#address3').val());
        }else{
            $('#contact_address1').val('{{$student->contact->contact_address1}}');
            $('#contact_address2').val('{{$student->contact->contact_address2}}');
            $('#contact_address3').val('{{$student->contact->contact_address3}}');
        }
    });
   
    @if(Auth::user()->is_profile_confirmed==1){
        $('#dob').prop("disabled",true);
        $('#race').prop("disabled",true);
        $('#religion').prop("disabled",true);
        $('#citizenship').prop("disabled",true);

        $('#writing_hand_0').prop("disabled",true);
        $('#writing_hand_1').prop("disabled",true);
        $('#citizenship_type_0').prop("disabled",true);
        $('#citizenship_type_1').prop("disabled",true);

        $('#gaurdian_type_0').prop("disabled",true);
        $('#gaurdian_type_1').prop("disabled",true);
        $('#gaurdian_type_2').prop("disabled",true);

        $('#contact_address1').prop("disabled",true);
        $('#contact_address2').prop("disabled",true);
        $('#contact_address3').prop("disabled",true);
        $('#gn_division').prop("disabled",true);
        $('#moh_area').prop("disabled",true);
        $('#electorate').prop("disabled",true);

        $('#gaurdian_name').prop("disabled",true);
        $('#gaurdian_phone').prop("disabled",true);
        $('#gaurdian_emergency_c_name').prop("disabled",true);

        $('#alattempt').prop("disabled",true);
        $('#alsubject1').prop("disabled",true);
        $('#alsubject2').prop("disabled",true);
        $('#alsubject3').prop("disabled",true);
        $('#alsubject4').prop("disabled",true);
        $('#alsubject5').prop("disabled",true);
        $('#alsubject6').prop("disabled",true);
        $('#alresult1').prop("disabled",true);
        $('#alresult2').prop("disabled",true);
        $('#alresult3').prop("disabled",true);
        $('#alresult4').prop("disabled",true);
        $('#alresult5').prop("disabled",true);
        $('#alresult6').prop("disabled",true);
        
    }@endif

    // $('#student_list').change(function(e){
    //     var fileName = e.target.files[0].name;
    //     $('#student_list_label').html(fileName);
    // });

    // $('#show_advance_options').prop('checked',false);

    // $('#show_advance_options').change(function() {
    //     if($(this).prop('checked')){
    //         $('#advance_options').fadeIn();
    //     }else{
    //         $('#advance_options').fadeOut();
    //     }
    // });

});
</script>
@endsection