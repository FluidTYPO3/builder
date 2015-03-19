$(document).ready(function() {
	var namespaces = {
		"v": "vhs",
		"f": "fluid",
		"flux": "flux"
	};
	var timer;
	var automatic = $('#automatic');
	var renderer = $('#renderer').attr('action');
	var clear = $('#clear');
	var render = $('#render');
	var fluid = $('#fluid');
	var variables = $('#variables');
	var preview = $('#preview');
	var source = $('#source');
	var summary = $('#summary');
	var timing = $('#timing');
	var output = $('#output');
	var viewhelpers = $('#viewhelpers');
	var lastValue = fluid.val();
	var lastVariables = variables.val();
	if ($('#preset-list').length) {
		var presetList = $.parseJSON($('#preset-list').html());
		var presets = $('#presets');
		presets.change(function() {
			var index = $(this).val();
			var preset = presetList[index];
			fluid.val(preset.fluid);
			variables.val(preset.variables);
			renderFluid();
		});
		presets.tooltip({content: 'Preset examples of how to use Fluid - select one to load it.'});
	};
	var variablesError = function() {
		variables.addClass('error');
		render.removeAttr('disabled');
		return null;
	};
	var templateError = function(error) {
		output.removeClass('panel-success').addClass('panel-danger');
		preview.html(error.message);
	};
	var renderFluid = function() {
		lastValue = fluid.val();
		lastVariables = variables.val();
		render.attr('disabled', 'disabled');
		try {
			var data = $.parseJSON(variables.val());
		} catch (error) {
			return variablesError();
		};
		variables.removeClass('error');
		$.post(renderer, {
			"tx_builder_doodle": {
				"fluid": fluid.val(),
				"variables": data
			}
		}, function(response) {
			if (response.code) {
				return templateError(response);
			};
			preview.html(response.preview);
			source.html(response.source);
			prettyPrint();
			render.removeAttr('disabled');
			summary.html(
				'<dl class="dl-horizontal">' +
				'<dt>Nodes</dt><dd>' + response.analysis.NodesTotal.toString() + '</dd>' +
				'<dt>Split points</dt><dd>' + response.analysis.SplitsTotal.toString() + '</dd>' +
				'<dt>Max. nested nodes</dt><dd>' + response.analysis.MaxNestingLevel.toString() + '</dd>' +
				'<dt>ViewHelper nodes</dt><dd>' + response.analysis.ViewHelperNodes.toString() + '</dd>' +
				'</dl>'
			);
			timing.html(
				'<dl class="dl-horizontal">' +
				'<dt>Parse time</dt><dd>' + response.timing.parse.toString() + ' ms</dd>' +
				'<dt>Render time</dt><dd>' + response.timing.render.toString() + ' ms</dd>' +
				'<dt>Memory, parse</dt><dd>' + response.memory.parse.toString() + ' KB</dd>' +
				'<dt>Memory, render</dt><dd>' + response.memory.render.toString() + ' KB</dd>' +
				'</dl>'
			);
			if (0 == response.viewhelpers.length) {
				viewhelpers.html('No ViewHelpers used in template');
			} else {
				var html = '<ol>';
				for (var i = 0; i < response.viewhelpers.length; i++) {
					var parts = response.viewhelpers[i].split(':');
					var extension = namespaces[parts[0]];
					var segments = parts[1].split('.');
					// hardcoded URL with the expectation that semantic URLs are being used and the
					// EXT:schemaker schema rendering plugin is used on /viewhelpers.html with URL
					// rewriting rules to match following segments as extension key, version and
					// path to ViewHelper class file but with .html extension. We always reference
					// the "master" version - at least until, if it happens, additional rendering
					// slaves with different versions become available.
					var name = '';
					for (var u = 0; u < segments.length; u++) {
						var segmentName = segments[u].substring(0, 1).toUpperCase() + segments[u].substring(1);
						name += ('/' + segmentName);
					};
					name += 'ViewHelper';
					var url = '/viewhelpers/' + extension + '/master' + name + '.html';
					html += ('<li><a href="' + url + '">' + name.substring(1) + '</a> from <a href="/viewhelpers/'
						+ extension + '/master.html">' + extension + '</a></li>');
				};
				html += '</ol>';
				viewhelpers.html(html);
			};
			output.removeClass('panel-danger').removeClass('panel-default').addClass('panel-success');
		});
	};
	var autoRender = function() {
		if (!automatic.is(':checked') || (lastValue == fluid.val() && lastVariables == variables.val())) {
			return;
		};
		if (timer) {
			clearTimeout(timer);
		};
		timer = setTimeout(function() {
			renderFluid();
		}, 1000);
	};
	clear.click(function() {
		fluid.val('');
		variables.val('{}');
		preview.html('');
		source.html('');
		renderFluid();
	});
	fluid.keyup(autoRender);
	variables.keyup(autoRender);
	render.click(renderFluid);
	renderFluid();
});

