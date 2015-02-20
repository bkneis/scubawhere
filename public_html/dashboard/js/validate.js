/*
$.fn.validateForm = function(){
	$($(this).children(".valid")).each(function(){
*/
		/* $(this).validate(); */
/*
		console.log(this);
	});
}
*/

/*
$.fn.validate = function(){
	var minLen, maxLen, needsNum;

	if($(this).attr("data-min")){
		mminLen = $(this).attr("data-min");
	}

	if($(this).attr("data-max")){
		maxLen = $(this).attr("data-max");
	}

	if($(this).attr("data-needs-num")){
		needsNum = true;
	}
	console.log(minLen + " " + maxLen + " " + needsNum);

}
*/

$.fn.validateField = function(min, max){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 010 found"'); // 2015-02-20
	var bool = true;
	var val = $(this).val();

	//reset each to no error
	$(this).validationAction(bool);

	//check if it has a value at all
	if(val){
		//it has a value, so check it..
		if((val.length >= min) && (val.length <= max)){
			//all good
		}else{
			//error
			bool = false;
		}
	}else{ bool = false; }

	//set to error if there is an error
	$(this).validationAction(bool);


	return bool;
};

$.fn.validateNumericField = function(min, max){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 011 found"'); // 2015-02-20
	var bool = true;
	var val = $(this).val();

	//reset each to no error
	$(this).validationAction(bool);

	//check if it has a value at all
	//and is a number
	if((val) && ($.isNumeric(val))){
		//check if min and max are set
		if(min && max){
			//it has a value, so check it..
			if((val >= min) && (val <= max)){
				//all good
			}else{
				//error
				bool = false;
			}
		}//no? thats it then.
	}else{ bool = false; }

	$(this).validationAction(bool);

	return bool;
};

//displays error mssg if bool == false
$.fn.validationAction = function(bool){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 012 found"'); // 2015-02-20
	if(bool === true){
		$(this).css("border-color", "");
	}else{
		$(this).css("border-color", "red");
	}

};
