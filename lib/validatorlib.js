var requiredValidations = Array();

function setRequiredValidationField(FieldName, jsRequiredArrayFieldID, value) {
	if (value == 0) {
		Ext.get(FieldName).addClass('feuserregisterrequiredfields');
	} 
	requiredValidations[jsRequiredArrayFieldID] = value;
}


function validateRequired(FieldName, jsRequiredArrayFieldID){
	
	if (Ext.get(FieldName).dom.getAttribute('type') == 'checkbox'){
		// console.log();
		if (Ext.get(FieldName).dom.checked != 'false') {
			requiredValidations[jsRequiredArrayFieldID] = 1;
			Ext.get(FieldName).removeClass('feuserregisterrequiredfields');
			checkValidations();		
		} else {
			setRequiredValidationField(FieldName, jsRequiredArrayFieldID, 0);
			checkValidations();	
		}
	} else {
		if (Ext.get(FieldName).getValue() != '') {
			requiredValidations[jsRequiredArrayFieldID] = 1;
			Ext.get(FieldName).removeClass('feuserregisterrequiredfields');
			checkValidations();		
		} else {
			setRequiredValidationField(FieldName, jsRequiredArrayFieldID, 0);
			checkValidations();	
		}
	}

}

function checkValidations(){
	var check = true;
	Ext.get('feuserregister_savebutton').set({disabled:true});
//	console.log(requiredValidations);
	if (requiredValidations.length != 0) {
		for (itt=0; itt < requiredValidations.length; itt = itt + 1) {
			validationValue = requiredValidations[itt]; 
			if (validationValue == 0) {
				check = false;
			}
		}
		if (check) {
			Ext.get('feuserregister_savebutton').dom.removeAttribute('disabled');
		}
	} else {
		Ext.get('feuserregister_savebutton').dom.removeAttribute('disabled');
	}
}

function validateEquals(ID_orig, ID_validate, jsRequiredArrayFieldID){
	ID_orig_node_value = Ext.get(ID_orig).getValue();
	ID_validate_node_value = Ext.get(ID_validate).getValue();
	
	ID_orig_node = Ext.get(ID_orig);
	ID_validate_node = Ext.get(ID_validate);
	
	setRequiredValidationField(ID_validate, jsRequiredArrayFieldID, 0);
	Ext.get(ID_orig).addClass('feuserregisterrequiredfields');
	Ext.get(ID_validate).addClass('feuserregisterrequiredfields');
	if (ID_validate_node_value == ID_orig_node_value) {
		Ext.get(ID_orig).removeClass('feuserregisterrequiredfields');
		Ext.get(ID_validate).removeClass('feuserregisterrequiredfields');
		setRequiredValidationField(ID_validate, jsRequiredArrayFieldID, 1);
	}
	checkValidations();
}

function validateEmail(field,alerttxt)
{
	with (field)
	{
		apos=value.indexOf("@");
		dotpos=value.lastIndexOf(".");
		if (apos<1||dotpos-apos<2) {
			alert(alerttxt);
			return false;
		}
		else {
			return true;
		}
	}
}