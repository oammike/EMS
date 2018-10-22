@extends('layouts.main')


@section('metatags')
  <title>Manage User Types</title>
    <meta name="description" content="profile page">

@stop


@section('content')




<section class="content-header">

      <h1>
      User Types
        <small><a href="{{action('UserTypeController@create')}}" class="btn btn-xs btn-success"><i class="fa fa-plus"></i> Add New User Type</a></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        
        <li class="active">Manage User Types</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
      
        <div class="col-lg-12">

                    <div class="nav-tabs-custom">
                          <ul class="nav nav-tabs">
                            @foreach($userRoles as $userRole)
                            <li class="@if($userRole['id'] == 1)active @endif bg-success"><a href="#userType-{{$userRole['id']}}" data-toggle="tab"><strong>{{$userRole['userType']}} </strong></a></li>
                            @endforeach
                            
                          </ul>



                          <div class="tab-content">



                            @foreach($userRoles as $userRole2)
                            <div class="@if($userRole2['id'] == 1)active @endif tab-pane" id="userType-{{$userRole2['id']}}">

                              <div class="box no-border">
                                <div class="box-header"><h4>{{$userRole2['userType']}} <br/></h4>
                                  <a href="" class="submitBtn btn btn-primary btn-md pull-right"><i class="fa fa-save"></i> Save Changes</a>
                                  <div class="alert-submit"></div>
                                  <p>{{$userRole2['description']}} </p></div>
                                  <input type="hidden" name="roleType_id[]" value="{{$userRole2['id']}}" />
                                <div class="box-body">
                                 
                                  @foreach ($roles as $role)
                                  <div class="box pull-left" style="max-width:40%; min-height:200px; margin:5px">
                                    <h4>{{$role['name']}}</h4>
                                      <ul class="list-unstyled" style="margin-left:10px">
                                          @foreach ($role['items'] as $item)
                                        <li><label><input name="role-{{$userRole2['id']}}[]" id="role-{{$userRole2['id']}}-{{$item->id}}" type="checkbox" @if(in_array($item->id, (array)$userRole2['roles'])) checked="checked"  @endif value="{{$item->id}}" /> {{$item->name}}</label></li>
                                        @endforeach 
                                      </ul>
                                   </div>     
                                  @endforeach

                                  
                               

                                </div>
                              </div>
                              
                             
                            </div>
                            <!-- /.tab-pane -->
                            @endforeach

                          </div>
                          <!-- /.tab-content -->

        </div><!--end col-lg-12 -->
       

      </div>

     

    </section>

@stop

@section('footer-scripts')
<script>
  $(function () {
   'use strict';




   $('.submitBtn').on('click', function(e) {

      var _token = "{{ csrf_token() }}";
     
      var name = $('#name').val();
      //var description = $('#description').val();
      var alertName = $('#alert-name');
      
      var roleIDs = [];
      $("input[name='roleType_id[]']").each(function(){roleIDs.push($(this).val());});
     


      // if (!validateRequired(name,alertName,"enter user type")) { 
      //   console.log('not valid Name '); e.preventDefault(); e.stopPropagation();
      //   return false;
      // }  else {
            //save first the new position

      

      jQuery.each(roleIDs, function(index,item){
        
        var roles = [];
        var roleVar = "role-"+item;
        $("input[name='role-"+item+"[]']:checked").each(function(){roles.push($(this).val());});
          console.log("RoleID val: "+ item);
          console.log('Selected roles:');
          console.log(roles);

          $.ajax({
                                url:"{{action('UserTypeController@store')}} ",
                                type:'POST',
                                data:{
                                  'id': item,
                                  //'description': description,
                                  'roles':roles,
                                  _token:_token},

                                error: function(response2)
                                { console.log("Error saving User Roles: ");
                                  console.log(response2); return false;
                                },
                                success: function(response2)
                                {
                                  console.log("the response: ");
                                  console.log(response2);

                                   $('.submitBtn').fadeOut();
                                          var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> User Type data saved. <br /><br/>";
                                         
                                          //htmcode += "<a href=\"{{action('UserTypeController@index')}}\" class='btn btn-sm btn-default text-black pull-right'><i class='fa fa-reply'></i> Back to All User Types </a> <br/><br/>";
                                          
                                         // $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 
                                         $('.alert-submit').addClass('alert alert-success').fadeIn().html(htmcode).delay(2000).fadeOut( function(){ location.reload();}); 

                                }

                    }); //end ajax


      });
      

            /*
            console.log("save pos first");
              $.ajax({
                                url:"{{action('UserTypeController@store')}} ",
                                type:'POST',
                                data:{
                                  'name': name,
                                  'description': description,
                                  'roles':roles,
                                  _token:_token},

                                error: function(response2)
                                { console.log("Error saving program/department: ");
                                  console.log(response2); return false;
                                },
                                success: function(response2)
                                {
                                  console.log(response2);
                                   $('input[name="submit"').fadeOut();
                                          var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> New User Type saved. <br /><br/>";
                                         
                                          htmcode += "<a href=\"{{action('UserTypeController@index')}}\" class='btn btn-sm btn-default text-black pull-right'><i class='fa fa-reply'></i> Back to All User Types </a> <br/><br/>";
                                          
                                          $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 

                                }

                    }); //end ajax

            */

          
         // }//end else valid new position

        
      
      e.preventDefault(); e.stopPropagation();
      return false;
    }); //end addCampaign




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

}); //end main
</script>
@stop