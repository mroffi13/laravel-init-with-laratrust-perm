@if (session('alert_message'))
   <div class="d-none {{session('alert_class')}}" data-icon="{{session('alert_icon')}}" role="alert">
      {{ session('alert_message') }}
   </div>
@endif
@if (!empty(session('alert_login')))
   <span class="d-none swal-alert top-end" data-icon="success">{{ __('You are logged in!') }}</span>    
@endif