			var width;
			var height; 		


			width = window.outerWidth;
			height = window.outerHeight + 100;



			//$('.slide').css('height', height);

			$('.slider img').eq(0).css('opacity', 1);
			$('#triggerWindow').css({ 
				'height': height-71,
			});



			var sliderIndex = 0;
			
			var slider = function() {

				// change the logo bg color when the slide changes
				getAverageRGB(document.getElementById("logoBg"+sliderIndex));

				$('#slider').css("height", (window.innerHeight + $('footer').outerHeight()));
				$('.slide').css("z-index", 0);
				//$('#slider .info').css("top", (window.innerHeight - $('#slider .info').outerHeight() - 20))
				//$('footer').css("top", window.innerHeight);
				$('.slide').eq(sliderIndex)
				.css("z-index", 1)
				.animate({
					opacity: '1'
				})
				.delay(5000).animate({
					opacity:'0'
				});

				if(sliderIndex==$('.slide').length-1){
					sliderIndex=0;
				}
				else {
					sliderIndex++;
				}

				setTimeout( slider, 5000);
			
			}

			if ((width/height) > 1.77777777778) {
				$('.slider img').css({
					'width': '100%',
					'height': 'auto'
				});
			} else {
			
				$('.slider img').css({
					'width': 'auto',
					'height': '100%'
				});
				if(width < 780){
					$('.slider img').css({
						'left': (1-$(this).outerWidth())/2
					});
				}

			}

			$(window).resize(function() {

				width = window.outerWidth;
				height = window.outerHeight + 100;
				//$('.slide').css('height', height);

				if ((width/height) > 1.77777777778) {
					$('.slider img').css({
						'width': '100%',
						'height': 'auto',
						'left': 0
					});
				} else {
					$('.slider img').css({
						'width': 'auto',
						'height': '100%',
						'left': 0
					});
					if(width < 780){
						$('.slider img').css({
							'left': width/2 * -1 
						});
					}
				}

			});
