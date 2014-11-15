<div xmlns="http://www.w3.org/1999/xhtml" lang="en"
	xmlns:f="http://typo3.org/ns/fluid/ViewHelpers"
	f:schemaLocation="https://fluidtypo3.org/schemas/fluid-master.xsd"
	xmlns:flux="http://typo3.org/ns/FluidTYPO3/Flux/ViewHelpers"
	flux:schemaLocation="https://fluidtypo3.org/schemas/flux-master.xsd">

	<f:layout name="###layout###" />

	<f:section name="###configurationSectionName###">
		<flux:form id="###id###" label="###label###" options="{icon: '###icon###'}">
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
