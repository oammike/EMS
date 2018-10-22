@extends('layouts.main')


@section('metatags')
  <title>Create New User Types</title>
    <meta name="description" content="profile page">

@stop


@section('content')




<section class="content-header">

      <h1>
      Create New User Types
       
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{action('UserTypeController@index')}}"> Manage User Types</a></li>
        
        <li class="active">New User Types</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
       {{ Form::open(['route' => 'userType.store', 'id'=> 'newUserType','name'=>'newUserType' ]) }}
        <div class="col-xs-8">
          <div class="box box-primary">
            <div class="box-body">
              

              <label>Name of new User Type: </label>
              <input type="text" class="required form-control" name="name" id="name" placeholder="enter user type" style="width:50%" />
              <textarea name="description" id="description" class="form-control" placeholder="description" style="margin-top:10px; width:50%"></textarea>
              <div id="alert-name" style="margin-top:10px"></div>

              <h4 class="text-success"><br/><br/>Grant this User Type the following roles: </h4>
               @foreach ($roles as $role)
                <div class="box pull-left" style="max-width:40%; min-height:200px; margin:5px">
                  <h4>{{$role['name']}}</h4>
                   <ul class="list-unstyled" style="margin-left:10px">
                      @foreach ($role['items'] as $item)
                    <li><label><input name="roles[]" id="role-{{$item->id}}" type="checkbox" value="{{$item->id}}" /> {{$item->name}}</label></li>
                    @endforeach 
                  </ul>
                 </div>     
                @endforeach

                


              
              </div>
            </div>

                    


                      

        </div><!--end col-lg-12 -->

        <div class="col-xs-4">
          <input type="submit" class="btn btn-md btn-success" name='submit' value="Save" />
          <div id="alert-submit" style="margin-top:20px"></div>

        </div>
         {{Form::close()}}

      </div><!--end row-->

     

    </section>

@stop

@section('footer-scripts')
<!-- Page script -->
<script>
  
  $(function () {
   'use strict';




   $('#newUserType').on('submit', function(e) {

      var _token = "{{ csrf_token() }}";
     
      var name = $('#name').val();
      var description = $('#description').val();
      var alertName = $('#alert-name');
      var roles = [];
      $("input[name='roles[]']:checked").each(function(){roles.push($(this).val());});
     


      if (!validateRequired(name,alertName,"enter user type")) { 
        console.log('not valid Name '); e.preventDefault(); e.stopPropagation();
        return false;
      }  else {
            //save first the new position
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

          
          }//end else valid new position

        
      
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
<!-- end Page script -->
@stop