{namespace flux=FluidTYPO3\Flux\ViewHelpers}
{namespace v=FluidTYPO3\Vhs\ViewHelpers}
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
