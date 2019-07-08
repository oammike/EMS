@extends('layouts.main')

@section('metatags')
<title>Add New Leader | OAMPI Evaluation System</title>

<style type="text/css">
/* Sortable items */

.sortable-list {
  background: none; /* #fcedc6;*/
  list-style: none;
  margin: 0;
  min-height: 60px;
  padding: 10px;
}
.sortable-item {
  background-color: #fcedc6;
  
  cursor: move;
  
  font-weight: bold;
  margin: 2px;
  padding: 10px 0;
  text-align: center;
}

/* Containment area */

#containment {
  background-color: #FFA;
  height: 230px;
}


/* Item placeholder (visual helper) */

.placeholder {
  background-color: #ccc;
  border: 3px dashed #fcedc0;
  min-height: 150px;
  width: 180px;
  float: left;
  margin-bottom: 5px;
  padding: 45px;
}
</style>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Leaders
        <small>Manage all OAMPI leaders </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        
        <li class="active">Employees</li>
      </ol>
    </section>

     <section class="content">
      @if (Auth::user()->userType_id == 1 || Auth::user()->userType_id == 2 ) 
      <a href="{{action('UserController@create')}} " class="btn btn-md btn-success  pull-right"><i class="fa fa-plus"></i> Add New Employee</a>
     
      @endif

          <div class="row" style="margin-top:50px">
           
            

            <div class="col-lg-12"> <!-- ******************* LEFT COLUMN ***************** -->

              <!-- TABLE: LEFT -->
              <div class="box-body">
                  <div class="table">
                    <table class="table no-margin table-bordered" id="teams" >
                      <thead>
                      <tr>
                        <th></th>
                        
                        <th>Last Name</th>
                        <th>First Name</th>                      
                        <th>Program / Campaign</th>
                        <th>Status</th>
                        <th>Action</th>
                        
                      </tr>
                      </thead>
                      <tbody>
                        @foreach ($users as $member)
                        <tr id="row{{$member->employeeNumber}}">
                          <!--  @if ( file_exists('public/img/employees/'.$member->id.'.jpg') )
                         <td class="text-center "><img src="{{asset('public/img/employees/'.$member->id.'.jpg')}}" width='60' /> </td>
                         @else
                         <td class="text-center "><img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" class="img-circle" alt="User Image"> </td>
                         @endif -->

                         <td> - </td>
                        
                         
                        <td>{{$member->lastname}}</td>

                        <td>{{$member->firstname}}</a></td>
                        
                        
                        <td>{{$member->program}}</td>
                        <td>{{$member->status}}</td>

                        <td>
                            @if($canAddLeader)
                              @if(is_null($member->nickname))
                               <a style="margin-top:5px" class="promote btn btn-sm btn-flat btn-default" id="promote{{$member->id}}" firstname="{{$member->firstname}}" lastname="{{$member->lastname}}" campaign_id="{{$member->programID}}" employeeNumber="{{$member->employeeNumber}}" memberID="{{$member->id}}"><i class="fa fa-star"></i> Promote as Leader</a>

                              @else
                               <a style="margin-top:5px" class="promote btn btn-sm btn-flat btn-default" id="promote{{$member->id}}" firstname="{{$member->nickname}}" lastname="{{$member->lastname}}" campaign_id="{{$member->programID}}" employeeNumber="{{$member->employeeNumber}}" memberID="{{$member->id}}"><i class="fa fa-star"></i> Promote as Leader</a>

                              @endif
                          
                          @endif
                        </td>
                        </tr>

                         @include('layouts.modals', [
                          'modelRoute'=>'user.destroy',
                          'modelID' => $member->id, 
                          'modelName'=>$member->firstname." ". $member->lastname, 
                          'modalTitle'=>'Delete', 
                          'modalMessage'=>'Are you sure you want to delete this?', 
                          'formID'=>'deleteEmployee',
                          'icon'=>'glyphicon-trash' ])


                        @endforeach
                      </tbody>
                      
                    </table>

                   
                    
                  </div>
                  <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix" style="background:none">
                 
                  
                </div>
                <!-- /.box-footer -->
              <!-- /.box -->
            </div><!--end left -->


           


            
           

          
          </div><!-- end row -->

       
     </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->

 <script type="text/javascript">

 $(function () {
   'use strict';
  
   $("#teams").DataTable({
    "scrollX": false,
     "oLanguage": {
                     "sSearch": "<strong>Add New Leader</strong> <br/>To add a new leader, use the search box to look for the employee you want to promote as a new leader: ",
                     "class": "pull-left"
                   },
    "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
   
    "lengthMenu": [20, 100, 500],//[5, 20, 50, -1],
    "responsive": false,
    "columnDefs": [
            
            { "width": "60",  "targets": [ 0 ], "sorting":false },
            // { "width": "150",  "targets": [ 1 ] },
            // { "width": "150",  "targets": [ 2 ] },
            // { "width": "180",  "targets": [ 3 ] },
            // { "width": "150",  "targets": [ 4 ] },
            {"sorting": false, "targets": [5], "width":"200"}
        ],
                 

            
    });
});

 </script>


<script>
  
  $(function () {
   'use strict';


  
   $('#teams tbody').on( 'click', 'a.promote', function (e) {
      e.preventDefault();
      
      

       var employeeNumber = $(this).attr('employeeNumber');
       var firstname = $(this).attr('firstname');
       var lastname = $(this).attr('lastname');
       var campaign_id = $(this).attr('campaign_id');
       
       var _token = "{{ csrf_token() }}";

       console.log("employeeNum:" +employeeNumber);
       console.log("firstname:" +firstname);
       console.log("lastname:" +lastname);
       console.log("campaign_id:" +campaign_id);


                        
                       

      $.ajax({
                      url:"{{action('ImmediateHeadController@store')}}",
                      type:'POST',
                      data:{employeeNumber:employeeNumber, firstname:firstname, lastname:lastname, campaign_id:campaign_id, _token:_token},
                      error: function(response)
                      {
                          
                        
                        console.log("Error saving leader ");
                        console.log(response);

                          
                          return false;
                      },
                      success: function(response)
                      { 

                        $('#row'+employeeNumber).html('<td colspan="6"><p class="text-center text-success">Promoting employee as a New Leader...</p></td>').delay(2000).fadeOut("slow");
                        console.log("Leader saved.");
                        console.log(response);

                          return true;
                      }
                  });




    

    });


   });


   

 
</script>
<!-- end Page script -->

<!-- <script type="text/javascript" src="{{asset('public/js/dashboard.js')}}"></script> -->

@stop