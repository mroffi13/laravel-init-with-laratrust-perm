@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
<div class="row mb-2">
   <div class="col-sm-6">
      <h1 class="m-0 text-dark">
         @if (!empty($profile))
            Profile 
         @else
            Users    
         @endif
      </h1>
   </div><!-- /.col -->
   <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
         <li class="breadcrumb-item"><a href="/">Home</a></li>
         @if (empty($profile))
            <li class="breadcrumb-item"><a href="/users">Users</a></li>
         @endif
         <li class="breadcrumb-item active">
            @if (!empty($user))
               @if (!empty($profile))
                  Profile
               @else
                  Edit user #{{$user->id}}
               @endif
            @else
               Create user
            @endif
         </li>
      </ol>
   </div><!-- /.col -->
</div><!-- /.row -->
@stop

@section('content')
<form class="needs-validation" id="formSubmit" method="POST" enctype="multipart/form-data" action="{{ !empty($user) ? url('users/'. encrypt($user->id)) : url('users') }}" novalidate>
   <div class="container-fluid">
      <div class="row justify-content-center">
         <div class="col-md-8 col-sm-12">
            <div class="card card-outline card-primary">
               <div class="card-header align-middle">
                  @include('alert') 
                  <h3 class="card-title">
                     <i class="fas fa-edit"></i>
                     <strong>
                        @if (!empty($user))
                           #@if(empty($profile)){{$user->id}} | @endif{{$user->name}}
                        @else
                           Create user
                        @endif
                     </strong>
                  </h3>
               </div>
               @csrf
               @if (!empty($user))
                  @method('PUT')
               @endif
               <div class="card-body">
                  @include('alert')
                  <div class="form-row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="fullName">Full Name *</label>
                           <input type="text" class="form-control rounded-0" id="fullName" placeholder="Enter fullname .." value="@if(!empty($user->name)){{$user->name}}@else{{old('name')}}@endif" autocomplete="off" name="name" required>
                           <div class="invalid-feedback">
                              Please enter the full name!
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="email">Email *</label>
                           <input type="email" class="form-control rounded-0" autocomplete="off" placeholder="Enter email .." id="email" name="email" value="@if(!empty($user->email)){{$user->email}}@else{{old('email')}}@endif" required>
                           <div class="invalid-feedback">
                              Please enter the email!
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="phone">Phone</label>
                           <input type="text" class="form-control rounded-0" pattern="(\+62 ((\d{3}([ -]\d{3,})([- ]\d{4,})?)|(\d+)))|(\(\d+\) \d+)|\d{3}( \d+)+|(\d+[ -]\d+)|\d+" id="phone" placeholder="Enter phone .." value="@if(!empty($user->phone)){{$user->phone}}@else{{old('phone')}}@endif" autocomplete="off" name="phone">
                           <div class="invalid-feedback">
                              Please enter valid phone. Ex: +62 xxx, 021xxx, 0893xxx!
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="role">Role *</label>
                           <select name="role" id="role" style="width: 100%" required class="form-control rounded-0 select2ajax" data-data_placeholder="Choose role .." data-url="{{url('access-control/roles/getSelect2Option')}}">
                              @if (!empty($user->roles))
                                 <option value="{{$user->roles[0]->id}}" selected>{{$user->roles[0]->display_name}}</option>
                              @endif
                           </select>
                           <div class="invalid-feedback">
                              Please choose the role!
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="password">Password @if(empty($user))*@endif</label>
                           <input type="password" class="form-control rounded-0" autocomplete="new-password" id="password" @if(empty($user)) required @endif placeholder="Enter password .." value="" autocomplete="off" name="password">
                           <div class="invalid-feedback">
                              Please enter the password!
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="passwordConfirm">Password Confirmation @if(empty($user))*@endif</label>
                           <input type="password" class="form-control rounded-0" autocomplete="off" @if(empty($user)) required @endif placeholder="Enter password confirmation .." id="passwordConfirm" value="" name="password_confirmation">
                           <div class="invalid-feedback">
                              Please enter the password confirmation!
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="outlet">Outlet *</label>
                           <select name="outlet" id="outlet" style="width: 100%" required class="form-control rounded-0 select2ajax" data-data_placeholder="Choose outlet .." data-url="{{url('outlets/getSelect2Option')}}">
                              @if (!empty($user->outlet))
                                 <option value="{{$user->outlet->id}}" selected>{{$user->outlet->outlet_code . ' - '. $user->outlet->outlet_name}}</option>
                              @endif
                           </select>
                           <div class="invalid-feedback">
                              Please choose the outlet!
                           </div>
                        </div>
                     </div>
                     @if (!empty($profile))
                        @if (!empty($user->roles[0]->name) && $user->roles[0]->name == 'supervisor')
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label class="d-block" for="pin">PIN Approval</label>
                                 <input type="password" name="pin" placeholder="Enter pin .." pattern="^[0-9]{1,6}$" id="pin" class="form-control rounded-0" value="@if(!empty($user->pin)){{$user->pin}}@endif">
                                 <div class="invalid-feedback">
                                    Please enter number and max 6 digit!
                                 </div>
                              </div>
                           </div>
                        @endif
                        <input type="hidden" name="profile" value="1">
                     @endif
                     @if (!empty($user) && empty($profile))
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="d-block" for="status">Status</label>
                              <input type="checkbox" data-size="sm" @if(!empty($user->status)) checked @endif name="status" id="status" data-toggle="toggle" data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="warning">
                           </div>
                        </div>
                     @else
                        <input type="hidden" name="status" value="1">
                     @endif
                  </div>
               </div>

               <div class="card-footer">
                  @if (!empty($profile))
                     <a href="{{url('/')}}" class="btn btn-flat btn-default">Home</a>
                  @else
                     <a href="{{url('/users')}}" class="btn btn-flat btn-default">Cancel</a>
                  @endif
                  <button type="submit" class="btn btn-flat btn-primary">Save</button>
               </div>
               @include('partials.overlay')
            </div>
         </div>
         <div class="col-md-4 col-sm-12">
            <div class="card card-outline card-primary">
               <div class="card-header align-middle">
                  <h3 class="card-title">
                     <strong>
                        <i class="fas fa-user"></i> Profile
                     </strong>
                  </h3>
               </div>

               <div class="card-body">
                  <div class="card-body img-box box-profile">
                     <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle img-preview" src="@if(!empty($user->image['media_path'])){{$user->image['media_path']}}@else{{asset('assets/default_user.jpg')}}@endif">
                     </div>

                     <div class="custom-file file-box mt-5">
                        <input type="file" class="custom-file-input btn-upload rounded-0" data-preview-default="@if(!empty($user->image['media_path'])){{$user->image['media_path']}}@else{{asset('assets/default_user.jpg')}}@endif" data-min-width="500" data-min-height="500" id="photo" name="img_profile" accept="image/*">
                        <label class="custom-file-label" for="photo">
                           <span class="file-label">Choose Photo</span>
                        </label>
                        <small>* Max size: 5 mb</small>
                     </div>
                  </div>
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
   <script src="{{asset('js/user/user.js')}}"></script>
   <script src="{{asset('js/upload.js')}}"></script>
@endsection
