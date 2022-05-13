
<div class="dropdown">
    <button class="btn" data-toggle-second="tooltip" data-placement="left" title="Action" type="button" id="dropdownMenuButton{{$data->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
       <i class="fas fa-ellipsis-v"></i>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$data->id}}">
       @foreach ($buttons as $button)
          @if ($button['show'] && 
          !empty($button['can']) && 
          Auth::user()->isAbleTo($button['can']))
             <a class="dropdown-item {{ $button['classes'] ?? '' }}" href="{{!empty($button['url']) ? url($button['url']) : 'javascript:void(0)'}}"
                @if (!empty($button['attributes']))
                   @foreach ($button['attributes'] as $attr => $value)
                      {{'data-'.$attr.'="'.$value.'"'}}
                   @endforeach
                @endif
             ><i class="{{ $button['icon'] ?? '' }}"></i> {{$button['label'] ?? ''}}</a>
          @endif
       @endforeach
    </div>
 </div>