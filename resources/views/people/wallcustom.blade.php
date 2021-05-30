<!DOCTYPE html>
<html>
<head>
	

	<title> (<?php echo count($posts); ?>) Open Access BPO Wall</title>
	<!-- <link href="https://fonts.googleapis.com/css?family=Gochi+Hand|Montserrat&display=swap" rel="stylesheet"> -->
	
	<!-- <link href="https://fonts.googleapis.com/css?family=Gochi+Hand|Montserrat&display=swap" rel="stylesheet"> -->
	<link rel="stylesheet" type="text/css" href="{{url('/')}}/public/css/wall/assets/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="{{url('/')}}/public/css/wall/assets/css/stylecustom.css">
	<style type="text/css">
		.freedom-wall {
		  position: relative;
		  /*background-image: url('../img/banner.png');
		  background-image: url('../img/polaroid.jpg');*/
		  
		  /*background-image: url("{{url('/')}}/storage/uploads/{{$background}}");*/
		  background-image: url("https://internal.openaccess.bpo/EMS/public/storage/uploads/{{$background}}");
		  background-color: {{$bgcolor}};
		  background-size: 100% auto;
		  background-position: top center;
		  background-repeat: no-repeat;
		  margin: 0;
		  min-height: 100vh;
		  display: flex;
		  align-items: center;
		}

		.freedom-wall::before {
		  content: '';
		  width: 100%;
		  height: 100%;
		  /*background-color: black;*/
		  background-image: url("{{url('/')}}/storage/uploads/{{$background}}") top fixed no-repeat;
		  background-color: transparent;
		  position: absolute;
		  top: 0;
		  left: 0;
		  opacity: .5;
		}
		.freedom-wall ul li:nth-child(12n+1) a, .freedom-wall ul li:nth-child(12n+12) a {
		  background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		  
		  background-color: #e3c01e;
		}

		.freedom-wall ul li:nth-child(12n+2) a, .freedom-wall ul li:nth-child(12n+11) a {
		 background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		  background-color: #d77a2a;
		}

		.freedom-wall ul li:nth-child(12n+3) a, .freedom-wall ul li:nth-child(12n+10) a {
		  background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		  background-color: #c6492b; /*2b86c6*/
		}

		.freedom-wall ul li:nth-child(12n+4) a, .freedom-wall ul li:nth-child(12n+9) a {
		  background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		  background-color: #8aad4f;
		}

		.freedom-wall ul li:nth-child(12n+5) a, .freedom-wall ul li:nth-child(12n+8) a {
		  background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		  background-color: #76b5e0;

		}

		.freedom-wall ul li:nth-child(12n+6) a, .freedom-wall ul li:nth-child(12n+7) a {
		 background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		  background-color: #613d87;
		}

		.freedom-wall ul li:nth-child(6n+1) a {
		  background-color: #e3c01e;
		  background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		}

		.freedom-wall ul li:nth-child(6n+2) a {
		  background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		  background-color: #d77a2a;
		}

		.freedom-wall ul li:nth-child(6n+3) a {
		  background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		  background-color: #c6492b; /*#2b86c6;*/
		}

		.freedom-wall ul li:nth-child(6n+4) a {
		  background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		  background-color: #8aad4f;
		}

		.freedom-wall ul li:nth-child(6n+5) a {
		  background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		  background-color: #76b5e0;

		}

		.freedom-wall ul li:nth-child(6n+6) a {
		  background-image: url("{{url('/')}}/public/css/wall/assets/img/openaccess.svg");
		  background-color: #613d87;
		}

		.freedom-wall ul li a.polaroid {
		  background-image: url("{{url('/')}}/public/css/wall/assets/img/polaroid1.png");
		  justify-content: flex-start;
		}

		.message p{ white-space: pre-line; }
	</style>

	<!-- <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> 
	<link href="{{asset('public'.elixir('css/all.css'))}}" rel="stylesheet" />-->
	<link href="{{asset('public/css/font-awesome.min.css')}}" rel="stylesheet" />

