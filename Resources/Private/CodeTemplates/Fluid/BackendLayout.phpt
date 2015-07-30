<f:layout name="###name###" />
<f:be.container>
	<div class="typo3-fullDoc">
		<div id="typo3-docheader">
			<div class="typo3-docheader-functions">
				<div class="left">

				</div>
				<div class="right"></div>
			</div>
			<div class="typo3-docheader-buttons">
				<div class="left"></div>
				<div class="right">
					<div class="buttongroup">
						<f:link.page><span class="t3-icon fa fa-refresh"> </span></f:link.page>
						<f:be.buttons.shortcut />
					</div>
				</div>
			</div>
		</div>

		<div id="typo3-docbody">
			<div id="typo3-inner-docbody">
				<f:flashMessages class="tx-extbase-flash-message" />
				<f:render section="###section###" />
			</div>
		</div>
	</div>
</f:be.container>
