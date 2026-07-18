@extends('layouts.admin')

@section('title')
Create New Roles
@endsection


@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/settings')}}">Settings</a></li>
<li class="breadcrumb-item"><a href="{{url('/admin/settings/roles')}}">Roles</a></li>
<li class="breadcrumb-item">Add</li>
@endsection


@section('content')
<div class="card border-left-danger">
    <div class="card-body">
        {{ Form::open(['url' => '/admin/settings/add-roles/', 'id'=>'addRecordFrom']) }}
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="Name">Name</label>
                        <input type="input" class="form-control" id="Name" name="name" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="Description">Description</label>
                        <input type="input" class="form-control" id="Description" name="description" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-grey-overlay d-block">Create Role</button>
                    </div>
                </div>
            </div>
            <?php  
                $fid= array_key_first($permissions);
                $curmod = $permissions[$fid]['module'];            
            ?>
            <div id="accordion">
                <a class="btn btn-link" data-toggle="collapse" data-target="#permission-group_{{$curmod}}" aria-expanded="true" aria-controls="permission-group_{{$curmod}}">{{$curmod}}</a>
                <div id="#permission-group_{{$curmod}}" class="collapse show" data-parent="#accordion">
                    <table class="table table-striped compact table-bordered dataTable">
                        <tbody>
                            @foreach($permissions as $permission)
                            @if($curmod != $permission['module'])
                            <?php $curmod = $permission['module'];?>
                                </tbody>
                            </table>
                        </div>
                        <a class="btn btn-link" data-toggle="collapse" data-target="#permission-group_{{$curmod}}" aria-expanded="true" aria-controls="#permission-group_{{$curmod}}">{{$curmod}}</a>
                        <div id="#permission-group_{{$curmod}}" class="collapse show" aria-labelledby="#accordion-control_{{$curmod}}" data-parent="#accordion">
                            <table class="table table-striped compact table-bordered dataTable">
                                <tbody>
                            @endif
                            <tr>
                                <td width="15px">
                                    <div class="checkbox checkbox-switch">
                                        <label>
                                            <input type="checkbox" value="{{$permission['id']}}" name="permissions[{{$permission['id']}}]">
                                        </label>
                                    </div>
                                </td>
                                <td>                                    
                                    <span class="help-block">{{$permission['description']}} ({{$permission['module']}} : {{$permission['event']}})</span>
                                </td>
                            </tr>                            
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
                    {{-- <tr>
            <td width="10px"><input type="checkbox" value="{{$permission['id']}}" name="permissions[{{$permission['id']}}]"></td>
            <td width="30%">{{$permission['description']}} </td>
            <td>{{$permission['module']}} : ({{$permission['event']}}</td>
        </tr> --}}
        </form>
    </div>
</div>
@endsection


@section('custom-js')
<script src="{{ asset('plugins/validate/jquery.validate.js') }}"></script>
<script>
$(document).ready(function() {
    var validater = $('#addRecordFrom').validate();

    $('#accordion').collapse({

    });
});
</script>
@endsection