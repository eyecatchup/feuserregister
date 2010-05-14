
	This project is hosted on forge.typo3.org
		http://forge.typo3.org/projects/show/extension-feuserregister

	Notice:
		The static TypoScript files delivered with this extension must be included in the template
		AFTER extensions that clear and modify the tt_content key (as e.g. CSS Styled Content does).

	Exception Codes:
		This extension uses specific exceptions that are used with an accordant code.
		That code value can be used to show different error messages if defined in the error template file.

		Code	Description

		1100	error while creating fe user
		1200	error while updae fe user
		1300	error while saving fe user
		1400	wrong hash code or user always confirmed
		3100	previous step not available
		3200	step [...] not available
		3300	second reload
		4100	Transformer [...] has no type defined
		5100	no support for TCA field type [...]
		5200	unknown field type: [...] for field [...]
		6100	field of type: [...] need the extension "static_info_tables"
