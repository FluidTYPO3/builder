<div xmlns="http://www.w3.org/1999/xhtml" lang="en"
	xmlns:f="http://typo3.org/ns/TYPO3/Fluid/ViewHelpers"
	xmlns:flux="http://typo3.org/ns/FluidTYPO3/Flux/ViewHelpers"
	###vhs###>

	<f:layout name="###layout###" />

	<f:section name="###configurationSectionName###">
		<flux:form id="###formId###" options="{group: 'FCE'}">
			<!-- Insert fields, sheets, grid, form section objects etc. here, in this flux:flexform tag -->
			<flux:field.input name="settings.helloWorld" required="true" />
		</flux:form>
	</f:section>

	<f:section name="Preview">
		<!-- If you wish, place custom backend preview content here -->
		<strong>Your Input:</strong><br />
		{settings.helloWorld}
	</f:section>

	<f:section name="###section###">
		<h3>I am a content element!</h3>
		<p>
			My template file is EXT:###extension###/Resources/Private/###placement###.
		</p>
		<h3>Frontend output</h3>
		<p>
			{settings.helloWorld}
		</p>
	</f:section>
</div>
