@extends('layouts.main')

@section('metatags')
<title>Employee Engagement Activities | EMS</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('UserController@index')}}"> All Employees</a></li>
        <li class="active">My Team</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">

              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4);padding:30px">

                

                
                {!! $engagement[0]->content!!}


               
               
                <hr/>
                @if($canModerate)
                <div class="row">
                  <div class="col-lg-12">
                    <!-- ******** collapsible box ********** -->
                          <div class="box box-primary collapsed-box" style="margin-top: 20px">
                            <div class="box-header with-border">
                             <h3 class="text-primary">All Posts <strong class="text-orange">({{count($userEntries)}})</strong> :</h3>
                              <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>
                              </div>
                              <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table class="table table-bordered" id="allEntries">
                                  <thead>
                                    <tr>
                                      <th>From</th>
                                      <th>Image</th>
                                      <th>Guess</th>
                                      
                                      <th>Facebook</th>
                                      <th>Instagram</th>
                                      <th>Date Posted</th>
                                      <th>Status</th>
                                      
                                      <th class="text-center">Actions</th>
                                    </tr>
                                  </thead>

                                  <tbody>
                                     @foreach($userEntries as $e)
                                      
                                    
                                     


                                            <?php $img = collect($e)->where('elemType','IMG');
                                                  $link = collect($e)->where('elemType','TXT');
                                                  $fb = collect($e)->where('label','FB account name'); 
                                                  $ig = collect($e)->where('label','IG Handle'); ?>
                                        <tr>
                                          <td>{{$e[0]->lastname}},{{$e[0]->firstname}} -- <strong>{{$e[0]->program}} </strong> </td>
                                            @if(count($img)>0)
                                            <td><a href="../storage/uploads/{{$img->first()->value}}" target="_blank"><img src="../storage/uploads/{{$img->first()->value}}" width="120" /></a></td>
                                            @else
                                            <td>None</td>

                                            @endif

                                            @if(count($link) > 0)
                                            <td><h4 class="text-primary"> {!! $link->first()->value !!}</h4></td>
                                            @else
                                            <td>None</td>
                                            @endif

                                            @if(count($fb)>0)
                                            <td> <div class="editable" style="margin:20px;">
                                              <a href="https://www.facebook.com/{{$fb->first()->value }}"> {{$fb->first()->value}}</a></div></td>
                                            @else
                                            <td>None</td>
                                            @endif

                                            @if(count($ig)>0)
                                            <td> <div class="editable" style="margin:20px;"><a href="https://www.instagram.com/{{$ig->first()->value}}/" target="_blank"> {{$ig->first()->value}}</a></div></td>
                                            @else
                                            <td>None</td>
                                            @endif

                                            <td>{{$e[0]->created_at}} </td>
                                            @if($e[0]->disqualified)
                                            <td><em>Disqualified</em></td>
                                            @else
                                            <td>
                                              <em>Posted</em> <br/>
                                            </td>
                                            @endif



                                           

                                          <td class="actions">
                                            
                                            <a class="btn btn-xs btn-default pull-right" data-toggle="modal" data-target="#delModal{{$e[0]->entryID}}"><i class="fa fa-trash"></i> Delete </a> 
                                          </td>
                                          
                                        </tr>



                                  


                                      <div class="modal fade" id="adminModal{{$e[0]->entryID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                          <div class="modal-dialog">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                
                                                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                  <h4 class="modal-title" id="myModalLabel">Delete this post</h4>
                                                
                                              </div>
                                              <div class="modal-body">
                                                Are you sure you want to delete this post for  <strong>{{$engagement[0]->activity}}</strong>?
                                              </div>
                                              <div class="modal-footer no-border">
                                                {{ Form::open(['route' => ['employeeEngagement.deletePost',$e[0]->entryID], 'method'=>'POST','class'=>'btn-outline pull-right','id'=>$e[0]->entryID ]) }} 

                                                  <button type="submit" class="del btn btn-primary"><i class="fa fa-trash"></i> Yes </button>
                                                
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                                              </div>
                                            </div>
                                          </div>
                                        </div> 

                                       

                                   

                                    @endforeach

                                    
                                  </tbody>
                                </table>
                          
                              <div class="clearfix"></div>
                            


                              
                            </div>
                            <!-- /.box-body -->
                          </div>
                         <!-- ******** end collapsible box ********** -->
                  </div>
                </div>
                @endif
                <div class="row">
                  
                    @if ($hasEntry)
                      <div id="entry" class="col-sm-12" style="background: rgba(256, 256, 256, 0.7);padding:30px" >
                        


                        

                        <!-- ******** collapsible box ********** -->
                          <div class="box box-primary collapsed-box" style="margin-top: 20px">
                            <div class="box-header with-border">
                             <h3 class="text-primary">Your Submitted Entries <strong class="text-orange">({{count($allPosts)}})</strong> :</h3>
                              <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>
                              </div>
                              <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      
                                      <th>Image</th>
                                      <th>Guess</th>
                                      
                                      <th>Facebook</th>
                                      <th>Instagram</th>
                                      <th>Date Posted</th>
                                      <th>Status</th>
                                      
                                      <th class="text-center">Actions</th>
                                    </tr>
                                  </thead>

                                  <tbody>
                                     @foreach($allPosts as $e)

                                     


                                            <?php $img = collect($e)->where('elemType','IMG');
                                                  $link = collect($e)->where('elemType','TXT');
                                                  $fb = collect($e)->where('label','FB account name'); 
                                                  $ig = collect($e)->where('label','IG Handle'); ?>

                                        <tr>
                                            @if(count($img)>0)
                                            <td><a href="../storage/uploads/{{$img->first()->value}}" target="_blank"><img src="../storage/uploads/{{$img->first()->value}}" width="120" /></a></td>
                                            @else
                                            <td>None</td>

                                            @endif

                                            @if(count($link) > 0)
                                            <td><h4 class="text-primary"> {!! $link->first()->value !!}</h4></td>
                                            @else
                                            <td>None</td>
                                            @endif

                                            @if(count($fb)>0)
                                            <td> <div class="editable" style="margin:20px;">
                                              <a href="https://www.facebook.com/{{$fb->first()->value }}"> {{$fb->first()->value}}</a></div></td>
                                            @else
                                            <td>None</td>
                                            @endif

                                             @if(count($ig)>0)
                                            <td> <div class="editable" style="margin:20px;">
                                              <a href="https://www.instagram.com/{{$ig->first()->value }}"> {{$ig->first()->value}}</a></div></td>
                                            @else
                                            <td>None</td>
                                            @endif

                                            <td>{{$e[0]->created_at}} </td>
                                            @if($e[0]->disqualified)
                                            <td><em>Disqualified</em></td>
                                            @else
                                            <td>
                                              <em>Posted</em> <br/>
                                            </td>
                                            @endif



                                           

                                          <td class="actions">
                                            
                                            <a class="btn btn-xs btn-default pull-right" data-toggle="modal" data-target="#delModal{{$e[0]->entryID}}"><i class="fa fa-trash"></i> Delete </a> 
                                          </td>
                                          
                                        </tr>



                                     

                                      <div class="modal fade" id="delModal{{$e[0]->entryID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                          <div class="modal-dialog">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                
                                                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                  <h4 class="modal-title" id="myModalLabel">Delete this post</h4>
                                                
                                              </div>
                                              <div class="modal-body">
                                                Are you sure you want to delete your post for  <strong>{{$engagement[0]->activity}}</strong>?
                                              </div>
                                              <div class="modal-footer no-border">
                                                {{ Form::open(['route' => ['employeeEngagement.deletePost',$e[0]->entryID], 'method'=>'POST','class'=>'btn-outline pull-right','id'=>$e[0]->entryID ]) }} 

                                                  <button type="submit" class="del btn btn-primary"><i class="fa fa-trash"></i> Yes </button>
                                                
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
                                              </div>
                                            </div>
                                          </div>
                                        </div> 

                                       

                                   

                                    @endforeach

                                    
                                  </tbody>
                                </table>
                          
                              <div class="clearfix"></div>
                            


                              
                            </div>
                            <!-- /.box-body -->
                          </div>
                         <!-- ******** end collapsible box ********** -->

                         @if($engagement[0]->multipleEntry)
                         <!-- ************* POST NEW ************** -->
                         <div style="padding:30px">
                               <h3 class="text-primary">Submit your <span class="text-danger"> Masterpiece! <i class="fa fa-paint-brush"></i> </span></h3>
                                <?php $ctr=0; ?>

                                      <!-- <form id="form" accept-charset="UTF-8" enctype="multipart/form-data"> -->
                                        {{ Form::open(['route' => 'employeeEngagement.saveEntry2','id'=> 'form','itemids'=>$itemIDs, 'name'=>'form','accept-charset'=>'UTF-8','enctype'=>"multipart/form-data" ]) }}
                                        <input type="hidden" name="anonymous" id="anonymous" value="0" />

                                @foreach($engagement as $element)<br/>
                                <label style="padding-top: 20px">{{$element->label}} </label>

                                    @if( $element->dataType == 'TXT' )
                                    <input type="text" name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control" style="width: 50%" placeholder="http://www.link-to-your-online-portfolio" />

                                    @endif

                                    @if( $element->dataType == 'PAR' )
                                    <textarea name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control"></textarea>
                                    @endif

                                    @if( $element->dataType == 'IMG' )
                                     <p><span class="text-danger"><i class="fa fa-exclamation-circle"></i> Note: </span>Please limit your image uploads to a <strong>maximum of 1.5MB</strong> per image.<br/></p>
                                    
                                    <input type="file" name="file" id="file" accept="image/*"   class="form-control" data-itemID="{{$element->itemID}}" /><br/><br/>
                                    
                                    @endif



                                @endforeach

                                
                                <input type="submit" name="upload" value="Send Entry " class="btn btn-lg btn-success" style="margin: 20px 0 30px 0;"> </input>

                                <div class="progress">
                                          <div class="progress-bar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                              0%
                                          </div>
                                </div>
                                <div id="success" class="row"></div>
                                <input type="hidden" name="itemids" id="itemids" value="{{$itemIDs}}" />
                                <input type="hidden" name="itemtypes" id="itemtypes" value="{{$itemTypes}}" />
                                <input type="hidden" name="engagement_id" value="{{$id}}" />
                                {{Form::close()}}
                          </div>
                          <!-- ************* POST NEW ************** -->
                          @endif
                        </div>

                        




                    @else
                   <div id="entry" class="col-sm-12" style="background: rgba(256, 256, 256, 0.7);padding:30px" >
                        <h3 class="text-primary">Submit your <span class="text-danger"> Guess! <i class="fa fa-search"></i> </span></h3>
                        <?php $ctr=0; ?>

                              <!-- <form id="form" accept-charset="UTF-8" enctype="multipart/form-data"> -->
                                {{ Form::open(['route' => 'employeeEngagement.saveEntry2','id'=> 'form','itemids'=>$itemIDs, 'name'=>'form','accept-charset'=>'UTF-8','enctype'=>"multipart/form-data" ]) }}
                                <input type="hidden" name="anonymous" id="anonymous" value="0" />

                        @foreach($engagement as $element)<br/>
                        <label style="padding-top: 20px">{{$element->label}} </label>

                            @if( $element->dataType == 'TXT' )
                            <input type="text" name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control" style="width: 50%"  />

                            @endif

                            @if( $element->dataType == 'PAR' )
                            <textarea required="required" name="item_{{$element->itemID}}" data-itemID="{{$element->itemID}}" class="form-control"></textarea>
                            @endif

                            @if( $element->dataType == 'IMG' )
                             <p><span class="text-danger"><i class="fa fa-exclamation-circle"></i> Note: </span>Please limit your image uploads to a <strong>maximum of 1.5MB</strong> per image.<br/></p>
                            
                            <input type="file" name="file" id="file" accept="image/*"   class="form-control" data-itemID="{{$element->itemID}}" />
                            
                            @endif



                        @endforeach

                        
                        <input type="submit" name="upload" value="Send Entry " class="btn btn-lg btn-success" style="margin: 20px 0 30px 0;"> </input>

                         <div class="progress">
                                  <div class="progress-bar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                      0%
                                  </div>
                              </div>

                              <div id="success" class="row"></div>

                         
                          <input type="hidden" name="itemids" id="itemids" value="{{$itemIDs}}" />
                          <input type="hidden" name="itemtypes" id="itemtypes" value="{{$itemTypes}}" />
                          
                          <input type="hidden" name="engagement_id" value="{{$id}}" />
                         {{Form::close()}}

                       </div>

                      
                  

                    @endif

                    

                    
                    
                  

                  
                  
                </div>


               




              </div><!--end box-primary-->


             

             

          </div><!--end main row-->
      </section>

   

      
 

    



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<script type="text/javascript" src="../public/js/jquery.form.min.js"></script>

