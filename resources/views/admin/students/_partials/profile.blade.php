{{ Form::open(['url'=>url('/admin/student/update-personal-details'),'method'=>'post','id'=>'frmPersonal']) }}
{!! Form::hidden('id', $student->id) !!} 
<h5 class="alert bg-gray-700 text-white">Personal Information</h5>
<div class="row">
    <div class="col-lg-6">
        <div class="form-group"> 
                {!!Form::label('full_name', 'Full Name')!!}
                {!! Form::text('full_name', $student->full_name, ['class'=>'form-control','id'=>'full_name']) !!}              
        </div>  
    </div>
    <div class="col-lg-3">
        <div class="form-group">
                {!!Form::label('name_marking', 'Name Marking')!!}
                {!! Form::text('name_marking', $student->name_marking, ['class'=>'form-control','id'=>'name_marking']) !!}              
        </div>  
    </div>
    <div class="col-lg-3">
        <div class="form-group">
                {!!Form::label('initials', 'Initials')!!}
                {!! Form::text('initials', $student->initials, ['class'=>'form-control','id'=>'initials']) !!}              
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
    <div class="col-lg-4">
        <div class="form-group">
                {!!Form::label('id_no', 'ID Number (Main)')!!}
                {!! Form::text('id_no', $student->id_no, ['class'=>'form-control','id'=>'id_no']) !!}              
        </div>  
    </div>
    <div class="col-lg-4">
        <div class="form-group">
                {!!Form::label('id_no_2', 'ID Number II')!!}
                {!! Form::text('id_no_2', $student->id_no_2, ['class'=>'form-control','id'=>'id_no_2']) !!}              
        </div>  
    </div>
    <div class="col-lg-4">
        <div class="form-group">
                {!!Form::label('dob', 'Date of Birth')!!}
                {!! Form::text('dob', $student->dob, ['class'=>'form-control datepicker','id'=>'dob']) !!}              
        </div>  
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            {!!Form::label('civil_status', 'Civil Status',['class'=>'d-block'])!!}
            <div class="custom-control custom-radio custom-control-inline">
                {!! Form::radio('civil_status','1',($student->civil_status==1)?true:false, ['class'=>'custom-control-input','id'=>'civil_status_1']) !!}
                {!!Form::label('civil_status_1', 'Single',['class'=>'custom-control-label'])!!}
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                {!! Form::radio('civil_status','2',($student->civil_status==2)?true:false, ['class'=>'custom-control-input','id'=>'civil_status_2']) !!}
                {!!Form::label('civil_status_2', 'Married',['class'=>'custom-control-label'])!!}
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            {!!Form::label('gender', 'Gender',['class'=>'d-block'])!!}
            <div class="custom-control custom-radio custom-control-inline">
                {!! Form::radio('gender','1',($student->gender==1)?true:false, ['class'=>'custom-control-input','id'=>'gender_1']) !!}
                {!!Form::label('gender_1', 'Male',['class'=>'custom-control-label'])!!}
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                {!! Form::radio('gender','2',($student->gender==2)?true:false, ['class'=>'custom-control-input','id'=>'gender_2']) !!}
                {!!Form::label('gender_2', 'Female',['class'=>'custom-control-label'])!!}
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            {!!Form::label('citizenship_type', 'Citizenship Type',['class'=>'d-block'])!!}
            <div class="custom-control custom-radio custom-control-inline">
            {!! Form::radio('citizenship_type','0',($student->citizenship_type==1)?true:false, ['class'=>'custom-control-input','id'=>'citizenship_type_0']) !!}
            {!!Form::label('citizenship_type_0', 'By Descent',['class'=>'custom-control-label'])!!}
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                {!! Form::radio('citizenship_type','1',($student->citizenship_type==2)?true:false, ['class'=>'custom-control-input','id'=>'citizenship_type_1']) !!}
                {!!Form::label('citizenship_type_1', 'By Registration',['class'=>'custom-control-label'])!!}
            </div>
        </div>  
    </div>
</div>
<div class="row">
    <div class="col-lg-4">
        <div class="form-group">
                {!!Form::label('race', 'Race')!!}
                {!! Form::text('race', $student->race, ['class'=>'form-control','id'=>'race']) !!}              
        </div>  
    </div>
    <div class="col-lg-4">
        <div class="form-group">
                {!!Form::label('religion', 'Religion')!!}
                {!! Form::text('religion', $student->religion, ['class'=>'form-control','id'=>'religion']) !!}              
        </div>  
    </div>
    <div class="col-lg-4">
        <div class="form-group">
                {!!Form::label('citizenship', 'Citizenship')!!}
                {!! Form::text('citizenship', $student->citizenship, ['class'=>'form-control','id'=>'citizenship']) !!}              
        </div>  
    </div>    
