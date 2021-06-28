<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">ISBN号：{{ $record->book->ISBN }}</h3>
    <div class="box-tools">
      <div class="btn-group float-right" style="margin-right: 10px">
        <a href="{{ route('admin.records.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
      </div>
    </div>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tbody>
      <tr>
        <td>用户ID：</td>
        <td>{{ $record->user_id }}</td>
        <td>用户姓名：</td>
        <td>{{ $record->user->name }}</td>
      </tr>
      <tr>
        <td>图书ID：</td>
        <td>{{ $record->book_id }}</td>
        <td>图书名称：</td>
        <td>{{ $record->book->name }}</td>
      </tr>
      <tr>
        <td>借阅时间：</td>
        <td colspan="3">{{$record->borrow_date}}</td>
      </tr>
      <tr>
        <td>最晚归还时间：</td>
        <td colspan="3">{{ $record->return_deadline }}</td>
      </tr>
      @if(!$record->status)
      <tr>
        <td>归还时间：</td>
        <td>
          <input type="datetime-local" name="return_date" id="return_date">
          <button class="btn btn-success" id="returnBtn" style="height: 25px;margin-left:5px; padding:2px 4px;">归还图书</button>
          @if($errors->has('return_date'))
            @foreach($errors->get('return_date') as $msg)
              <span class="help-block">{{ $msg }}</span>
            @endforeach
          @endif
        </td>
      </tr>
      @else
      <tr>
        <td>归还时间：</td>
        <td colspan="3">{{ $record->return_date }}</td>
      </tr>
      @endif
      </tbody>
    </table>
  </div>
</div>

<script>
$(document).ready(function(){
  $('#returnBtn').click(function(){
    let t = $('#return_date').val();
    if(t){
      let returnTime = t.replace('T',' ');
      fetch('{{route('admin.records.return')}}',{
        body:JSON.stringify({
          id:{{$record->id}},
          return_date:returnTime+':00',
          _token: LA.token
        }),
          method:'POST',
          headers: {
            'content-type': 'application/json'
          },
      }).then(function(res){
        if(res.status == 200){
          Swal.fire({
            type:'success',
            text: '归还成功'
          }).then(()=>{
            location.reload();
          });
          return res.text();
        }else{
          if(res.status == 400){
              return Swal.fire({
              type:'error',
              text:'归还时间不得早于借阅时间'  
            });
          }else{
              return Swal.fire({
              type:'error',
              text:'系统错误'  
            });
          }
          
        }
      }).then(res=>{console.log(res)});
    }
  });
});
</script>