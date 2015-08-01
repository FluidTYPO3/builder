plugin.###signature### {
	view {
		templateRootPaths.0 = {$plugin.###signature###.view.templateRootPaths.default}
		partialRootPaths.0 = {$plugin.###signature###.view.partialRootPaths.default}
		layoutRootPaths.0 = {$plugin.###signature###.view.layoutRootPaths.default}
	}
	#By default the following settings only will have relevance if you have fluidcontent_core extension loaded
	settings{
		container {
			types {
				default = div
				Example = div
			}
		}
	}
}
