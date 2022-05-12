@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark">Roles</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/access-control/roles">Roles</a></li>
            <li class="breadcrumb-item active">
               @if (!empty($role))
                  Edit role #{{$role->id}}
               @else
                  Create role
               @endif
            </li>
        </ol>
    </div><!-- /.col -->
</div><!-- /.row -->
@stop

@section('content')
<form class="needs-validation" id="formSubmit" method="POST" action="{{ !empty($role) ? url('access-control/roles/'. encrypt($role->id)) : url('access-control/roles') }}" novalidate>
   <div class="container-fluid">
      <div class="row justify-content-center">
         <div class="col-md-12 col-sm-12">
            <div class="card card-outline card-primary">
               <div class="card-header align-middle">
                  <h3 class="card-title">
                     <i class="fas fa-edit"></i>
                     <strong>
                        @if (!empty($role))
                           #{{$role->id}} | {{$role->display_name}}
                        @else
                           Create role
                        @endif
                     </strong>
                  </h3>
               </div>
               @csrf
               @if (!empty($role))
                  @method('PUT')
               @endif
               <div class="card-body">
                  @include('alert')
                  <div class="form-row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="roleName">Role Name *</label>
                           <input type="text" class="form-control rounded-0" id="roleName" placeholder="Enter Role Name .." value="@if(!empty($role->display_name)){{$role->display_name}}@else{{old('name')}}@endif" autocomplete="off" name="name" required>
                           <div class="invalid-feedback">
                              Please enter role name!
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="permission">Permissions *</label>
                           <select name="permissions[]" @if(!empty($role->permissions)) data-selected="{{\Helper::maybe_serialize(array_column($role->permissions, 'id'))}}" @endif id="permission" data-url="{{url('access-control/permissions/getSelect2Option')}}" multiple style="width: 100%" required class="form-control rounded-0 bsDualistbox" data-data_placeholder="Choose permissions .." data-url="{{url('access-control/permissions/getSelect2Option')}}">
                           </select> 
                           <div class="invalid-validate">
                              <div class="invalid-feedback">
                                 Please choose permissions!
                              </div>                      
                           </div>
                        </div>
                     </div>
                  </div>
                  @if (!empty($role))
                     <div class="form-row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="d-block" for="status">Status</label>
                              <input type="checkbox" data-size="sm" @if($role->status == 1) checked @endif name="status" id="status" data-toggle="toggle" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="warning">
                           </div>
                        </div>
                     </div>
                  @endif
               </div>

               <div class="card-footer">
                  <a href="{{url('/access-control/roles')}}" class="btn btn-flat btn-default">Cancel</a>
                  <button type="submit" class="btn btn-flat btn-primary">Save</button>
               </div>
               @include('partials.overlay')
            </div>
         </div>
      </div>
   </div>
<form>
@stop

@section('footer')
   @include('partials.footer')
@endsection

@section('css')
   <link rel="stylesheet" href="{{asset('vendor/overlayScrollbars/css/OverlayScrollbars.css')}}">
   <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
   <link rel="stylesheet" href="{{asset('vendor/bootstrap4-duallistbox/bootstrap-duallistbox.css')}}">
@endsection

@section('js')
   <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
   <script src="{{asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.js')}}"></script>
   <script src="{{asset('vendor/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.js')}}"></script>
   <script src="{{asset('js/user/role.js')}}"></script>
   <script src="{{asset('js/upload.js')}}"></script>
@endsection
