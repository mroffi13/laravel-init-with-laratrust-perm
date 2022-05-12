@extends('adminlte::page')

@section('title', 'Permissions')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark">Permissions</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/access-control/permissions">Permissions</a></li>
            <li class="breadcrumb-item active">
               @if (!empty($permission))
                  Edit permission #{{$permission->id}}
               @else
                  Create permission
               @endif
            </li>
        </ol>
    </div><!-- /.col -->
</div><!-- /.row -->
@stop

@section('content')
<form class="needs-validation" id="formSubmit" method="POST" action="{{ !empty($permission) ? url('access-control/permissions/'. encrypt($permission->id)) : url('access-control/permissions') }}" novalidate>
   <div class="container-fluid">
      <div class="row justify-content-center">
         <div class="col-md-12 col-sm-12">
            <div class="card card-outline card-primary">
               <div class="card-header align-middle">
                  <h3 class="card-title">
                     <i class="fas fa-edit"></i>
                     <strong>
                        @if (!empty($permission))
                           #{{$permission->id}} | {{$permission->display_name}}
                        @else
                           Create permission
                        @endif
                     </strong>
                  </h3>
               </div>
               @csrf
               @if (!empty($permission))
                  @method('PUT')
               @endif
               <div class="card-body">
                  @include('alert')
                  <div class="form-row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="permissionName">Permission Name *</label>
                           <input type="text" class="form-control rounded-0" id="permissionName" placeholder="Enter Permission Name .." value="@if(!empty($permission->display_name)){{$permission->display_name}}@else{{old('name')}}@endif" autocomplete="off" name="name" required>
                           <div class="invalid-feedback">
                              Please enter permission name!
                           </div>
                        </div>
                     </div>
                     @if (empty($permission))
                        <div class="col-md-6">
                           <div class="form-group">
                              <label for="email">Action *</label>
                              <div class="row">
                                 @foreach ($actions as $action)
                                    <div class="col-sm-12">
                                       <div class="custom-control custom-checkbox">
                                          <input class="custom-control-input" type="checkbox" id="{{$action}}" name="action[]" value="{{$action}}">
                                          <label for="{{$action}}" class="custom-control-label">{{$action}}</label>
                                       </div>
                                    </div>
                                 @endforeach
                              </div>
                           </div>
                        </div>
                     @endif
                  </div>
                  @if (!empty($permission))
                     <div class="form-row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="d-block" for="status">Status</label>
                              <input type="checkbox" data-size="sm" @if($permission->status == 1) checked @endif name="status" id="status" data-toggle="toggle" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="warning">
                           </div>
                        </div>
                     </div>
                  @endif
               </div>

               <div class="card-footer">
                  <a href="{{url('/access-control/permissions')}}" class="btn btn-flat btn-default">Cancel</a>
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
   <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
@endsection

@section('js')
   <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
   <script src="{{asset('js/user/permission.js')}}"></script>
   <script src="{{asset('js/upload.js')}}"></script>
@endsection
