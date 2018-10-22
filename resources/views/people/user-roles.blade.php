@extends('layouts.main')


@section('metatags')
  <title>Manage User Roles</title>
    <meta name="description" content="profile page">

@stop


@section('content')




<section class="content-header">

      <h1>
      Roles
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">Manage Roles</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
      
        <div class="col-lg-12">
          


          <!-- Profile Image -->
                          <div class="box box-primary">
                            <div class="box-body">

                          
                            
                              @foreach ($roles as $role)
                              <div class="box pull-left" style="max-width:40%; min-height:200px; margin:5px">
                                <h4>{{$role['name']}} </h4>
                                  <ul>
                                      @foreach ($role['items'] as $item)
                                    <li>{{$item->name}}</li>
                                    @endforeach 
                                    <li class="list-unstyled"><a class="addBtn btn btn-xs btn-success" style="margin-top:1em" href="" roleID="{{$role['id']}}" ><i class="fa fa-plus"></i> Add new role </a></li>
                                  </ul>

                                  <div class="holders" id="holder-{{$role['id']}}">
                                    <input type="text" class="pull-left form-control newRole" name="roleType_id" id="roleType-{{$role['id']}}" placeholder="Enter new role" style="max-width:190px; margin:5px 0 30px 30px" />
                                    <a href="" roleID="{{$role['id']}}" id="submit-{{$role['id']}}" class="submitBtn btn btn-sm btn-default pull-left" style="max-width:190px; margin:8px 0 30px 5px"><i class="fa fa-save"></i> Save </a>
                                  </div>

                                  <div id="alert-submit-{{$role['id']}}"></div>
                               </div>     
                              @endforeach
                           
                            

                            <!-- /.box-body -->
                          </div>
                        </div>

        </div>
       

      </div>

     

    </section>

@stop

@section('footer-scripts')
<script>

 $(function () {
   //'use strict';

   $('.holders').hide();

   $('.addBtn').on('click', function(e) {
    e.preventDefault(); e.stopPropagation();

    var roleID = $(this).attr('roleID');
    console.log("roleID: "+roleID);

    $('#holder-'+roleID).fadeIn();
    $(this).fadeOut();

   }); //end addBtn click



   $('.submitBtn').on('click', function(e) {


      var _token = "{{ csrf_token() }}";
      var roleType_id = $(this).attr('roleID');
     
      var name = $('#roleType-'+roleType_id).val();

      var alertCampaign = $('#alert-name');
     


      if (!validateRequired(name,alertCampaign,"Enter new role")) { 
        console.log('not valid role '); e.preventDefault(); e.stopPropagation();
        return false;
      }  else {
            //save first the new position
            console.log("save first");
              $.ajax({
                                url:"{{action('RoleController@store')}} ",
                                type:'POST',
                                data:{
                                  'name': name,
                                  'roleType_id': roleType_id,
                                  _token:_token},

                                error: function(response2)
                                { console.log("Error saving role");
                                  console.log(response2); return false;
                                },
                                success: function(response2)
                                {
                                   $('#holder-'+response2.id).fadeOut();
                                          var htmcode = '<span class="success"> <i class="fa fa-save"></i> Role '+response2.name+' saved. <br /><br/>';
                                         
                                         
                                          
                                          $('#alert-submit-'+response2.id).addClass('alert alert-success').fadeIn().html(htmcode).delay(2000).fadeOut( function(){ location.reload();}); 
                                          

                                }

                    }); //end ajax

          
          }//end else valid new position

        
      
      e.preventDefault(); e.stopPropagation();
      return false;
    }); //end addCampaign


       

   });



function validateRequired(param, availability, defaultval) {

        
        if (param == null){

          availability.addClass('alert alert-danger').fadeIn();
            availability.html('<span class="success"> <i class="fa fa-warning"></i> This field is required. </span>');  
             return false;
        }

        else if(param.length <= 0 || param === defaultval) { 
            
            availability.addClass('alert alert-danger').fadeIn();
            availability.html('<span class="success"> <i class="fa fa-warning"></i> This field is required. </span>');   
             return false;         
            

        } else{
            availability.removeClass();
            availability.html('');
            return true;
                      
        }
       

}

   


</script>
@stop