</div>
<div class="row">
    <div class="col-lg-4">
        <div class="form-group">
            {!!Form::label('writing_hand', 'Writing Hand',['class'=>'d-block'])!!}
            <div class="custom-control custom-radio custom-control-inline">
                {!! Form::radio('writing_hand','0',($student->writing_hand==0)?true:false, ['class'=>'custom-control-input','id'=>'writing_hand_1']) !!}
                {!!Form::label('writing_hand_1', 'Right',['class'=>'custom-control-label'])!!}
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                {!! Form::radio('writing_hand','1',($student->writing_hand==1)?true:false, ['class'=>'custom-control-input','id'=>'writing_hand_2']) !!}
                {!!Form::label('writing_hand_2', 'Left',['class'=>'custom-control-label'])!!}
            </div>
        </div>
    </div>
</div>

<h5 class="alert bg-gray-700 text-white mt-4">Contact Information</h5>
<div class="row">
    <div class="col-lg-6">
        <div class="group-section">
            <h5 class="text-secondary">Permanent Address</h5>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                            {!!Form::label('address1', 'Address I')!!}
                            {!! Form::text('address1', $student->contact->address1, ['class'=>'form-control','id'=>'address1']) !!}              
                    </div>  
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                            {!!Form::label('address2', 'Address II')!!}
                            {!! Form::text('address2', $student->contact->address2, ['class'=>'form-control','id'=>'address2']) !!}              
                    </div>  
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                            {!!Form::label('address3', 'Address III')!!}
                            {!! Form::text('address3', $student->contact->address3, ['class'=>'form-control','id'=>'address3']) !!}              
                    </div>  
                </div>
            </div>
        </div>    
    </div>
    <div class="col-lg-6">
        <div class="group-section">
            <h5 class="text-secondary">Mailing Address</h5>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                            {!!Form::label('contact_address1', 'Address I')!!}
                            {!! Form::text('contact_address1', $student->contact->contact_address1, ['class'=>'form-control','id'=>'contact_address1']) !!}              
                    </div>  
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                            {!!Form::label('contact_address2', 'Address II')!!}
                            {!! Form::text('contact_address2', $student->contact->contact_address2, ['class'=>'form-control','id'=>'contact_address2']) !!}              
                    </div>  
                </div>
                <div class="col-lg-12">
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
    <div class="col-lg-3">
        <div class="form-group">
                {!!Form::label('district', 'District')!!}
                {!! Form::text('district', $student->contact->district, ['class'=>'form-control','id'=>'district']) !!}              
        </div>  
    </div>
    <div class="col-lg-3">
        <div class="form-group">
                {!!Form::label('gn_division', 'Grama Niladhari Division')!!}
                {!! Form::text('gn_division', $student->contact->gn_division, ['class'=>'form-control','id'=>'gn_division']) !!}              
        </div>  
    </div>
    <div class="col-lg-3">
        <div class="form-group">
                {!!Form::label('electorate', 'Electorate')!!}
                {!! Form::text('electorate', $student->contact->electorate, ['class'=>'form-control','id'=>'electorate']) !!}              
        </div>  
    </div>
    <div class="col-lg-3">
        <div class="form-group">
                {!!Form::label('moh_area', 'MOH')!!}
                {!! Form::text('moh_area', $student->contact->moh_area, ['class'=>'form-control','id'=>'moh_area']) !!}              
        </div>  
    </div>
</div>
<div class="row">
    <div class="col-lg-4">
        <div class="form-group">
                {!!Form::label('mobile', 'Mobile Phone no')!!}
                {!! Form::text('mobile', $student->contact->mobile, ['class'=>'form-control','id'=>'mobile']) !!}              
        </div>  
    </div>
    <div class="col-lg-4">
        <div class="form-group">
                {!!Form::label('phone', 'Land Phone no')!!}
                {!! Form::text('phone', $student->contact->phone, ['class'=>'form-control','id'=>'phone']) !!}              
        </div>  
    </div>
    <div class="col-lg-4">
        <div class="form-group">
                {!!Form::label('email', 'E Mail')!!}
                {!! Form::text('email', $student->contact->email, ['class'=>'form-control','id'=>'email']) !!}              
        </div>  
    </div>
</div>


<h5 class="alert bg-gray-700 text-white mt-4">Parent / Gaurdian Information</h5>
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

<h5 class="alert bg-gray-700 text-white mt-4">Emergency Contact Information</h5>
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


<div class="row mb-2">
    <div class="col-lg-12 text-right">
        @if(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin'))
        <button type="submit" class="btn btn-primary btn-icon-split" id="frmPersonalSubmit" name="frmPersonalSubmit"><span class="icon"><i class="fa fa-paper-plane"></i></span> <span class="text">Update</span></button>
        @endif
    </div>
</div>
{{ Form::close() }}