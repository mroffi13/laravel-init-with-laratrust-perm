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
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </div><!-- /.col -->
</div><!-- /.row -->
@stop

@section('content')
   <div class="container-fluid">
      <div class="row justify-content-center">
         <div class="col-md-12">
            <div class="card card-outline card-primary">
               <div class="card-header">
                  @if (\Helper::isAbleTo(request(), 'create-users'))
                     <a href="{{url('users/create')}}" class="btn btn-flat btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Users
                     </a>
                  @endif
               </div>

               <div class="card-body">
                  @include('alert')
                  <table class="table border table-hover w-100" id="userList" style="width: 100%">
                     <thead class="thead-light">
                        @foreach ($theaders as $thead)
                           <th>{{$thead}}</th>
                        @endforeach
                     </thead>
                     <tbody>
                        <tr>
                           <td colspan="{{count($theaders)}}">
                              &nbsp;
                           </td>
                        </tr>
                     </tbody>
                  </table>
                  @include('partials.overlay')
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
   <script src="{{asset('js/user/user.js')}}"></script>
@endsection
