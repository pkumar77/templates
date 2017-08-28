function trim(sString) {
	while (sString.substring(0,1) == ' ') sString = sString.substring(1, sString.length);
	while (sString.substring(sString.length-1, sString.length) == ' ') sString = sString.substring(0,sString.length-1);
	return sString;
}


function isTrimEmpty(elem, helperMsg) {
	var fieldval = trim(elem.value);
	if (fieldval.length == 0) {
		if (helperMsg) alert(helperMsg);
		elem.focus();
		return true;
	}
	return false;
}

function isVersion(elem, helperMsg){
                          
	var versionLegal = /^((\d+(\.\d*)+)|((\d*\.)+\d+))$/
	if(elem.value.match(versionLegal)) {
		return true;
	} else {
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

function isEmpty(elem, helperMsg) {
	if (elem.value.length == 0) {
		if (helperMsg) alert(helperMsg);
		elem.focus();
		return true;
	}
	return false;
}

function isNumeric(elem, helperMsg){
	var numericExpression = /^[0-9]+$/;
	if(elem.value.match(numericExpression)) {
		return true;
	} else {
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

function isAlphabet(elem, helperMsg){
	var alphaExp = /^[a-zA-Z]+$/;
	if (elem.value.match(alphaExp)) {
		return true;
	} else {
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

function isAlphanumeric(elem, helperMsg) {
	var alphaExp = /^[0-9a-zA-Z]+$/;
	if(elem.value.match(alphaExp)) {
		return true;
	} else {
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

function lengthRestriction(elem, min, max) {
	var uInput = elem.value;
	if(uInput.length >= min && uInput.length <= max) {
		return true;
	} else {
		alert("Please enter between " +min+ " and " +max+ " characters");
		elem.focus();
		return false;
	}
}

function madeSelection(elem, helperMsg) {
	if (elem.value == "Please Choose") {
		alert(helperMsg);
		elem.focus();
		return false;
	}
	return true;
}

function emailValidator(elem, helperMsg) {
	var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
	if (elem.value.match(emailExp)) {
		return true;
	} else {
		alert(helperMsg);
		elem.focus();
		return false;
	}
}


function isZip(val) {
	var zipstring = /(^\d{5}$)|(^\d{5}-\d{4}$)/;
	if (zipstring.test(val))
		{
		return true;
		}
	else
		{
		alert("Please enter a valid zip code");
		return false;
		}
}
