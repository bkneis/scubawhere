var Tour = {

	getAcommodationsTour : function() {
		if(window.tourStart) {
			$("#tour-next-step").show();
			$("#tour-finish").hide();
			introJs().setOptions( {
				showStepNumbers : false,
				exitOnOverlayClick : false,
				exitOnEsc : false
			}).start().onchange(function(targetElement) {
				switch (targetElement.id) {
					case "accommodation-form-container":
						$("#room-name").val("3* Hotel Single Room");
			            $("#acom-price").val(50);
			            break;
			        case "acom-base":
			            $("#add-base-price").click();
			            $("#acom-price").val(50);
			            break;
			        case "acom-season":
			            $("#acom-season-price").click();
			            $("#acom-price").val(50);
			            break;
			        case "acom-rooms":
			            $("#room-amount").val(6);
			            break;
			        case "accommodations-list":
			            $("#no-accommodations").remove();
			            $("#accommodation-list").append('<li id="dummy-room"><strong>3* Hotel Single Room</strong> | 6 | 50</li>');
			            break;
			        }
			    }).oncomplete(function() {
			    	$("#dummy-room").remove();
			    	$("#accommodation-list").append('<p id="no-accommodations">No accommodations available.</p>');
			    	clearForm();
			    });

			    $("#tour-next-step").on("click", function() {
			    	window.location.href = "#agents";
			    	if(window.currentStep.position <= 1) {
			    		window.currentStep = {
			    			tab : "#agents",
			    			position : 2
			    		};
			    	}
					$('.nav-wizard li').filter('.active').next('li').addClass("active");
					$('.nav-wizard li').filter('.active').first().removeClass("active");
			    });
			}
		},

		getAgentsTour : function() {
			if(window.tourStart) {
				if(window.currentStep.position < 2) {
					window.location.href = window.currentStep.tab;
				} else {
					$("#tour-next-step").show();
					$("#tour-finish").hide();
					introJs().setOptions( {
						showStepNumbers : false,
						exitOnOverlayClick : false,
						exitOnEsc : false
					}).start().onchange(function(targetElement) {
						switch (targetElement.id) {  
							case "agent-form-container": 
							$("#agent-name").val("John doe");

							$("#agent-web").val("http://www.onlinescubaholidays.com");
							$("#branch-name").val("Scuba holidays R us");
							$("#branch-address").val("46 grand avenue tenerife");
							$("#branch-phone").val("+44 7866565047");
							$("#branch-email").val("john.doe@scubaholidays.com");
							break;

							case "commission-div":
							$("#commission-amount").val(20);
							break;

							case "agent-list-div":
							$("#no-agents").remove();
							$("#agent-list").append('<li id="dummy-agent"><strong>John doe</strong> | Scuba holidays r us</li>');
							break;
						}
					}).oncomplete(function() {
						$("#dummy-agent").remove();
						$("#agent-list").append('<p id="no-agents">No agents available.</p>');
						clearForm();
					});
				}
				$("#tour-next-step").on("click", function() {
					window.location.href = "#locations";
					if(window.currentStep.position <= 2) {
						window.currentStep = {
							tab : "#locations",
							position : 3
						};
					}
					$('.nav-wizard li').filter('.active').next('li').addClass("active");
					$('.nav-wizard li').filter('.active').first().removeClass("active");
				});
			}
		},

		getLocationsTour : function() {
			if(window.tourStart) {
				if(window.currentStep.position < 3) {
					window.location.href = window.currentStep.tab;
				} else { 
					$("#tour-next-step").show();
					$("#tour-finish").hide();
					introJs().setOptions( {
						showStepNumbers : false,
						exitOnOverlayClick : false,
						exitOnEsc : false
					}).start();
				}
				$("#tour-next-step").on("click", function() {
					window.location.href = "#boats";
					if(window.currentStep.position <= 3) {
						window.currentStep = {
							tab : "#boats",
							position : 4
						};
					}
					$('.nav-wizard li').filter('.active').next('li').addClass("active");
					$('.nav-wizard li').filter('.active').first().removeClass("active");
				});
			}
		},

		getBoatsTour : function() {
			if(window.tourStart) {
				if(window.currentStep.position < 4) {
					window.location.href = window.currentStep.tab;
				} else {
					$("#tour-next-step").show();
					$("#tour-finish").hide();
					introJs().setOptions( {
						showStepNumbers : false,
						exitOnOverlayClick : false,
						exitOnEsc : false
					}).start().onchange(function(targetElement) {
						switch (targetElement.id) {  

							case "change-to-add-boatroom":
							$("#boatroom-list").append('<li id="dummy-boatroom"><strong>Single Cabin</strong></li>');
							break;

							case "boat-form-container":
							$("#boat-name").val("Barry's big boat");
			        	//CKEDITOR.setData("Add a description of your boat here."); 
			        	$("#boat-capacity").val(25);
			        	break;

			        	case "boat-cabins":
			        	$("#room-types").append('<p> \
			        		<select class="room-type-select"> \
			        		<option value="{{id}}">Single Cabin</option> \
			        		</select> Number of rooms: \
			        		<input type="number" value="6" placeholder="0" style="width: 100px;" min="0"> \
			        		<button class="btn btn-danger remove-room">&#215;</button> \
			        		</p>');
			        	break;

			        	case "boats-list-div":
			        	$("#no-boats").remove();
			        	$("#boat-list").append('<li id="dummy-boat"><strong>Barrys big boat</strong> | Capacity: 25</li>');
			        	break;
			        }
			    }).oncomplete(function() {
			    	$("#dummy-boat").remove();
			    	$("#boat-list").append('<p id="no-boats">No boats available.</p>');
			    	clearForm();
			    });
			}

			$("#tour-next-step").on("click", function() {
				if(window.boats.length != 0) {
					window.location.href = "#trips";
					if(window.currentStep.position <= 4) {
						window.currentStep = {
							tab : "#trips",
							position : 5
						};
					}
					$('.nav-wizard li').filter('.active').next('li').addClass("active");
					$('.nav-wizard li').filter('.active').first().removeClass("active");
				} else alert("You need to add atleast one boat");
			});

		}
	},

	getTripsTour : function() {
		if(window.tourStart) {
			if(window.currentStep.position < 5) {
				window.location.href = window.currentStep.tab;
			} else {
				$("#tour-next-step").show();
				$("#tour-finish").hide();
				introJs().setOptions( {
					showStepNumbers : false,
					exitOnOverlayClick : false,
					exitOnEsc : false
				}).start().onchange(function(targetElement) {
					switch (targetElement.id) {  
						case "trip-form-container":
						$("#trip-name").val("Single boat dive");
						$("#tripDuration").val(4);
						break;
						case "locationsList":
						$('#locationsList').find('.location').filter(':first').click();
						break;
						case "tagsList":
						$('#tagsList').find('.tag').filter(':first').click();
						break;
						case "trips-list-div":
						$("#no-trips").remove();
						$("#trip-list").append('<li id="dummy-trip"><strong>Single boat dive</strong> | 0d 4h </li>');
						break;
					}
				}).oncomplete(function() {
					$("#dummy-trip").remove();
					$("#trip-list").append('<p id="no-trips">No trips available.</p>');
					clearForm();
				});
			}
			$("#tour-next-step").on("click", function() {
				if(window.trips.length != 0) {
					window.location.href = "#tickets";
					if(window.currentStep.position <= 5) {
						window.currentStep = {
							tab : "#tickets",
							position : 6
						};
					}
				} else alert("You need to add atleast one ticket");
				$('.nav-wizard li').filter('.active').next('li').addClass("active");
				$('.nav-wizard li').filter('.active').first().removeClass("active");
			});
		}
	},

	getTicketsTour : function() {
		if(window.tourStart) {
			if(window.currentStep.position < 6) {
				window.location.href = window.currentStep.tab;
			} else {
				$("#tour-next-step").show();
				$("#tour-finish").hide();
				introJs().setOptions( {
					showStepNumbers : false,
					exitOnOverlayClick : false,
					exitOnEsc : false
				}).start().onchange(function(targetElement) {
					switch (targetElement.id) {  
						case "ticket-form-container":
						$("#ticket-name").val("2 dive boat trip");
						$("#ticket-base").val(50);
						break;
						case "tickets-seasonal":
						$("#seasonal-prices-checkbox").click();
						break;
						case "tickets-boats":
						$("#tickets-boats-checkbox").click();
						break;
						case "tickets-boatrooms":
						$("#tickets-boatroom-checkbox").click();
						break;
						case "tickets-list-div":
						$("#no-tickets").remove();
						$("#ticket-list").append('<li id="dummy-ticket"><strong>New diving trip</strong> | £50.00 </li>');
						break;
					}
				}).oncomplete(function() {
					$("#dummy-ticket").remove();
					$("#ticket-list").append('<p id="no-tickets">No tickets available.</p>');
					clearForm();
				});
			}

			$("#tour-next-step").on("click", function() {
				if(window.tickets.length != 0) {
					window.location.href = "#packages";
					if(window.currentStep.position <= 6) {
						window.currentStep = {
							tab : "#packages",
							position : 7
						};
					}
				} else pageMssg("Please add atleast one ticket");
				$('.nav-wizard li').filter('.active').next('li').addClass("active");
				$('.nav-wizard li').filter('.active').first().removeClass("active");
			});
		}
	},

	getPackagesTour : function() {
		if(window.tourStart) {
			if(window.currentStep.position < 7) {
				window.location.href = window.currentStep.tab;
				console.log(window.currentStep.tab);
			} else {
				$("#tour-next-step").show();
				$("#tour-finish").hide();
				introJs().setOptions( {
					showStepNumbers : false,
					exitOnOverlayClick : false,
					exitOnEsc : false
				}).start().onchange(function(targetElement) {
					switch (targetElement.id) {  
						case "package-form-container":
						$("#package-name").val("Family dive day");
						break;
						case "package-tickets":
						$("#package-tickets").find(".ticket-select").filter(":first").val(1);
						$("#package-tickets").find(".quantity-input").filter(":first").val(4);
						break;
						case "package-base":
						$("#package-base").find(".base-price").filter(":first").val(150);
						break;
						case "package-seasonal":
						$("#package-seasonal").find('input[type=checkbox]').filter(':first').click();
						break;
						case "package-size":
						$("#package-capacity").val(4);
						break;
						case "packages-list-div":
						$("#no-packages").remove();
						$("#package-list").append('<li id="dummy-package"><strong>Family dive day</strong> | £150.00 </li>');
						break;
					}
				}).oncomplete(function() {
					$("#dummy-package").remove();
					$("#package-list").append('<p id="no-packages">No packages available.</p>');
					clearForm();
				});
			}

			$("#tour-next-step").on("click", function() {
				window.location.href = "#add-ons";
				if(window.currentStep.position <= 7) {
					window.currentStep = {
						tab : "#add-ons",
						position : 8
					};
				}
				$('.nav-wizard li').filter('.active').next('li').addClass("active");
				$('.nav-wizard li').filter('.active').first().removeClass("active");
			});
		}
	},

	getAddonsTour : function() {
		if(window.tourStart) {
			$("#tour-next-step").hide();
			$("#tour-finish").show();
			if(window.currentStep.position < 8) {
				window.location.href = window.currentStep.tab;
			} else {
				$("#tour-button").empty();
				$("#tour-button").append('<button id="tour-finish" class="btn btn-success text-uppercase">Finish tour</button>');
				introJs().setOptions( {
					showStepNumbers : false,
					exitOnOverlayClick : false,
					exitOnEsc : false
				}).start().onchange(function(targetElement) {
					switch (targetElement.id) {  
						case "addon-form-container":
						$("#addon-name").val("Reef diving tax");
						$("#addon-price").val(10);
						break;
						case "addon-compulsory-div":
						$("#addon-compulsory").attr("checked", true);
						break;
						case "addon-list-div":
						$("#no-addons").remove();
						$("#addon-list").append('<li id="dummy-addon"><strong>Reef diving tax</strong> | £10.00 </li>');
						break;
					}
				}).oncomplete(function() {
					$("#dummy-addon").remove();
					$("#addon-list").append('<p id="no-addons">No addons available.</p>');
					clearForm();
				});
			}

			$("#tour-finish").click(function(event) {
				var params = { _token : window.token };
				Company.initialise(params, function success(data) {
					pageMssg("Thank you for following our wizard. Your system is now fully configured.", true);
					setTimeout(function () {
				       window.location.href = "#dashboard";
				       window.location.reload(true);
				    }, 3000);
				});
			});
		}
	}

};