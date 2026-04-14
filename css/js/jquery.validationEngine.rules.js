
(function($) {
	$.fn.validationEngineLanguage = function() {};
	$.validationEngineLanguage = {
		newLang: function() {
			$.validationEngineLanguage.allRules = 	{
					"required":{    			// Add your regex rules here, you can take telephone as an example
						"regex":"none",
						"alertText":"* Acest spațiu este necesar.",
						"alertTextCheckboxMultiple":"* Treff eine Entscheidung",
						"alertTextCheckboxe":"* Nu ai acceptat termenii si conditiile"},
					"length":{
						"regex":"none",
						"alertText":"* Între ",
						"alertText2":" și ",
						"alertText3":" caractere permise."},
					"maxCheckbox":{
						"regex":"none",
						"alertText":"* Checks allowed Exceeded"},	
					"minCheckbox":{
						"regex":"none",
						"alertText":"* Bitte wähle ",
						"alertText2":" Optionen"},	
					"confirm":{
						"regex":"none",
						"alertText":"* Aceste spatii nu se potrivesc."},
					"telephone":{
						"regex":"/^[0-9\-\(\)\ ]+$/",
						"alertText":"* Unzulässige Telefonnummer"},	
					"email":{
						"regex":"/^[a-zA-Z0-9_\\.\\-]+\\@([a-zA-Z0-9\\-]+\\.)+[a-zA-Z0-9]{2,4}$/",
						"alertText":"* Adresa ta de email pare a fi eronată!"},
					"date":{
                         "regex":"/^[0-9]{4}\-\[0-9]{1,2}\-\[0-9]{1,2}$/",
                         "alertText":"* Invalid date, must be in YYYY-MM-DD format"},
					"onlyNumber":{
						"regex":"/^[0-9\ ]+$/",
						"alertText":"* Bitte nur Nummern"},	
					"noSpecialCharacters":{
						"regex":"/^[0-9a-zA-Z]*$/",
						"alertText":"* Caracterele speciale nu sunt permise"},
					"onlyValidPasswordCharacters":{
						"regex":"/^[\x20-\x7E]*$/",
						"alertText":"* Caracterele speciale nu sunt permise"},
					"ajaxUser":{
						"file":"../validateUser.php",
						"alertTextOk":"* Dieser Benutzername ist verfügbar",	
						"alertTextLoad":"* Bitte warten es wird geladen.",
						"alertText":"* Dieser Benutzername ist nicht mehr verfügbar"},	
					"ajaxName":{
						"file":"../validateUser.php",
						"alertText":"* Dieser Benutzername ist nicht mehr verfügbar",
						"alertTextOk":"* Dieser Benutzername ist verfügbar",	
						"alertTextLoad":"* Bitte warten es wird geladen."},		
					"onlyLetter":{
						"regex":"/^[a-zA-Z\ \']+$/",
						"alertText":"* Nur Zeichen verwenden."}
					}	
		}
	}
})(jQuery);

$(document).ready(function() {	
	$.validationEngineLanguage.newLang()
});