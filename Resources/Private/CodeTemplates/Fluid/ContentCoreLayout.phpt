{namespace flux=FluidTYPO3\Flux\ViewHelpers}
{namespace v=FluidTYPO3\Vhs\ViewHelpers}
<f:layout name="ContentCore" />

<f:if condition="{settings.content.settings.container.addAnchor}">
	<a id="{record.header -> v:or(alternative: record.titleAttribute) -> v:or(alternative: 'content {record.uid}') -> v:format.url.sanitizeString()}"></a>
</f:if>
<v:tag name="{v:variable.get(name: 'settings.container.types.{v:format.regularExpression(pattern: \'/.*\:(.*)\..*/\', replacement: \'$1\', subject: record.tx_fed_fcefile)}') -> v:or(alternative: settings.container.types.default)}"
	   class="{settings.content.settings.container.className}">
	<v:tag name="h{settings.content.settings.header.type -> v:or(alternative: record.header_layout) -> v:or(alternative: settings.header.type)}"
		   class="{settings.content.settings.header.className}" hideIfEmpty="{settings.content.settings.header.hideIfEmpty -> v:or(alternative: 1)}">
		{record.header -> f:format.raw()}
	</v:tag>
	<f:render section="Main" />
</v:tag>

