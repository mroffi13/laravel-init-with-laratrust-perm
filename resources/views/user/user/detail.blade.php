@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark">Users</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/users">Users</a></li>
            <li class="breadcrumb-item active">
               Profile | {{$user->name}}
            </li>
        </ol>
    </div><!-- /.col -->
</div><!-- /.row -->
@stop

@section('content')
<div class="container-fluid">
   <div class="row justify-content-center">
      <div class="col-sm-12">
         <div class="card card-outline card-primary">
            <div class="card-header align-middle">
               <h3 class="card-title">
                  <strong>
                     <i class="fas fa-user"></i> Profile | {{$user->name}}
                  </strong>
               </h3>
            </div>
            <div class="card-body">
               <div class="card-body img-box box-profile">
                  <div class="text-center">
                     <img class="profile-user-img img-fluid img-circle img-preview" style="width: 20%" src="@if(!empty($user->image['media_path'])){{$user->image['media_path']}}@else{{\Helper::generateProfilePic($user->name)}}@endif">
                  </div>
               </div>
               <div class="dropdown-divider"></div>
               <div class="form-row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <strong>Full Name</strong>
                        @if (!empty($user->name))
                           <p class="text-muted">{{$user->name}}</p>
                        @else
                           <p class="text-muted">-</p>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <strong>Email</strong>
                        @if (!empty($user->email))
                           <p class="text-muted">{{$user->email}}</p>
                        @else
                           <p class="text-muted">-</p>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="form-row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <strong>Phone</strong>
                        @if (!empty($user->phone))
                           <p class="text-muted">{{$user->phone}}</p>
                        @else
                           <p class="text-muted">-</p>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <strong>Role</strong>
                        @if (!empty($user->roles))
                           <p class="text-muted">{{$user->roles[0]->display_name}}</p>
                        @else
                           <p class="text-muted">-</p>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="form-row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <strong>Outlet</strong>
                        @if (!empty($user->outlet))
                           <p class="text-muted">{{$user->outlet->outlet_code}} - {{$user->outlet->outlet_name}}</p>
                        @else
                           <p class="text-muted">-</p>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="d-block" for="status">Status</label>
                        @switch($user->status)
                           @case(1)
                              {!! \Helper::badge($user->status_label, 'success') !!}
                              @break
                           @default 
                              {!! \Helper::badge($user->status_label, 'warning') !!}  
                        @endswitch
                     </div>
                  </div>
               </div>
            </div>

            <div class="card-footer">
               <a href="{{url('/users')}}" class="btn btn-flat btn-default">Back</a>
               @if (\Helper::isAbleTo(request(), 'update-users'))
                  <a href="{{url('/users/'.$user->id.'/edit')}}" class="btn btn-flat btn-info">Edit</a>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>
@stop

@section('footer')
   @include('partials.footer')
@endsection

@section('css')

@endsection

@section('js')

@endsection
