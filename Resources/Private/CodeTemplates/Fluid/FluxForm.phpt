{namespace flux=Tx_Flux_ViewHelpers}
<f:layout name="###layout###" />
<div xmlns="http://www.w3.org/1999/xhtml"
     xmlns:flux="http://fedext.net/ns/flux/ViewHelpers"
     xmlns:v="http://fedext.net/ns/vhs/ViewHelpers"
     xmlns:f="http://typo3.org/ns/fluid/ViewHelpers">
<f:section name="###configurationSectionName###">
	<flux:flexform id="###id###" label="###label###" icon="{f:uri.resource(path: '###icon###')}">
		<!-- Insert fields, sheets, grid, form section objects etc. here, in this flux:flexform tag -->
	</flux:flexform>
</f:section>

<f:section name="###section###">
	Hello world!
</f:section>
</div>