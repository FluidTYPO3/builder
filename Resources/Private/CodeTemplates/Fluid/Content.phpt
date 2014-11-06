<div xmlns="http://www.w3.org/1999/xhtml" lang="en"
	xmlns:f="http://typo3.org/ns/fluid/ViewHelpers"
	f:schemaLocation="https://fluidtypo3.org/schemas/fluid-master.xsd"
	xmlns:flux="http://typo3.org/ns/FluidTYPO3/Flux/ViewHelpers"
	flux:schemaLocation="https://fluidtypo3.org/schemas/flux-master.xsd">
	<f:layout name="###layout###" />

	<f:section name="###configurationSectionName###">
		<flux:form id="###id###" options="{icon: '###icon###', group: 'FCE'}">
			<!-- Insert fields, sheets, grid, form section objects etc. here, in this flux:flexform tag -->
		</flux:form>
	</f:section>

	<f:section name="Preview">
		<!-- uncomment this to use a grid for nested content elements -->
		<!-- <flux:widget.grid /> -->
	</f:section>

	<f:section name="###section###">
		<h3>I am a content element!</h3>
		<p>
			My template file is EXT:###extension###/Resources/Private/###placement###.
		</p>
	</f:section>
</div>
