@if (!empty($datas))
    @foreach ($datas as $data)
        <option value="{{$data->id}}" @if(in_array($data->id, $selected)) selected @endif>{{$data->text}}</option>
    @endforeach
@endif