@extends('layouts.main')

@section('metatags')
<title> {{$campaign->name}} | Open Access EMS</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

<!-- 
 -->
<style type="text/css">


div.items {
  white-space: nowrap;
  flex-flow: row nowrap;
  justify-content: space-between;
  overflow: hidden;
  display: flex;
  align-self: center;
}
div.items:hover .item {
  opacity: 0.3;
}
div.items:hover .item:hover {
  opacity: 1;
}
div.control-container {
  height: 300px;
  position: absolute;
  width: 100%;
  overflow: hidden;
  box-sizing: border-box;
}
div.container {
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  min-height: 200px;
  position: relative;
  width: 100%;
  box-sizing: border-box;
  overflow: hidden;
  display: flex;
  flex-flow: row nowrap;
  justify-content: center;
}
div.left-scroll {
  left: 0;
}
div.left-scroll i {
  -webkit-transform: translate(-60%, -50%);
          transform: translate(-60%, -50%);
}
div.right-scroll {
  right: 0;
}
div.right-scroll i {
  -webkit-transform: translate(-40%, -50%);
          transform: translate(-40%, -50%);
}
div.scroll {
  position: absolute;
  display: inline-block;
  color: #f6f6f6;
  top: 50%;
  -webkit-transform: translate(0, -50%);
          transform: translate(0, -50%);
  width: 60px;
  height: 60px;
  border: 1px solid #f6f6f6;
  border-radius: 60px;
  margin: 0 5px;
  z-index: 951;
}
div.scroll i {
  font-size: 30px;
  position: relative;
  left: 50%;
  top: 50%;
}

.item {
  position: relative;
  align-self: center;
  width: 200px;
  height: 200px;
  margin: 0 3px;
  transition: all 0.3s ease-in-out;
  cursor: pointer;
  z-index: 899;
}
.item:hover {
  -webkit-transform: scale(1.5);
          transform: scale(1.5);
  margin: 30px;
  opacity: 1;
  z-index: 950;
}
.item:hover .opacity-none {
  opacity: 1;
}
.item .item-load-icon {
  left: 50%;
  top: 50%;
  -webkit-transform: translate(-50%, -50%);
          transform: translate(-50%, -50%);
}
.item .opacity-none {
  opacity: 0;
}
.item img.item-image {
  width: 200px;
  height: 200px;
  -o-object-fit: cover;
     object-fit: cover;
}
.item .item-title {
  color: #f6f6f6;
  position: absolute;
  margin: 5px 0;
  padding: 5px 0;
  width: 100%;
  left: 50%;
  top: 0px;
  -webkit-transform: translate(-50%, 0);
          transform: translate(-50%, 0);
  background: rgba(0, 0, 0, 0.5);
  text-align: center;
  font-size: 0.7em; font-weight: bold;
}
.item .item-description {
  color: #f6f6f6;
  font-size: 12px;
  position: absolute;
  bottom: 0;
  left: 50%;
  -webkit-transform: translate(-50%, 0);
          transform: translate(-50%, 0);
  white-space: pre-wrap;
  width: 100%;
  background: rgba(0, 0, 0, 0.5);
  margin: 5px 0;
  padding: 10px 0;
}

.button {
  position: absolute;
  color: #f6f6f6;
  font-size: 30px;
  border: 1px solid #f6f6f6;
  width: 60px;
  height: 60px;
  border-radius: 60px;
  z-index: 950;
  background-color: rgba(0, 0, 0, 0.7);
  transition: all 0.3s ease-in-out;
}
.button i {
  position: relative;
  top: 50%;
  left: 50%;
  -webkit-transform: translate(-35%, -55%);
          transform: translate(-35%, -55%);
  z-index: 950;
}
.button:hover {
  box-shadow: 0px 0px 50px #FFFFFF;
}

  

</style>



@endsection

@section('bodyClasses')

