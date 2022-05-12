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
               Role | {{$role->display_name}}
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
                     <i class="fas fa-fingerprint"></i> Role | {{$role->display_name}}
                  </strong>
               </h3>
            </div>
            <div class="card-body">
               <div class="form-row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <strong>Role</strong>
                        @if (!empty($role->display_name))
                           <p class="text-muted">{{$role->display_name}}</p>
                        @else
                           <p class="text-muted">-</p>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="d-block" for="status">Status</label>
                        @switch($role->status)
                           @case(1)
                              {!! \Helper::badge($role->status_label, 'success') !!}
                              @break
                           @default 
                              {!! \Helper::badge($role->status_label, 'warning') !!}  
                        @endswitch
                     </div>
                  </div>
               </div>
               <div class="form-row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <strong>Permissions</strong>
                        @if (!empty($role->permissions))
                           <ul>
                              @foreach ($role->permissions as $permission)
                                 <li class="text-muted">{{$permission->display_name}}</li>
                              @endforeach
                           </ul>
                        @else
                           <p>-</p>
                        @endif
                     </div>
                  </div>
               </div>
            </div>

            <div class="card-footer">
               <a href="{{url('/access-control/roles')}}" class="btn btn-flat btn-default">Back</a>
               @if (\Helper::isAbleTo(request(), 'update-acl'))
                  <a href="{{url('/access-control/roles/'.$role->id.'/edit')}}" class="btn btn-flat btn-info">Edit</a>
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