<!-- Page script -->
<script>

   $(document).ready(function(){

    $('.row').on('click','input.flag',function(){

        var d = $(this);
        var q = null;
        var _token = "{{ csrf_token() }}";

        if (d.is(":checked")) q=1; else q=0;

        console.log(q);
        $.ajax({

                  url:"{{action('EngagementController@disqualify')}}",
                  type:'POST',
                  data:{

                    'entry_id': $(this).attr('data-entryid'),
                    'q': q,
                    
                    _token: _token

                  },
                  error: function(response)
                  { console.log("Error saving entry: ");
                    console.log(response);
                    $.notify("Error processing request. Please check all submitted fields and try again.",{className:"error", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} ); 
                    return false;
                  },
                  success: function(response)
                  {
                    console.log(response);
                    if(q)
                      $.notify("Post tagged as disqualified.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                    else
                      $.notify("Post unflagged as disqualified.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
                    

                  }

            });



       
    });

   
    $('#allEntries').DataTable();

    $('.del.btn').on('click',function(){
      setTimeout(function () {
        window.location="{{action('EngagementController@show',$id)}}";
  
    }, 500);

     
    });
   



    $('form').ajaxForm({
        beforeSend:function(){

            $('#success').empty();
            $('.progress-bar').text('0%');
            $('.progress-bar').css('width', '0%');

        },
        uploadProgress:function(event, position, total, percentComplete){
            $('.progress-bar').text(percentComplete + '0%');
            $('.progress-bar').css('width', percentComplete + '0%');
        },
        success:function(data)
        {
            if(data.success)
            {
                console.log(data);
                $('input[name="upload"]').fadeOut();
                $('#success').html('<h2 class="text-success">Thank you for participating!</h2><br /><br />');
                //$('#success').append(data.image);
                $('.progress-bar').text('Entry Uploaded!');
                $('.progress-bar').css('width', '100%');
               
            }
            return false;

        }
    });
});



</script>
<!-- end Page script -->



@stop