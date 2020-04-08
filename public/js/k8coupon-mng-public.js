// console.log( k8All );
(function( $ ) {
	'use strict';
	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(document).ready(function() {
		(function() {
		// var deffInpTel = 0;
		var All = {
			// Trigger all actions
			triggerActions: function(){
				// INPUT DATA TO FORMS
				function checkInp( el ){
					var wrr = el.parents('.k8-inp__wrr');
					// console.log(el);
					if( el[0].checkValidity() ){
						el.removeClass('err');
						el.addClass('succ');
						wrr.removeClass('err');
						wrr.addClass('succ');
						$('[name="k8phn-valid"]').val(1);
					}
					else{
						el.addClass('err');
						el.removeClass('succ');
						wrr.addClass('err');
						wrr.removeClass('succ');
						$('[name="k8phn-valid"]').val(0);
					}
				}
				$('body').on('input blur', '.k8-inp', function(event) {
					event.preventDefault();
					checkInp( $(this) );
				});
				// Perform AJAX login/register on form submit
				$('form.k8-form__coupon').on('submit', function (e) {
					e.preventDefault();
					var errz = $(this).find('.k8-inp__wrr.err');
					if( errz.length )	return false;
					$('.k8-prld').css('display', 'block');
					// $('p.status', this).show().text(k8All.loadingmessage);
					var action = $(this).attr('action');
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: k8All.ajaxurl,
						data: {
							'action': action,
							'datta' : $(this).serializeArray()
						},
						success: function (data) {
							// console.;log(data);

							$('.k8-prld').css('display', 'none');
							if ( data.error ) {
								var $mod = $('#modd__err'),
								txt = '<p>';
								data.error.forEach(function (item, index) {
								  txt = txt + item + '</p><p>';
								});
								txt = txt + '</p>';
								$mod.find('.modd__txt').html( txt );
								$('body').addClass('ov-hidd');
								$mod.css('display', 'block');
							}
							else if (data.html_1) {
								var $succ = $('#modd__succ'),
										$txt = $succ.find('.modd__txt');
								$txt.html(data.html_1);
								$('#modd__succ').css('display', 'block');
							}
							else{
								$('#modd__succ').css('display', 'block');
								setTimeout(function(){
									window.location.replace("https://vavt.de/link/ppt");
								}, 1000);
							}

						}
					});
				});
				$('body').on('click', '.modd__clz', function(event){
					event.preventDefault();
					var curr = $(this),
					modd = curr.parents('.modd');
					curr.parents('.modd').css('display', 'none');
					$('body').removeClass('ov-hidd');
				});
			},
			/* Trigger Ajax Events */
			ajaxTrg: function() {
			},/* END Trigger Ajax Events */
			/*Start Preloader*/
			pldStart: function(){
			},
			/*End Preloader*/
			pldEnd: function(){
			},
			anim: function(objj){
			},
			validation: function(){
			},
			setts: function(){
				//Phone with Country Flags
  			$(window).on('load', function() {
	  			if( $('[data-k8phn]').length ){
			 			var phnn = $('[data-k8phn]').intlTelInput({
					  	nationalMode: false,
					  	initialCountry: "auto",
					  	initialCountry: "AT",
						  onlyCountries:["AT","CH","DE","RU"],
						  separateDialCode: true,
					  }),
			 			$parr = phnn.closest('.intl-tel-input'),
						$validd = $parr.siblings('[data-k8phn-valid]'),
						$counn = $parr.siblings('[data-k8phn-country]'),
						$wrr = phnn.closest('.k8-inp__wrr');
						function setInpCountry(){
							var plc = phnn.attr('placeholder'),
						  ccode = $('.selected-dial-code').text(),
						  repll = plc.replace(/[0-9]/g, 0);
						  $counn.val( ccode );
						  $validd.val('0');
						  phnn.val('');
						  $wrr.removeClass('err');
						  $wrr.removeClass('succ');
							// phnn.unmask();
					  // 	phnn.mask( repll, {
						 //  	onComplete: function(cep) {
							// 	 	$validd.val('1');
							// 	 	$wrr.removeClass('err');
							// 		$wrr.addClass('succ');
							// 	},
							// 	onChange: function(cep){
							// 	 $validd.val('0');
							// 	 $wrr.removeClass('succ');
							// 	 $wrr.addClass('err');
							// 	}
						 //  });
						}
						setInpCountry();
					 	phnn[0].addEventListener("open:countrydropdown", function() {
					 		phnn.removeAttr('maxLength');
						});
					 	phnn[0].addEventListener("countrychange", function() {
							setInpCountry();
						});
					}
		 		});
			},
			//Initionalisation
			init: function() {
				this.validation();
				this.ajaxTrg();
				this.setts();
				this.triggerActions();
			}
		}
		All.init();
	})();
	});
})( jQuery );
