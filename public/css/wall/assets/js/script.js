postID = 0
$(".post-it").click(function() {
	$(".full-message").toggleClass("show");
	$(".message").toggleClass("minimized");
	$(this).addClass("nohover");
	$(".pagination").addClass("hidden");

	message = $(this).find("p").html();
	console.log(message);
	comments = $(this).find("div.comments").html();
	$(".message p").html(message);
	$(".message div.comments").html(comments);

	if ($(this).find("a .img").css('background-image') != 'none' ) {
		var bg_img = $(this).find("a .img").css('background-image').replace(/^url\(['"](.+)['"]\)/, '$1');
		if (bg_img.includes("openaccess.svg")) {
			$('.full-message .message').addClass("hidden")
			$('.full-message .message img').attr("src", '')
			$('.full-message .message img').css("display", 'none')
		}

		else {
			$('.full-message .message').removeClass("hidden")
			$('.full-message .message img').attr("src", bg_img)
			$('.full-message .message img').css("display", 'inline-block')
		}
	}

	if ($(this).find("a .img").css('background-image') == 'none' ) {
		$('.full-message .message img').attr("src", '')
		$('.full-message .message img').css("display", 'none')
	}

	else if(!$(this).find("a .img").css('background-image')) {
		$('.full-message .message img').attr("src", '')
		$('.full-message .message img').css("display", 'none')
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

$(".next").click(function() {
	fp = $('.freedom-wall ul li').first().data("post");
	lp = $('.freedom-wall ul li').last().data("post");
	postCount = $('.freedom-wall ul').data("postcount");

	fpost = $('.freedom-wall ul').data("fpost");
	lpost = $('.freedom-wall ul').data("lpost");

	if (lp < lpost) {
		$.post('next.php', {
			firstPost: fp,
			lastPost: lp
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
	postCount1 = $('.freedom-wall ul li').length

	$('.freedom-wall ul').data("maxpost", postCount1);

	postCount = $('.freedom-wall ul').data("maxpost");

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
			$.post('prev.php', {
				firstPost: fp,
				lastPost: lp,
				postCount: postCount,
				fp: fpost,
				lp: lpost
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
       $(".next").click();
       return false;
    }

    else if (e.which == 39) {
       $(".prev").click();
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
			// $('.freedom-wall ul li').last().remove();
			$('.freedom-wall ul li').first().remove();
			pagination();
		}

		else if(postCount1 > postCount || maxpost !== '') {
			$('.pagination').removeClass("hidden");
			$('.preloader').addClass('end')
		}

		else if(postCount1 >= postCount && maxpost == '') {
			$('.pagination').addClass("hidden");
		}

	}, 1);
	$(window).resize(function(){location.reload();});
})




