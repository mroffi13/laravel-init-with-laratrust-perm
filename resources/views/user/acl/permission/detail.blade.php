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
               Permission | {{$permission->display_name}}
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
                     <i class="fas fa-fingerprint"></i> Permission | {{$permission->display_name}}
                  </strong>
               </h3>
            </div>
            <div class="card-body">
               <div class="form-row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <strong>Permission</strong>
                        @if (!empty($permission->display_name))
                           <p class="text-muted">{{$permission->display_name}}</p>
                        @else
                           <p class="text-muted">-</p>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="d-block" for="status">Status</label>
                        @switch($permission->status)
                           @case(1)
                              {!! \Helper::badge($permission->status_label, 'success') !!}
                              @break
                           @default 
                              {!! \Helper::badge($permission->status_label, 'warning') !!}  
                        @endswitch
                     </div>
                  </div>
               </div>
            </div>

            <div class="card-footer">
               <a href="{{url('/access-control/permissions')}}" class="btn btn-flat btn-default">Back</a>
               @if (\Helper::isAbleTo(request(), 'update-acl'))
                  <a href="{{url('/access-control/permissions/'.$permission->id.'/edit')}}" class="btn btn-flat btn-info">Edit</a>
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
