<div xmlns="http://www.w3.org/1999/xhtml" lang="en"
	xmlns:f="http://typo3.org/ns/fluid/ViewHelpers"
	f:schemaLocation="https://fluidtypo3.org/schemas/fluid-master.xsd"
	xmlns:flux="http://typo3.org/ns/FluidTYPO3/Flux/ViewHelpers"
	flux:schemaLocation="https://fluidtypo3.org/schemas/flux-master.xsd"
	xmlns:v="http://typo3.org/ns/FluidTYPO3/Vhs/ViewHelpers"
	v:schemaLocation="https://fluidtypo3.org/schemas/vhs-master.xsd">
	<f:layout name="###layout###" />

	<f:section name="###configurationSectionName###">
		<flux:form id="###id###" options="{icon: '###icon###'}">
			<!-- Insert fields, sheets, grid, form section objects etc. here, in this flux:form tag -->
		</flux:form>
		<flux:grid>
			<!-- Edit this grid to change the "backend layout" structure -->
			<flux:grid.row>
				<flux:grid.column colPos="0" colspan="3" name="main" />
				<flux:grid.column colPos="1" name="right" />
			</flux:grid.row>
		</flux:grid>
	</f:section>

	<f:section name="###section###">
		<h1>I am a page template!</h1>
		<p>
			My template file is EXT:###extension###/Resources/Private/###placement###.
		</p>
		<div style="float: left; width: 75%;">
			<h2>Content main</h2>
			<v:content.render column="0" />
		</div>
		<div style="float: left; width: 25%;">
			<h2>Content right</h2>
			<v:content.render column="1" />
		</div>
	</f:section>
</div>