@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1 class="pull-left">
        @if(is_null($logo))
          
          {{$campaign->name}}

        @elseif ($campaign->name=='IMO')
        <img src="../public/img/{{$logo[0]->filename}}" height="55" class="pull-left"  />


        @else
        <img src="../public/img/{{$logo[0]->filename}}" width="150px" class="pull-left" style="margin-top: 20px" />
        @endif
        
      </h1>
      

      @if(is_null($logo))
      <a href="{{action('CampaignController@index')}}" class="btn btn-xs btn-default" style="margin-left: 60px"><i class="fa fa-arrow-left">
        @else
         <a href="{{action('CampaignController@index')}}" class="btn btn-xs btn-default" style="margin-top:30px; margin-left: 60px"><i class="fa fa-arrow-left">
          @endif
       



      </i> Back to all Programs </a>
      
      @if(!is_null($campaign->has_vicidial))
        
        
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('campaignStats/'.$campaign->id) }}">View Campaign Stats</a>
      <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('agentStats/'.$campaign->id) }}">View Agent Stats</a>
      
      @endif
      
      
      @if(!is_null($has_id_permissions) && $has_id_permissions == TRUE)
        <a style="margin-top:30px; margin-left: 10px" class="btn btn-xs btn-default" href="{{ url('camera/by_campaign/'.$campaign->id) }}">Print IDs</a>
      @endif
      
      <div class="clearfix"></div>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Programs</li>
      </ol>
    </section>

     <section class="content">

        <?php $ctr = 1; ?>
        @foreach($TLs as $leader)
        <?php $hasMembers = collect($members)->where('tlID',$leader->tlID);
        $hisOwn = collect($members)->where('userID',$leader->userID);
        $actualMembers = count($hasMembers) - count($hisOwn); //we need to less himself from the count
        $tlcount = count($TLs); 
         ?>

          <div class="row">
            
            <div class="col-lg-12">
              
             

              <div class="box box-default direct-chat direct-chat-default @if(count($hasMembers) <= 0) collapsed-box @endif " style="background: rgba(256, 256, 256, 0.4)">
                  <div class="box-header with-border">
                    
                    <!-- THE TL -->
                    <h3 class="box-title" style="width:80%"><a target="_blank" href="{{action('UserController@show',$leader->userID)}}">
                      @if ( file_exists('public/img/employees/'.$leader->userID.'.jpg') )
                      <img src="{{asset('public/img/employees/'.$leader->userID.'.jpg')}}"  class="img-circle pull-left" alt="User Image"  width="90" style="padding-right:5px">
                      @else
                        <img src="{{asset('public/img/useravatar.png')}}" class="img-circle pull-left" width="90" alt="Employee Image"style="padding-right:5px">

                        @endif

                        @if (is_null($leader->TLnick) || empty($leader->TLnick)) 
                         <span style="color:#333"> Team </span><span style="text-transform:uppercase">  {{$leader->TLfname}} {{$leader->TLlname}}</span>
                        @else
                       <span style="color:#333"> Team </span> <span style="text-transform:uppercase">  {{$leader->TLnick}} {{$leader->TLlname}}</span>

                        @endif
                        @if (count($hasMembers) > 0)<span class="badge bg-orange">{{count($hasMembers)}} </span> <em style="font-size: xx-small;"> member(s) </em>@endif
                    </a><br/>
                      <small >{{$leader->jobTitle}}</small>





                    </h3>

                    <div class="box-tools pull-right">    
                      
                      @if($canEdit)
                      <a href="" title="Remove leader from program/campaign" data-toggle="modal" data-target="#myModal_leader{{$leader->tlID}}"><i class="fa fa-trash"></i></a>

                      @include('layouts.modals-leader', [
                        'modelRoute'=>'immediateHeadCampaign.disable',
                        'modelID' => $leader->tlID, 
                        'modelName'=>" ". $leader->TLfname . " from ". $campaign->name, 
                        'modalTitle'=>'Remove leader: ', 
                        'modalMessage'=>'Are you sure you want to remove him/her from this program/campaign?', 
                        'formID'=>'disableIH',
                        'icon'=>'glyphicon-trash' ])

                      @endif

                      @if (count($hasMembers) > 0 )
                      <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Hide"><i class="fa fa-minus"></i></button>
                      @endif 
                      
                    </div>

                  </div>
                  <!-- /.box-header -->

                  <div class="box-body" style="background: rgba(256, 256, 256, 0.4)">
                      <!-- Conversations are loaded here -->
                      <div class="direct-chat-messages" style="min-height:300px">
                        <div id="carousel_{{$ctr}}" class="container">
                            <div class="control-container">
                            <div id="left-scroll-button_{{$ctr}}" class="left-scroll button scroll">
                              <i class="fa fa-chevron-left" aria-hidden="true"></i>
                            </div>
                            <div id="right-scroll-button_{{$ctr}}" class="right-scroll button scroll">
                              <i class="fa fa-chevron-right" aria-hidden="true"></i>
                            </div>
                            </div>

                             

                            <div class="items" id="carousel-items_{{$ctr}}">

                              


                                     @foreach($hasMembers as $member)
                                     <div class="item">
                                      @if ( file_exists('public/img/employees/'.$member->userID.'.jpg') )
                                        <img class="item-image" src={{asset('public/img/employees/'.$member->userID.'.jpg')}} />
                                      @else
                                        <img class="item-image" src="{{asset('public/img/useravatar.png')}}" />

                                      @endif

                                      @if(!is_null($member->nickname)) 
                                      <span class="item-title"> {{$member->lastname}}, {{$member->nickname}} </span>
                                     
                                      @else
                                      <span class="item-title"> {{$member->lastname}}, {{$member->firstname}} </span>

                                      @endif


                                      
                                      <a href="{{action('UserController@show',$member->userID)}}" target="_blank"><span class="item-load-icon button opacity-none" style="font-size: x-small;"><i class="fa fa-play"></i><br/> 
                                      View Profile</span></a>
                                      <div class="item-description opacity-none text-center" style="font-size: xx-small;" >{{$member->jobTitle }}</div>
                                    </div>

                                     
                                                             

                                     @endforeach

                   


                              
                            </div>

                            
                        </div>
                      </div>
                  </div>
                  <div class="box-footer" style="background: rgba(256, 256, 256, 0.8)">
                  </div>
                  <!-- /.box-footer-->
              </div>
                <!--/.direct-chat -->
              
              
            
            

           
          </div><!--end row-->
          
          <?php $ctr++;?>
        </div>
        @endforeach
      
      </section>
          



