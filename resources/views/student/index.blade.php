@extends('layouts.student')


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="text-primary">Academic Information</h4>
                <hr class="mb-1 mt-0 border-primary"/>
                <div class="row mb-2">
                    <div class="col-md-2">Registration No:</div>
                    <div class="col-md-4"><strong>{{$user->registration_no}}</strong></div>
                    <div class="col-md-2">Index No:</div>
                    <div class="col-md-4"><strong>{{$user->index_no}}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-2">Acedamic Year :</div>
                    <div class="col-md-4"><strong>{{$user->AcademicDetail->current_study_year}}</strong></div>
                    <div class="col-md-2">Batch :</div>
                    <div class="col-md-4"><strong>{{$user->AcademicDetail->batchCode()}}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-2">Specialization :</div>
                    <div class="col-md-10"><strong>@if($user->AcademicDetail->specialization_id > 0) {{$user->AcademicDetail->Specialization->name}}  @else - @endif</strong></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="text-primary">Personal Information</h4>
                <hr class="mb-1 mt-0 border-primary"/>
                <div class="row mb-2">
                    <div class="col-md-4">Full Name :</div>
                    <div class="col-md-8"><strong>{{$user->full_name}}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">Name with Initials :</div>
                    <div class="col-md-8"><strong>{{$user->initials}} {{$user->name_marking}}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">Gender :</div>
                    <div class="col-md-8"><strong>{{$user->GenderName()}}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">Id Number :</div>
                    <div class="col-md-8"><strong>{{$user->id_no}}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">Date of Birth :</div>
                    <div class="col-md-8"><strong>{{$user->dob}}</strong></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="text-primary">Contact Information</h4>
                <hr class="mb-1 mt-0 border-primary"/>
                <div class="row mb-2">
                    <div class="col-md-4">Address :</div>
                    <div class="col-md-8"><strong>{{$user->contact->address1}} {{$user->contact->address2}} {{$user->contact->address3}}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">District :</div>
                    <div class="col-md-8"><strong>{{$user->contact->district}}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">Land Phone :</div>
                    <div class="col-md-8"><strong>{{$user->contact->phone}}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">Mobile Phone :</div>
                    <div class="col-md-8"><strong>{{$user->contact->mobile}}</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">E Mail :</div>
                    <div class="col-md-8"><strong>{{$user->contact->email}}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection