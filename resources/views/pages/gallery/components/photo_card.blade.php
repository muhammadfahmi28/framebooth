<div>
    <img src="{{asset('storage/'.$folder.'/'.$photo->filename)}}" alt="" width="250px">
    <a href="{{url("app/print/{$photo->id}")}}">Print</a>
    <br/>
    <a href="{{url("app/delete/{$photo->id}")}}">Delete</a>
    <br/>
    {{$photo->created_at}}
</div>