@endsection


@section('footer-scripts')

<script>
  function MouseWheelHandler(e, element) {
  var delta = 0;
  if (typeof e === 'number') {
    delta = e;
  } else {
    if (e.deltaX !== 0) {
      delta = e.deltaX;
    } else {
      delta = e.deltaY;
    }
    e.preventDefault();
  }

  element.scrollLeft -= (delta);

}

window.onload = function() {
 
  <?php $ctr=1;?>
  @foreach($TLs as $tl)


  
  var carousel_{{$ctr}} = {};
  carousel_{{$ctr}}.e = document.getElementById('carousel_{{$ctr}}');
  carousel_{{$ctr}}.items = document.getElementById('carousel-items_{{$ctr}}');
  carousel_{{$ctr}}.leftScroll = document.getElementById('left-scroll-button_{{$ctr}}');
  carousel_{{$ctr}}.rightScroll = document.getElementById('right-scroll-button_{{$ctr}}');

  carousel_{{$ctr}}.items.addEventListener("mousewheel", handleMouse{{$ctr}}, false);
  carousel_{{$ctr}}.items.addEventListener("scroll", scrollEvent{{$ctr}});
  carousel_{{$ctr}}.leftScroll.addEventListener("click", leftScrollClick{{$ctr}});
  carousel_{{$ctr}}.rightScroll.addEventListener("click", rightScrollClick{{$ctr}});
 /* carousel.leftScroll.addEventListener("mousedown", leftScrollClick);
  carousel.rightScroll.addEventListener("mousedown", rightScrollClick);*/

  

  setLeftScrollOpacity_{{$ctr}}();
 
  setRightScrollOpacity_{{$ctr}}();
  

  
  function handleMouse{{$ctr}}(e) {
    MouseWheelHandler(e, carousel_{{$ctr}}.items);
  }

  function leftScrollClick{{$ctr}}() {
    MouseWheelHandler(100, carousel_{{$ctr}}.items);
  }

  function rightScrollClick{{$ctr}}() {
    MouseWheelHandler(-100, carousel_{{$ctr}}.items);
  }

  function scrollEvent{{$ctr}}(e) {
    setLeftScrollOpacity_{{$ctr}}();
    setRightScrollOpacity_{{$ctr}}();
  }
  
  function setLeftScrollOpacity_{{$ctr}}() {
    if ( isScrolledAllLeft(carousel_{{$ctr}}) ) {
      carousel_{{$ctr}}.leftScroll.style.opacity = 0;
    } else {
      carousel_{{$ctr}}.leftScroll.style.opacity = 1;
    }
    
  }
  
  
  
  function setRightScrollOpacity_{{$ctr}}() {
    if ( isScrolledAllRight(carousel_{{$ctr}}) ){
        carousel_{{$ctr}}.rightScroll.style.opacity = 0;
      } else {
        carousel_{{$ctr}}.rightScroll.style.opacity = 1;
      }
    
  }

  

  <?php $ctr++;?>
  @endforeach

  function isScrolledAllLeft(x) {
    if (x.items.scrollLeft === 0) {
      return true;
    } else {
      return false;
    }
  }
  
  function isScrolledAllRight(x) {
    if (x.items.scrollWidth > x.items.offsetWidth) {
      if (x.items.scrollLeft + x.items.offsetWidth === x.items.scrollWidth) {
        return true;
      } 
    }else {
      return true;
    }
    
    return false;
  }


  


}
</script>



<!-- Page script -->
<script>

  $(function () {
   'use strict';



   

   


      
      
   });

   

  /*
   * Custom Label formatter
   * ----------------------
   */
  function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        + "<br>"
        + Math.round(series.percent) + "%</div>";
  }
</script>
<!-- end Page script -->


@stop