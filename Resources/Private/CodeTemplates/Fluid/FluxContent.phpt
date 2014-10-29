{namespace flux=FluidTYPO3\Flux\ViewHelpers}
<f:layout name="###layout###" />

<f:section name="###configurationSectionName###">
	<flux:form id="###id###" label="###label###" options="{icon: '###icon###', group: 'FCE'}">
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
