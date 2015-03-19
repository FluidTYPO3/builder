<div xmlns="http://www.w3.org/1999/xhtml" lang="en"
	xmlns:f="http://typo3.org/ns/TYPO3/Fluid/ViewHelpers"
	xmlns:flux="http://typo3.org/ns/FluidTYPO3/Flux/ViewHelpers">

	<f:layout name="###layout###" />

	<f:section name="###configurationSectionName###">
		<flux:form id="###formId###" label="###label###">
			<!-- Insert fields, sheets, grid, form section objects etc. here, in this flux:form tag -->
		</flux:form>
	</f:section>

	<f:section name="###section###">
		<h1>I am a standard template file!</h1>
		<p>
			My template file is EXT:###extension###/Resources/Private/###placement###.
		</p>
	</f:section>
</div>
