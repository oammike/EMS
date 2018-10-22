<table style="border:1px dotted #666; font-family:Arial,sans-serif; font-size:0.8em; font-weight:normal; width:50%">
  <tr>
    <th  style="border-bottom:3px solid #333">Production Date</td>
    <th  style="border-bottom:3px solid #333">Logs</th>
  </tr>
@foreach($dtr as $data)
<tr>
  <td id="{{$data->id}}" style="border-bottom:1px dotted #333" align="left">&nbsp;&nbsp;&nbsp;{{date('Y-M-d l',strtotime($data->Production_Date))}}</td>
 
  <td style="border-bottom:1px dotted #333">
    <div style="border-bottom:1px solid #666">{{$data->Log_Type}} : <span style="float:right">{{$data->logTime}}</span></div><br/>
     


  </td>
</tr>
@endforeach

</table>