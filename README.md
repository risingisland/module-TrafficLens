# TrafficLens — Dashboard Module

A compact "Daily Visitors" widget for the [Dashboard](https://github.com/GetSimpleCMS-CE-plugins/plugin-Dashboard)
plugin, showing a 30-day visitor chart plus this week's page views and the
current live visitor count — powered by the [TrafficLens](../../../TrafficLens.php) plugin.

## Requirements

- The **Dashboard** plugin, installed and active
- The **TrafficLens** plugin, installed and active (this module reads directly from it — it has no data of its own)

## Installation

Copy `trafficlens.php` and the `trafficlens/` folder into Dashboard's `modules/`
directory, as siblings:

```
plugins/
└── Dashboard/
    └── modules/
        ├── trafficlens.php
        └── trafficlens/
            └── lang/
                ├── .htaccess
                ├── en_US.php
                └── es_ES.php
```

Then, in the GS admin, go to **Dashboard → Settings** and enable **TrafficLens**
from the module list. It defaults to a full-width (12-column), 4-row tile —
resize as you like once it's on the board.

## What it shows

- **This week** — total page views over the last 7 days
- **Live now** — visitors active in the last 5 minutes, with a link straight to the full TrafficLens dashboard
- **A 30-day line chart** — unique visitors (blue) and page views (orange), by day, labeled `M/D` (no year, to save space)

The chart is rendered with the same Chart.js build TrafficLens already vendors
(`plugins/TrafficLens/inc/js/chart.umd.min.js`) — this module doesn't ship its
own copy.

## Troubleshooting

If the module shows a warning instead of the chart, it tells you exactly which of two things is wrong:

- **"TrafficLens is not active."** — the TrafficLens plugin itself isn't installed or enabled. Activate it from the plugin list.
- **"TrafficLens is active, but this module needs a newer version of the plugin..."** — TrafficLens is active, but you're running a version that predates `TrafficLens_daily_summary()` (added alongside this module). Redeploy the latest TrafficLens plugin files, no need to touch the module itself.

If it shows **"No visits recorded yet"** instead, that's not an error — TrafficLens just hasn't logged any front-end visits yet.

## Notes

- This module is display-only; all settings (data retention, excluding your own visits, etc.) are configured on the main TrafficLens dashboard, not here.
- Ships in English and Spanish, matching the main plugin.
