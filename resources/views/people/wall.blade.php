<!DOCTYPE html>
<html>
<head>
	<!-- <link rel="icon" href="assets/img/icon.png"> -->
	<title>Valentine Wall | Open Access BPO</title>
	<link href="https://fonts.googleapis.com/css?family=Gochi+Hand|Montserrat&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="http://172.17.0.2/project/freedomwall/wall/assets/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="http://172.17.0.2/project/freedomwall/wall/assets/css/style.css">
	<?php
		session_start();
		
	?>
</head>

<body translate="no" style="">
	<?php /* <div class="freedom-wall">
		<ul data-postcount="{{count($posts)}}" data-maxpost="<?php 
		if(isset($_SESSION['postCount'])) {
			echo $_SESSION['postCount'];
		}

		else {
			echo '';
		}
		?>">
			<?php
				if (isset($_SESSION["firstPost"]) && isset($_SESSION["lastPost"]) && isset($_SESSION['postCount'])) {
					$fp=$_SESSION["firstPost"];
					$lp=$_SESSION["lastPost"];
					$postCount=$_SESSION["postCount"];
					$i=0;

					foreach ($posts as $post) {
						if ($post['id']>=$fp) {
							if ($i++ == $postCount) break;

							if ($post['img'] !== "") {
								$bg = 'background-image: url('.$post["img"].');';
							}

							else {
								$bg = "";
							}

							$length = 150;
							$end = "...";
							$string = strip_tags($post['message']);

						    if (strlen($string) > $length) {

						        // truncate string
						        $stringCut = substr($string, 0, $length);

						        // make sure it ends in a word so assassinate doesn't become ass...
						        $string = substr($stringCut, 0, strrpos($stringCut, ' ')).$end;
						    }
						    
							?>
							<li class="post-it" data-post="<?php echo $post['id']; ?>">
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
									<p style="display: none; white-space:pre-wrap;">
										<?php
											echo $post['message'];
										?>
										<br/><strong style="color:#333">From: {{$post['from']}}</strong>
									</p>
									<p>
										<?php
											echo $string;
										?><br/><strong style="color:#3ea6ff; ">From: {{$post['from']}}</strong>
									</p>
								</a>
							</li>
							<?php
						}
					}
					session_destroy();
				}

				else {
					$postCount=5000;

					$i=0;

					foreach ($posts as $post) {
						if ($i++ == $postCount) break;

						if ($post['img'] !== "" && !empty($post['img'])) {
							$bg = 'background-image: url('.$post["img"].');';
						}

						else {
							$bg = "";
						}

						$length = 100;
						$end = "...";

						$string = strip_tags($post['message']);

					    if (strlen($string) > $length) {
					        $stringCut = substr($string, 0, $length);
					        $string = substr($stringCut, 0, strrpos($stringCut, ' ')).$end;
					    }
					    
						?>
						<li class="post-it" data-post="<?php echo $post['id']; ?>">
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
								<p style="display: none; white-space:pre-wrap;"">
									<?php
										echo $post['message'];
									?>
									<br/><strong style="color:#333;">From: {{$post['from']}}</strong>
								</p>
								<p>
									<?php
										echo $string;
									?><br/><strong style="color:#3ea6ff; font-size: x-small;">From: {{$post['from']}}</strong>
								</p>
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
					<i class="fas fa-minus-circle"></i>
				</div>
				<div class="flag" data-messageID="">
					<i class="fas fa-flag"></i>
				</div>
				<img src="">
				<p></p>
			</div>
			<div class="image-overlay">
				<div class="close-flag close-overlay">
					<i class="fas fa-times-circle"></i>
				</div>
				<img src="">
			</div>
		</div>
		<div class="flagged">
			<label>
				<span>Reason: </span>
				<textarea id="flagReason" name="flagReason" placeholder="Indicate reason for flagging this post as inappropriate"></textarea>
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
				<div class="prev">
					<i class="fas fa-angle-left"></i>
				</div>
				<div class="next">
					<i class="fas fa-angle-right"></i>
				</div>
			</div>
		</div>
		<div class="system-message">
			<p></p>
		</div>
	</div> */ ?>




	<div class="freedom-wall">
		<ul data-postcount="{{count($posts) }}" data-maxpost="<?php 
		if(isset($_SESSION['postCount'])) {
			echo $_SESSION['postCount'];
		}

		else {
			echo '';
		}
		?>"

		data-fpost="{{$posts[0]['id']}}"
		data-lpost="<?php foreach ($posts as $post) { $lpost = $post["id"]; } echo $lpost; ?>"
		>
			<?php
				if (isset($_SESSION["firstPost"]) && isset($_SESSION["lastPost"]) && isset($_SESSION['postCount'])) {
					$fp=$_SESSION["firstPost"];
					$lp=$_SESSION["lastPost"];
					$postCount=$_SESSION["postCount"];
					$i=0;

					foreach ($posts as $post) {
						if ($post['id']>=$fp) {
							if ($i++ == $postCount) break;

							if ($post['img'] !== "") {
								$bg = 'background-image: url('.$post["img"].');';
							}

							else {
								$bg = "";
							}

							$length = 100;
							$end = "...";
							$string = strip_tags($post['message']);

						    if (strlen($string) > $length) {

						        // truncate string
						        $stringCut = substr($string, 0, $length);

						        // make sure it ends in a word so assassinate doesn't become ass...
						        $string = substr($stringCut, 0, strrpos($stringCut, ' ')).$end;
						    }
						    
							?>
							<li class="post-it" data-post="<?php echo $post['id']; ?>">
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
										<?php
											echo $post['message'];
										?>
									</p>
									<p>
										<?php
											echo $string;
										?>
									</p>
								</a>
							</li>
							<?php
						}
					}
					session_destroy();
				}

				else {
					$postCount=5000;

					$i=0;

					foreach ($posts as $post) {
						if ($i++ == $postCount) break;

						if ($post['img'] !== "" && !empty($post['img'])) {
							$bg = 'background-image: url('.$post["img"].');';
						}

						else {
							$bg = "";
						}

						$length = 100;
						$end = "...";

						$string = strip_tags($post['message']);

					    if (strlen($string) > $length) {
					        $stringCut = substr($string, 0, $length);
					        $string = substr($stringCut, 0, strrpos($stringCut, ' ')).$end;
					    }
					    
						?>
						<li class="post-it" data-post="<?php echo $post['id']; ?>">
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
									<?php
										echo $post['message'];
									?>
								</p>
								<p>
									<?php
										echo $string;
									?>
								</p>
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
					<i class="fas fa-minus-circle"></i>
				</div>
				<div class="flag" data-messageID="">
					<i class="fas fa-flag"></i>
				</div>
				<img src="">
				<p></p>
			</div>
			<div class="image-overlay">
				<div class="close-flag close-overlay">
					<i class="fas fa-times-circle"></i>
				</div>
				<img src="">
			</div>
		</div>
		<div class="flagged">
			<label>
				<span>Reason: </span>
				<textarea name="flagReason" placeholder="State Your Reason Here.."></textarea>
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
				<div class="prev">
					<i class="fas fa-angle-left"></i>
				</div>
				<div class="next">
					<i class="fas fa-angle-right"></i>
				</div>
			</div>
		</div>
		<div class="system-message">
			<p></p>
		</div>
	</div>
	<script type="text/javascript" src="http://172.17.0.2/project/freedomwall/wall/assets/js/jquery.min.js"></script>
	<!-- <script type="text/javascript" src="http://172.17.0.2/project/freedomwall/wall/assets/js/script.js"></script> -->

	<script type="text/javascript">
		postID = 0
		$(".post-it").click(function() {
			$(".full-message").toggleClass("show");
			$(".message").toggleClass("minimized");
			$(this).addClass("nohover");
			$(".pagination").addClass("hidden");

			message = $(this).find("p").html();
			$(".message p").html(message);

			if ($(this).find("a .img").css('background-image') != 'none' ) {
				var bg_img = $(this).find("a .img").css('background-image').replace(/^url\(['"](.+)['"]\)/, '$1');
				if (bg_img.includes("openaccess.svg")) {
					$('.full-message .message').addClass("hidden")
				}

				else {
					$('.full-message .message').removeClass("hidden")
					$('.full-message .message img').attr("src", bg_img)
				}
			}

			if ($(this).find("a .img").css('background-image') == 'none' ) {
				$('.full-message .message img').attr("src", '')
			}

			else if(!$(this).find("a .img").css('background-image')) {
				$('.full-message .message img').attr("src", '')
			}

			postID = $(this).data("post")
			$(".message .flag").attr("data-messageID", postID)
		})

		$(".close").click(function() {
			$(".full-message").toggleClass("show");
			$(".message").toggleClass("minimized");
			$(".post-it").removeClass("nohover");
			$(".pagination").removeClass("hidden");
			$(".close-flag").click();
			pagination();
		})

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
		});

		$(".message .flag").click(function(){
			$('.flagged').addClass("show");
			$('.flagged label textarea').val('')
		})

		$(".close-flag").click(function(){
			$('.flagged').removeClass("show");
		})

		$("#submitReason").click(function(){
			reason = $('.flagged label textarea').val();
			console.log(postID)
			console.log(reason)

			$('.system-message p').text("Message Sent");
			$('.system-message').addClass("show");
			setTimeout(function() {
		       $( ".system-message" ).removeClass("show");
		   }, 3000);
			$(".close-flag").click();

		})

		h2 = $(window).height();
		h1 = $(".freedom-wall ul").height();
		var _token = "{{ csrf_token() }}";

		$(".next").click(function() {
			fp = $('.freedom-wall ul li').first().data("post");
			lp = $('.freedom-wall ul li').last().data("post");
			postCount = $('.freedom-wall ul').data("postcount");

			fpost = $('.freedom-wall ul').data("fpost");
			lpost = $('.freedom-wall ul').data("lpost");

			if (lp < lpost) {
				$.post("{{action('EngagementController@next')}}", {
					firstPost: fp,
					lastPost: lp,
					_token: _token

				}, function(data) {
					location.reload(true);
				})
			}

			else {
				$('.system-message p').html("Sorry<br>This is the last page so far");
				$('.system-message').addClass("show");
				setTimeout(function() {
			       $( ".system-message" ).removeClass("show");
			   }, 10000);
			}
		})

		$(".prev").click(function() {
			fp = $('.freedom-wall ul li').first().data("post");
			lp = $('.freedom-wall ul li').last().data("post");
			postCount = $('.freedom-wall ul').data("maxpost");
			postCount1 = $('.freedom-wall ul li').length

			fpost = $('.freedom-wall ul').data("fpost");
			lpost = $('.freedom-wall ul').data("lpost");

			if (fp > fpost && lp > postCount1) {

			// if (lp > postCount && lp > postCount1) {
				// if (postCount!=null) {
				// 	$('.system-message p').text("This is the first page");
				// 	$('.system-message').addClass("show");
				// 	setTimeout(function() {
				//        $( ".system-message" ).removeClass("show");
				//    }, 10000);
				// }

				if (postCount== '') {
					$('.system-message p').text("This is the first page");
					$('.system-message').addClass("show");
					setTimeout(function() {
				       $( ".system-message" ).removeClass("show");
				   }, 10000);
				}

				else {
					$.post("{{action('EngagementController@prev')}}", {
						firstPost: fp,
						lastPost: lp,
						postCount: postCount,
						fp: fpost,
						lp: lpost,
						_token: _token
					}, function(data) {
						location.reload(true);
					})
				}

				
			}

			else {
				$('.system-message p').text("This is the first page");
				$('.system-message').addClass("show");
				setTimeout(function() {
			       $( ".system-message" ).removeClass("show");
			   }, 10000);
			}
		})



		$(".full-message .message img").click(function() {
			bg = $('.message img').attr("src")
			$(".full-message .image-overlay").addClass("show");

			$(".full-message .image-overlay img").attr("src", bg)
		})

		$(".full-message .image-overlay .close-overlay").click(function() {
			$(".full-message .image-overlay").removeClass("show");
		})


		$(document).keydown(function(e){
		    if (e.which == 37) { 
		       $(".prev").click();
		       return false;
		    }

		    else if (e.which == 39) { 
		       $(".next").click();
		       return false;
		    }
		});

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
					$('.freedom-wall ul li').last().remove();
					pagination();
				}

				else if(postCount1 > postCount || maxpost !== '') {
					$('.pagination').removeClass("hidden");
				}

				else if(postCount1 >= postCount && maxpost == '') {
					$('.pagination').addClass("hidden");
				}

			}, 1);
			$(window).resize(function(){location.reload();});
		})


	</script>

	<script type="text/javascript">

		 $(function () {
							   'use strict';


							    $('#submitReason').on('click',function()
							    {
							      var _token = "{{ csrf_token() }}";
							      var entry_id = $('.flag').attr('data-messageid');

							      


							       $.ajax({

							                  url:"{{action('EngagementController@reportEntry')}}",
							                  type:'POST',
							                  data:{

							                    'reason': $('#flagReason').val(),
							                    'entry_id': entry_id,
							                    _token: _token

							                  },
							                  error: function(response)
							                  { console.log("Error reporting note: ");
							                    console.log(response);
							                    alert('Error reporting note');
							                    return false;
							                  },
							                  success: function(response)
							                  {
							                    console.log(response);
							                    // $.notify("Entry updated. \nThank you for participating.",{className:"success", globalPosition:'right middle',autoHideDelay:7000, clickToHide:true} );
							                    // $('#saveTrigger').fadeOut();$('#editTrigger').fadeIn();
							                    //alert('Thank you. Our moderators will review your reported concerns.');
							                    window.location.reload(true);


							                  }

							            });
							      

							    });

     
      
   });

   
		


	</script>
</body>


</html>