</head>
<body translate="no" style="">
	<div class="preloader"></div>
	
	<div class="freedom-wall">
		

		<ul data-arrlen="{{$allpostCount}}" data-postcount="<?php echo count($posts); ?>" @if (isset($_GET['c'])) data-maxpost="{{$_GET['c']}}"  @else data-maxpost @endif

		data-fpost="<?php echo $posts[0]['id']; ?>"
		data-lpost="{{$lastPost['id']}}"
		>
			<?php
				if (isset($_GET["f"]) && isset($_GET["l"]) && isset($_GET['c'])) {
					$fp=$_GET["f"];
					$lp=$_GET["l"];
					$postCount=$_GET["c"];
					//$i=lp;

					for($i=$lp; $i <= $fp; $i++)
					{

							//if ($i++ == $postCount) break;
						    $likes = collect($allLikes)->where('entryID',$posts[$i]['id']); 
						    $likedAlready = collect($allLikes)->where('entryID',$posts[$i]['id'])->where('user_id',$user_id); 
						    $commentDito = collect($allComments)->where('entryID',$posts[$i]['id']);

							if ($posts[$i]['img'] !== "" && !empty($posts[$i]['img'])) {
								$bg = 'background-image: url('.$posts[$i]["img"].');';
							}

							else {
								$bg = "";
							}

							$length = 90;
							$end = "...";
							$string = strip_tags($posts[$i]['message']);

						    if (strlen($string) > $length) {

						        // truncate string
						        $stringCut = substr($string, 0, $length);

						        // make sure it ends in a word so assassinate doesn't become ass...
						        $string = substr($stringCut, 0, strrpos($stringCut, ' ')).$end;
						    }

						    
							?>

							@if (count($likedAlready) > 0)
							<li class="post-it" data-post="{{$i}}" data-totalLikes="{{count($likes)}}" data-entryID="{{$posts[$i]['id']}}" data-already="1" id="post-it{{$posts[$i]['id']}}">
							@else
							<li class="post-it" data-post="{{$i}}" data-totalLikes="{{count($likes)}}" data-entryID="{{$posts[$i]['id']}}" data-already="0" id="post-it{{$posts[$i]['id']}}">
							@endif
							
							
								<a href="#"
									<?php
										if ($bg != null && $bg != 'background-image: url();') {
											echo "class='polaroid'";
										}

										else {
											echo "style=$bg";
										}
									?>
									>

									<div class="img" style="<?php echo $bg; ?>">
										
									</div>
									<p style="display: none;">
										{!! $posts[$i]['message']; !!}
										
										<span class="text-primary">From: <?php echo $posts[$i]['from']; ?>
											<br/>
											<small style="font-size: x-small;">Posted: {{date('M d h:i A',strtotime($posts[$i]['datePosted'])) }} </small>
										</span>

									</p>
									<div class="comments" style="display: none;">

										@if(count($commentDito) > 0)
										<h3 class="text-orange mCommentsHeader{{$posts[$i]['id']}}" style="margin-bottom: 20px">Comments ({{count($commentDito)}}): </h3>

										@else
										<h3 class="text-orange mCommentsHeader{{$posts[$i]['id']}}" style="margin-bottom: 20px">Comments: </h3>

										@endif

										
										
									
									
									@foreach($commentDito as $cmt)

										@if($cmt->anonymous)
										<p class="comment{{$cmt->commentID}}" style="margin-bottom: 10px; white-space: pre;background-color: #383a3c; padding:20px; width:75%">{!! $cmt->comment !!} -- <strong class="text-primary"><i class="fa fa-user"></i> <em>Anonymous</em>, {{$cmt->program}}  </strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small style="color: #666">[ Posted {{ date('l, M d h:i A',strtotime($cmt->created_at))}} ]</small>
											@if($user_id == $cmt->user_id)
											<i class="fa fa-trash" style="cursor: pointer; float: right; padding:10px;" data-commentID="{{$cmt->commentID}}"> Delete</i> 
											@endif
										</p>
										

										@else
										<p class="comment{{$cmt->commentID}}" style="margin-bottom: 10px; white-space: pre; background-color: #383a3c; padding:20px; width:75%">{!! $cmt->comment !!} -- <strong class="text-primary"><i class="fa fa-user"></i> {{$cmt->nickname}} {{$cmt->lastname}}, {{$cmt->program}} </strong>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small style="color: #666">[ Posted {{ date('l, M d h:i A',strtotime($cmt->created_at))}} ]</small>
											@if($user_id == $cmt->user_id)
											<i class="fa fa-trash" style="cursor: pointer; float: right; padding:10px;" data-commentID="{{$cmt->commentID}}"> Delete</i> 
											@endif
										</p>

										@endif

										
									@endforeach
									
								</div>
									<p>
										<?php
											echo $string;
										?>
									</p>

									@if(count($likes) > 0 || count($commentDito) > 0)
										<span>From: <?php echo  $posts[$i]['from']; ?> &nbsp;&nbsp;(<b id="likecounter{{$posts[$i]['id']}}">{{count($likes)}}</b>) <i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;(<b id="commentcounter{{$posts[$i]['id']}}">{{count($commentDito)}}</b>) <i class="fa fa-comments"></i> </span>
										@else
										<span>From: <?php echo  $posts[$i]['from']; ?> &nbsp;&nbsp; <!-- <br/>
										<small style="font-size: x-small;">Posted: {{date('M d h:i A',strtotime($posts[$i]['datePosted'])) }} </small> --></span>
										@endif


									
									
								</a>
							</li>
							<?php
						

					}

					
					
				}

				else {
					$postCount=$allpostCount;

					$i=0;

					foreach ($posts as $post) {
						$likes = collect($allLikes)->where('entryID',$post['id']);
						$likedAlready = collect($allLikes)->where('entryID',$post['id'])->where('user_id',$user_id);
						$comments = collect($allComments)->where('entryID',$post['id']);

						if ($i++ == $postCount) break;

						if ($post['img'] !== "" && !empty($post['img'])) {
							$bg = 'background-image: url('.$post["img"].');';
						}

						else {
							$bg = "";
						}

						$length = 90;
						$end = "...";

						$string = strip_tags($post['message']);

					    if (strlen($string) > $length) {
					        $stringCut = substr($string, 0, $length);
					        $string = substr($stringCut, 0, strrpos($stringCut, ' ')).$end;
					    }
					    
						?>
						@if (count($likedAlready) > 0)
						<li class="post-it" data-post="{{$i}}" data-totalLikes="{{count($likes)}}"  data-entryID="{{$post['id']}}" data-already="1" id="post-it{{$post['id']}}">
						@else
						<li class="post-it" data-post="{{$i}}" data-totalLikes="{{count($likes)}}"  data-entryID="{{$post['id']}}" data-already="0" id="post-it{{$post['id']}}">
						@endif
							<!--  echo $post['id'];  -->
							<a href="#"

								<?php
									if ($bg != null) {
										echo "class='polaroid'";
									}

									else {
										echo "style=$bg";
									}
								?>
								>

								<div class="img" style="<?php echo $bg; ?>">
									
								</div>
								<p style="display: none;">
									{!! $post['message']; !!}

									<span class="text-primary">From: <?php echo $post['from']; ?>
										<br/>
										<small style="font-size: x-small;">Posted: {{date('M d h:i A',strtotime($post['datePosted'])) }} </small>
									</span>
								</p>

								<div class="comments" style="display: none;">
									
									<?php
										
										$commentDito = collect($allComments)->where('entryID',$post['id']);
									?>
									@if(count($commentDito) > 0)
										<h3 class="text-orange mCommentsHeader{{$post['id']}}"  style="margin-bottom: 20px">Comments ({{count($commentDito)}}): </h3>

										@else
										<h3 class="text-orange mCommentsHeader{{$post['id']}}"  style="margin-bottom: 20px">Comments: </h3>

									@endif


									@foreach($commentDito as $cmt)

										

										@if($cmt->anonymous)
										<p class="comment{{$cmt->commentID}}" style="margin-bottom: 10px; white-space: pre; background-color: #383a3c; padding:20px; width:75%" >{!! $cmt->comment !!} -- <strong class="text-primary"><i class="fa fa-user"></i> <em>Anonymous</em>, {{$cmt->program}}  </strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small style="color: #666">[ Posted {{ date('l, M d h:i A',strtotime($cmt->created_at))}} ]</small>
											@if($user_id == $cmt->user_id)
											<i class="fa fa-trash" style="cursor: pointer; float: right ;padding:10px;" data-commentid="{{$cmt->commentID}}"> Delete</i>
											@endif
										</p>
										

										@else
										<p class="comment{{$cmt->commentID}}" style="margin-bottom: 10px; white-space: pre; background-color: #383a3c; padding:20px; width:75%">{!! $cmt->comment !!} -- <strong class="text-primary"><i class="fa fa-user"></i> {{$cmt->nickname}} {{$cmt->lastname}}, {{$cmt->program}} </strong>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<small style="color: #666">[ Posted {{ date('l, M d h:i A',strtotime($cmt->created_at))}} ]</small> &nbsp;&nbsp;
											@if($user_id == $cmt->user_id)
											<i class="fa fa-trash" style="cursor: pointer; float: right; padding:10px;" data-commentid="{{$cmt->commentID}}"> Delete</i>
											@endif
										</p>

										@endif


									@endforeach
									

									
								</div>


								<p>
									<?php
										echo $string;
									?>
								</p>
								@if(count($likes) > 0 || count($comments) > 0)
								<span>From: <?php echo $post['from']; ?> &nbsp;&nbsp;(<b id="likecounter{{$post['id']}}">{{count($likes)}}</b>) <i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;(<b id="commentcounter{{$post['id']}}">{{count($comments)}}</b>) <i class="fa fa-comments"></i> </span>
								@else
								<span>From: <?php echo $post['from']; ?> &nbsp;&nbsp;<!--  <br/>
										<small style="font-size: x-small;">Posted: {{date('M d h:i A',strtotime($post['datePosted'])) }} </small> --></span>
								@endif

							</a>
						</li>
						<?php
					}
				}
			?>
		</ul>
		<div class="full-message">
			<div class="message minimized">
				<div class="close">
					<i class="fa fa-times-circle"></i>
				</div>
				<div class="flag" data-messageID="">
					<i class="fa fa-comments fa-2x" title="Post a Comment"><span style="font-size: small; font-family: sans-serif;"> Comment</span></i>

				</div>

				<div class="like" data-messageID="">
					
					<i class="fa fa-thumbs-up fa-2x" title="Like"><span style="font-size: small; font-family: sans-serif;"> Like <span class="totalLikes"></span></span>  </i>

				</div>


				
				<img src="">
				<p></p>
				<div class="commentholder"></div>
				
			</div>
			<div class="image-overlay">
				<div class="close-flag close-overlay">
					<i class="fa fa-times-circle"></i>
				</div>
				<img src="">
			</div>
		</div>
		<div class="flagged">
			<label>
				<span>Post a Comment: </span>
				<textarea name="flagReason" placeholder="type in your comment here.."></textarea>
				<label style="color: #666"><input type="radio" name="anonymously" value='0' checked="checked" /> Let them know it's me </label>&nbsp;&nbsp;
				<label style="color: #666"><input type="radio" name="anonymously" value='1' /> Comment anonymously</label>
				<button type="button" id="submitReason">
					Submit <i class="fas fa-angle-right"></i>
				</button>
			</label>
			<div class="close-flag">
				<i class="fas fa-times-circle"></i>
			</div>
		</div>
		<div class="pagination hidden">
			<div class="buttons">
				<div class="next">
					<i class="fas fa-angle-left"></i>
				</div>
				<div class="prev">
					<i class="fas fa-angle-right"></i>
				</div>
				<div class="home"><i class="fa fa-home"></i>
				</div>

			</div>
		</div>
		<div class="system-message">
			<p></p>
		</div>
	</div>
	<script type="text/javascript" src="{{url('/')}}/public/js/jquery-3.3.1.min.js"></script>
  <script src="//{{ env('APP_DOMAIN') }}:9972/socket.io/socket.io.js"></script>
	<!-- <script type="text/javascript" src="../../public/js/wall_script.js"></script> -->
	<script type="text/javascript">

		$(document).keyup(function(e) {
      if (e.keyCode === 27 && $(".full-message").hasClass("show")) {
        if ($('.flagged').hasClass('show')) {
          $('.flagged .close-flag').click();   // esc
        }

        else if ($('.image-overlay').hasClass('show')) {
          $('.image-overlay .close-overlay').click();   // esc
        }

        else {
          $('.close').click();   // esc
        }
      }
    }) // END $(document).keyup

    $(document).keydown(function(e){
      if (e.which == 37) {
        $(".next").click();
        return false;
      }

      else if (e.which == 39) {
        $(".prev").click();
        return false;
      }
    }) // END $(document).keydown

    $(".home").click(function(){
      window.location.href = "{{action('EngagementController@show',$id)}}";
    });

    $(".next").click(function() {
      fp = $('.freedom-wall ul li').first().data("post");
      lp = $('.freedom-wall ul li').last().data("post");
      postCount = $('.freedom-wall ul').data("postcount");

      fpost = $('.freedom-wall ul').data("fpost");
      lpost = $('.freedom-wall ul').data("lpost");

      _token = "{{ csrf_token() }}";

      postCount = $('.freedom-wall ul').data("maxpost");
      maxpost = $('.freedom-wall ul').data("maxpost");
      arrlen =  $('.freedom-wall ul').data("arrlen");

      if(lp >= arrlen-1)
      {
        $('.system-message p').html("You've already reached the \nmost recent posts.");
        $('.system-message').addClass("show");
        setTimeout(function() {
             $( ".system-message" ).removeClass("show");
         }, 10000);

        // pointstart = (arrlen - maxpost)-1;
        // pointend = (pointstart - maxpost)+1;
        // if (pointend <= 0) pointend=0;

      }
      else
      {
        pointstart = (lp + maxpost);
        pointend = (pointstart - maxpost);
        if (pointstart >= arrlen-1) pointstart=arrlen-1;
        window.location.href = "../../employeeEngagement/{{$id}}/wall?l="+pointend+"&f="+pointstart+"&c="+maxpost;
      }

    }) //END $(".next").clicked

    $(".prev").click(function() {
      _token = "{{ csrf_token() }}";

      fp = $('.freedom-wall ul li').first().data("post");
      lp = $('.freedom-wall ul li').last().data("post");
      postCount1 = $('.freedom-wall ul li').length

      $('.freedom-wall ul').data("maxpost", postCount1);

      postCount = $('.freedom-wall ul').data("maxpost");
      maxpost = $('.freedom-wall ul').data("maxpost");
      arrlen =  $('.freedom-wall ul').data("arrlen");

      if(lp == arrlen)
      {
        pointstart = (arrlen - maxpost)-1;
        pointend = (pointstart - maxpost)+1;
        if (pointend <= 0) pointend=0;

      }
      else
      {
        pointstart = (lp - maxpost)-1;
        pointend = (pointstart - maxpost)+1;
        if (pointend <= 0) pointend=0;
      }

      fpost = $('.freedom-wall ul').data("fpost");
      lpost = $('.freedom-wall ul').data("lpost");

      if (pointstart <= '0')
      {
        $('.system-message p').text("You've already reached \nthe last page");
        $('.system-message').addClass("show");
        setTimeout(function() {
             $( ".system-message" ).removeClass("show");
         }, 10000);
      }
      else window.location.href = "../../employeeEngagement/{{$id}}/wall?l="+pointend+"&f="+pointstart+"&c="+maxpost;

    }) //END $(".prev").clicked

    postID = 0
    window.postID = 0;
    window.activeEntry = 0;
    window.commentID = 0;

    $(".post-it").click(function() {
      $(".full-message").toggleClass("show");
      $(".message").toggleClass("minimized");
      $(this).addClass("nohover");
      $(".pagination").addClass("hidden");

      message = $(this).find("p").html();
      comments = $(this).find("div.comments");
      likes = $(this).attr('data-totalLikes');
      alreadyLiked = $(this).attr('data-already');

      $(".message p").html(message);
      $(".message div.commentholder").html(comments[0]['innerHTML']);
      $(".message span.totalLikes").html("("+likes+")");
      $(".message span.totalLikes").attr('data-countLike',likes);
      $(".message span.totalLikes").attr('data-entryID',likes);

      if ($(this).find("a .img").css('background-image') != 'none' )
      {
        var bg_img = $(this).find("a .img").css('background-image').replace(/^url\(['"](.+)['"]\)/, '$1');
        if (bg_img.includes("openaccess.svg"))
        {
          $('.full-message .message').addClass("hidden")
          $('.full-message .message img').attr("src", '')
          $('.full-message .message img').css("display", 'none')
        }
        else
        {
          $('.full-message .message').removeClass("hidden")
          $('.full-message .message img').attr("src", bg_img)
          $('.full-message .message img').css("display", 'inline-block')
        }
      }

      if ($(this).find("a .img").css('background-image') == 'none' )
      {
        $('.full-message .message img').attr("src", '')
        $('.full-message .message img').css("display", 'none')
      }
      else if(!$(this).find("a .img").css('background-image'))
      {
        $('.full-message .message img').attr("src", '')
        $('.full-message .message img').css("display", 'none')
      }

      postID = $(this).data("post");
      entryID = $(this).attr("data-entryID");
      window.postID = $(this).data("post");
      window.activeEntry = $(this).attr("data-entryID");

      $(".message .flag").attr("data-messageID", postID);
      $("#submitReason").attr("data-entryID", entryID);
      $(".message .like").attr("data-messageID", entryID);
      $(".message .like").attr("data-already", alreadyLiked);

      if (alreadyLiked=='0')
        $(".message .like").removeClass('liked');
      else
        $(".message .like").addClass('liked');

    }) // END $('.post-it').clicked


    $(".close").click(function() {
      window.activeEntry = 0;
      $(".full-message").toggleClass("show");
      $(".message").toggleClass("minimized");
      $(".post-it").removeClass("nohover");
      $(".pagination").removeClass("hidden");
      $(".close-flag").click();
      pagination();
    }) // END $('.close').clicked

    $(".message .flag").click(function(){
      $('.flagged').addClass("show");
      $('.flagged label textarea').val('')
    })


    $(".message .like").click(function(){
      var _token = "{{ csrf_token() }}";

      item = $(this);
      window.item = item;

      likes = item.find('span.totalLikes').attr('data-countLike');
      likeholder = item.find('span.totalLikes');
      entryID = item.attr('data-messageID');
      likedAlready = item.attr('data-already');

      if (likedAlready=='1')
      {
        $.ajax({
          url:"{{action('EngagementController@unlike')}}",
          type:'POST',
          data:{
            'type':'post',
            'entryID': entryID,
            _token: _token
          },
          error: function(response)
          {
            alert('Error sending unlike. Please try again.');
            return false;
          },
          success: function(response)
          {
            $('#post-it'+entryID).attr('data-already',0);
            item.removeClass('liked');
            likeholder.html('');
            likeholder.html('('+(likes - 1)+')');
            plus = parseInt(likes) - 1;
            window.item.find('span.totalLikes').attr('data-countLike',plus);
            if(window.socket!=null){
              socket.emit("send", {
                event: 'unlike',
                entryId: entryID
              });
            }else{
              window.location.reload(true);
            }
          }
        });
      }
      else
      {
        $.ajax({
          url:"{{action('EngagementController@like')}}",
          type:'POST',
          data:{
            'type':'post',
            'entryID': entryID,
            _token: _token
          },
          error: function(response)
          {
            alert('Error sending like. Please try again.');
            return false;
          },
          success: function(response)
          {

            plus = parseInt(likes) + 1;
            $('#post-it'+entryID).attr('data-already',1);
            item.addClass('liked');
            likeholder.html('');
            likeholder.html('('+plus+')');
            window.item.find('span.totalLikes').attr('data-countLike',plus);
            if(window.socket!=null){
              socket.emit("send", {
                event: 'like',
                entryId: entryID
              });
            }else{
              window.location.reload(true);
            }
          }
        });
      }
    }) //END $(".message .like").clicked


    $(".close-flag").click(function(){
      $('.flagged').removeClass("show");
    }) //END $(".close-flag").clicked

    $('.commentholder').on('click','i.fa.fa-trash',function(){
      commentID = $(this).attr('data-commentid');
      yes = confirm("Are you sure you want to delete this comment?");
      var _token = "{{ csrf_token() }}";

      if(yes){
        $.ajax({
            url:"{{action('EngagementController@deleteEntryComment')}}",
            type:'POST',
            data:{
              'commentID': commentID,
              _token: _token
            },
            error: function(response)
            {
              alert('Error deleting comment. Please try again.');
              return false;
            },
            success: function(response)
            {
              if(window.socket!=null){
                socket.emit("send", {
                  event: 'delete',
                  entryId: entryID,
                  commentId: commentID
                });
                $('.comment'+commentID).remove();
              }else{
                window.location.reload(true);
              }
            }
        });
      }
    }) // END $('.commentholder').clicked

    $("#submitReason").click(function(){
      entryID = $(this).attr('data-entryID');
      reason = $('.flagged label textarea').val();
      body = $('.flagged label textarea').val();
      anonymously = $('input[name="anonymously"]:checked').val();
      var _token = "{{ csrf_token() }}";

      $.ajax({
        url:"{{action('EngagementController@postEntryComment')}}",
        type:'POST',
        data:{
          'type':'post',
          'entryID': entryID,
          'body':body,
          'anonymously':anonymously,
          _token: _token
        },
        dataType: 'json',
        error: function(response)
        {
          alert('Error saving comment. Please try again.');
          return false;
        },
        success: function(response)
        {
          console.log(response.id);
          $('.system-message p').text("Comment posted");
          $('.system-message').addClass("show");
          setTimeout(function() {
           $( ".system-message" ).removeClass("show");
          }, 3000);
          $(".close-flag").click();
          var name = (anonymously=='1') ? "Anonymous" : "{{ $profile->name }}";
          var campaign = '{{ $profile->campaign}}';


          if(window.socket!=null){
            socket.emit("send", {
              event: 'newcomment',
              entryId: entryID,
              commentId: response.id,
              body: body,
              name: name,
              campaign: campaign
            });
          }else{
            window.location.reload(true);
          }
        }
      });

    }) // END $('#submitReason').clicked

    h2 = $(window).height();
    h1 = $(".freedom-wall ul").height();
    $(".full-message .message img").click(function() {
      bg = $('.message img').attr("src")
      $(".full-message .image-overlay").addClass("show");
      $(".full-message .image-overlay img").attr("src", bg)
    })
    $(".full-message .image-overlay .close-overlay").click(function() {
      $(".full-message .image-overlay").removeClass("show");
    })

    function pagination() {
      lp = $('.freedom-wall ul li').last().data("post");
      postCount = $('.freedom-wall ul').data("postcount");
      maxpost = $('.freedom-wall ul').data("maxpost");
      postlength = $('.freedom-wall ul li').length;

      if (lp >= postCount && postCount == maxpost) {
        $('.pagination').addClass("hidden");

        if (postlength < maxpost) {
          $('.pagination').removeClass("hidden");
        }
      }

      else {
        $('.pagination').removeClass("hidden");

        if (maxpost=='' && postCount >= postlength) {
          $('.pagination').removeClass("hidden");
        }

        else {
          if (postCount >= maxpost) {
            $('.pagination').removeClass("hidden");
          }
        }
      }
    }

    pagination();

    $(document).ready(function() {
      window.setInterval(function(){
        postCount = $('.freedom-wall ul li').length;
        postCount1 = $('.freedom-wall ul').data('postcount');
        maxpost = $('.freedom-wall ul').data('maxpost');
        h1 = $(".freedom-wall ul").height();
        h2 = $(window).height();

        if (h1 > h2 || h1 > 590) {
          $('.freedom-wall ul li').first().remove();
          pagination();
        }

        else if(postCount1 >= postCount || maxpost !== '') {
          $('.pagination').removeClass("hidden");
          $('.preloader').addClass('end')
        }

        else if(postCount1 >= postCount && maxpost == '') {
          $('.pagination').addClass("hidden");
        }

      }, 1);
      $(window).resize(function(){location.reload();});
    })

    function generateTimestamp(){
      //[ Posted Tuesday, May 04 08:54 AM ]
      const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
      var today = new Date();
      var date = '(Just Now) '+'-'+monthNames[today.getMonth()]+' '+('0'+today.getDate()).slice(-2);
      var time = ('0'+today.getHours()).slice(-2) + ":" + ('0'+today.getMinutes()).slice(-2) ;
      return date+' '+time;
    }

    window.socket==null;
    if(io){
      window.socket = io('ws://{{ env('APP_DOMAIN') }}:9972');

      if(window.socket!=null){
        window.socket.emit('set_id', '{{ $user_id }}');

        window.socket.on("event", data => {
          console.log(data);

          if(data.data.event=="unlike"){
              var newvalue = parseInt($('#likecounter'+data.data.entryId).text()) - 1;
              if(newvalue!=NaN){
                $('#likecounter'+data.data.entryId).text(newvalue);
                $('#post-it'+data.data.entryId).attr('data-totalLikes',newvalue);
              }
            if(data.emmitter!='{{ $user_id }}' && window.activeEntry == data.data.entryId){
              var likes = parseInt(window.item.find('span.totalLikes').attr('data-countLike')) - 1;
              window.item.find('span.totalLikes').attr('data-countLike', likes);
            }
          }
          if(data.data.event=="like"){
            var newvalue = parseInt($('#likecounter'+data.data.entryId).text()) + 1;
            if(newvalue!=NaN){
              $('#likecounter'+data.data.entryId).text(newvalue);
              $('#post-it'+data.data.entryId).attr('data-totalLikes',newvalue);

            }
            if(data.emmitter!='{{ $user_id }}' && window.activeEntry == data.data.entryId){
              var likes = parseInt(window.item.find('span.totalLikes').attr('data-countLike')) + 1;
              window.item.find('span.totalLikes').attr('data-countLike', likes);
            }
          }

          if(data.data.event=="newcomment"){
            var entryId = data.data.entryId;
            var commentId = data.data.commentId;
            var body = data.data.body;
            var name = data.data.name;
            var campaign = data.data.campaign;

            var newvalue = parseInt($('#commentcounter'+data.data.entryId).text()) + 1;
            if(newvalue!=NaN){
              $('#commentcounter'+data.data.entryId).text(newvalue);
            }

            var appendelem = $('<p class="comment' + commentId  + '" style="margin-bottom: 10px; white-space: pre; background-color: #383a3c; padding:20px; width:75%" >' + body + ' -- <strong class="text-primary"><i class="fa fa-user"></i> <em>' + name + '</em>, ' + campaign + '</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small style="color: #666">[ Posted ' + generateTimestamp() + ' ]</small></p>');

            if(data.emmitter=='{{ $user_id }}'){
              var deletebutton = $('<i class="fa fa-trash" style="cursor: pointer; float: right ;padding:10px;" data-commentid="' + commentId + '"> Delete</i>');
              deletebutton.appendTo(appendelem);
            }

            appendelem.insertAfter($('.mCommentsHeader'+entryId));


            
          }

          if(data.data.event=="delete"){
            var entryId = data.data.entryId;
            var commentId = data.data.commentId;
            $('.comment'+commentId).remove();
            var newvalue = parseInt($('#commentcounter'+data.data.entryId).text()) - 1;
            if(newvalue!=NaN){
              $('#commentcounter'+data.data.entryId).text(newvalue);
            }
          }

        });

        window.socket.on("connect_error", (error) => {
          window.socket==null;
          console.log("could not connect to socket server.");
          console.log(error);
        });
      }else{
        window.socket==null;
        console.log("could not connect to socket server.");
      }
    }else{
      window.socket==null;
      console.log("socket server not initialized, cannot load socket.io.js")
    }


	</script>
</body>
</html>