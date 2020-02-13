<!DOCTYPE html>
<html>
<head>
	<!-- <link rel="icon" href="assets/img/icon.png"> -->
	<title>Valentine Wall | Open Access BPO</title>
	<link href="https://fonts.googleapis.com/css?family=Gochi+Hand|Montserrat&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="http://172.17.0.2/project/freedomwall/wall/assets/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="http://172.17.0.2/project/freedomwall/wall/assets/css/style.css">
	
</head>
<body translate="no" style="">
	<div class="freedom-wall">
		<ul data-postcount="{{count($posts)}}">
			

					<?php $postCount=5000; $i=0; ?>

					@foreach($posts as $post)

					@if($post['disqualified'])
					<!--do nothing-->
					@else

					<?php
						($post['img'] !== "" && !empty($post['img'])) ?  $bg = 'background-image: url('.$post['img'].');' : $bg = "";
						$length = 150;
						$end = "...";
						$string = strip_tags($post['message']);

					    if (strlen($string) > $length) {
					        $stringCut = substr($string, 0, $length);
					        $string = substr($stringCut, 0, strrpos($stringCut, ' ')).$end;
					    }
					?>
					<li class="post-it" data-post="{{$post['id']}}">
						<a href="#" style="{{$bg}}">
							<p style="display: none;">
								{{$post['message']}}
								<br/><strong style="color: #333">From: {{$post['from']}} </strong>
							</p>
							<p>
								<?php
									echo $string;
								?>
								<br/><strong style="font-size: x-small;color: #3ea6ff">From: {{$post['from']}} </strong>
							</p>
						</a>
					</li>
					@endif

					@endforeach


					
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
	<script type="text/javascript" src="http://172.17.0.2/project/freedomwall/wall/assets/js/script.js"></script>
</body>
</html>