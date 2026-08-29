<?php
/**
 * Module Name: TrafficLens
 * Module ID: trafficlens
 * Description: Shows the last 30 days of visitor traffic from TrafficLens, plus this week's page views and the current live visitor count.
 * Version: 1.0
 * Default W: 12
 * Default H: 4
 */

if (!defined('IN_GS')) { die('You cannot load this page directly.'); }

$i18n_m = dash_module_i18n('trafficlens');

$uid = 'tld_' . substr(md5(__FILE__), 0, 6);

// Distinguish "plugin not installed/active" from "plugin active but running an
// older version that doesn't have this function yet" - the two look identical
// from the outside otherwise, and the second one is a much easier fix.
$core_ok    = function_exists('TrafficLens_db');
$summary_ok = function_exists('TrafficLens_daily_summary');
$plugin_ok  = $core_ok && $summary_ok;
$data       = null;

if ($plugin_ok) {
	try {
		$data = TrafficLens_daily_summary();
	} catch (\Throwable $e) {
		$data = null;
	}
}

$has_data = $plugin_ok && $data && array_sum($data['views']) > 0;

// $SITEURL is available here because dash_main() pulled it in via `global`
// before include()-ing this module, so it's shared into this file's scope.
$chart_js_url = $SITEURL . 'plugins/TrafficLens/inc/js/chart.umd.min.js';

// Date labels without the year, per spec: M/D (e.g. 8/5)
$chart_labels = $has_data ? array_map(function ($d) { return date('n/j', strtotime($d)); }, $data['labels']) : [];
?>

<style>
#<?php echo $uid ?> .tld-header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	margin-bottom: 10px;
}
#<?php echo $uid ?> .tld-header h3 {
	margin: 0;
	display: flex;
	align-items: center;
	gap: 6px;
	padding-bottom: 20px;
}
#<?php echo $uid ?> .tld-link {
	background: #4a90d9;
	color: #fff;
	font-size: 12px;
	padding: 4px 10px;
	border-radius: 4px;
	text-decoration: none;
	white-space: nowrap;
}
#<?php echo $uid ?> .tld-link:hover { text-decoration: underline; }
#<?php echo $uid ?> .tld-body {
	display: flex;
	gap: 18px;
	align-items: stretch;
}
#<?php echo $uid ?> .tld-side {
	display: flex;
	flex-direction: column;
	gap: 12px;
	flex-shrink: 0;
	width: 130px;
}
#<?php echo $uid ?> .tld-side-stat {
	border: 1px solid #ddd;
	border-radius: 5px;
	padding: 10px;
}
#<?php echo $uid ?> .tld-stat-week {
	background: #f4f6f9;
}
#<?php echo $uid ?> .tld-stat-now {
	background: #eefcf3;
}
#<?php echo $uid ?> .tld-stat-label {
	font-size: 11px;
	color: #888;
	text-transform: uppercase;
	letter-spacing: .02em;
	margin-bottom: 2px;
}
#<?php echo $uid ?> .tld-stat-value {
	font-size: 22px;
	font-weight: 700;
	color: #1a1a2e;
	line-height: 1.1;
}
#<?php echo $uid ?> .tld-stat-value .tld-live-dot {
	display: inline-block;
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: #4caf50;
	margin-right: 5px;
	vertical-align: middle;
}
#<?php echo $uid ?> .tld-stat-sub { font-size: 11px; color: #aaa; }
#<?php echo $uid ?> .tld-chart-wrap { flex: 1; min-width: 0; position: relative; height: 190px; }
#<?php echo $uid ?> .tld-empty {
	color: #bbb;
	font-style: italic;
	text-align: center;
	padding: 24px 0;
	flex: 1;
}
#<?php echo $uid ?> .tld-missing {
	color: #856404;
	background: #fff3cd;
	border: 1px solid #ffeeba;
	border-radius: 6px;
	padding: 10px 12px;
	font-size: 13px;
}
</style>

<div id="<?php echo $uid ?>">
	<div class="tld-header">
		<h3>
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="flex-shrink:0">
				<path d="M0 0h24v24H0z" fill="none"/>
				<path fill="currentColor" d="M3 13h4v8H3zm7-5h4v13h-4zm7-4h4v17h-4z"/>
			</svg>
			<?php echo $i18n_m('tld_lang_Title'); ?>
		</h3>
		<?php if ($plugin_ok): ?>
		<a class="tld-link" href="load.php?id=TrafficLens"><?php echo $i18n_m('tld_lang_View_Full'); ?> &rarr;</a>
		<?php endif; ?>
	</div>

	<?php if (!$core_ok): ?>
		<p class="tld-missing">&#9888; <?php echo $i18n_m('tld_lang_Plugin_Not_Active'); ?></p>
	<?php elseif (!$summary_ok): ?>
		<p class="tld-missing">&#9888; <?php echo $i18n_m('tld_lang_Plugin_Outdated'); ?></p>
	<?php else: ?>
	<div class="tld-body">
		<div class="tld-side">
			<div class="tld-side-stat tld-stat-week">
				<div class="tld-stat-label"><?php echo $i18n_m('tld_lang_This_Week'); ?></div>
				<div class="tld-stat-value"><?php echo (int)($data['week_views'] ?? 0); ?></div>
				<div class="tld-stat-sub"><?php echo $i18n_m('tld_lang_Page_Views'); ?></div>
			</div>
			<div class="tld-side-stat tld-stat-now">
				<div class="tld-stat-label"><?php echo $i18n_m('tld_lang_Live_Now'); ?></div>
				<div class="tld-stat-value"><span class="tld-live-dot"></span><?php echo (int)($data['live_now'] ?? 0); ?></div>
				<div class="tld-stat-sub"><?php echo $i18n_m('tld_lang_Online_Now'); ?></div>
			</div>
		</div>

		<?php if (!$has_data): ?>
			<div class="tld-empty"><?php echo $i18n_m('tld_lang_No_Data'); ?></div>
		<?php else: ?>
			<div class="tld-chart-wrap"><canvas id="<?php echo $uid ?>-canvas"></canvas></div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>

<?php if ($has_data): ?>
<script src="<?php echo $chart_js_url ?>"></script>
<script>
(function () {
	var canvas = document.getElementById('<?php echo $uid ?>-canvas');
	if (!canvas || typeof Chart === 'undefined') return;

	new Chart(canvas.getContext('2d'), {
		type: 'line',
		data: {
			labels: <?php echo json_encode($chart_labels); ?>,
			datasets: [
				{
					label: <?php echo json_encode($i18n_m('tld_lang_Legend_Visitors')); ?>,
					data: <?php echo json_encode($data['uniques']); ?>,
					borderColor: '#2764e7',
					backgroundColor: 'rgba(39,100,231,.08)',
					fill: true,
					tension: .25,
					pointRadius: 0,
				},
				{
					label: <?php echo json_encode($i18n_m('tld_lang_Legend_Views')); ?>,
					data: <?php echo json_encode($data['views']); ?>,
					borderColor: '#e67e22',
					backgroundColor: 'transparent',
					tension: .25,
					pointRadius: 0,
				},
			],
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			interaction: { mode: 'index', intersect: false },
			plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 10 } } } },
			scales: {
				x: { ticks: { maxRotation: 0, autoSkip: true, font: { size: 9 } } },
				y: { beginAtZero: true, ticks: { precision: 0, font: { size: 9 } } },
			},
		},
	});
})();
</script>
<?php endif; ?>
