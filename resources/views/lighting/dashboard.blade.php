{{-- resources/views/lighting/dashboard.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Lighting Control — {{ $device_code }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; margin: 24px; background:#f6f8fb; color:#111; }
    .card { background:white; border-radius:10px; box-shadow:0 6px 18px rgba(15,23,42,0.06); padding:16px; margin-bottom:16px; }
    .row { display:flex; gap:12px; align-items:center; }
    .col { display:flex; flex-direction:column; gap:8px; }
    button { padding:8px 12px; border-radius:8px; border: none; cursor:pointer; }
    .btn-primary { background:#0ea5a4; color:white; }
    .btn-danger { background:#ef4444; color:white; }
    .btn-ghost { background:transparent; border:1px solid #e6e9ef; }
    label { font-size:13px; color:#334155; }
    input, select { padding:8px; border-radius:8px; border:1px solid #e6e9ef; }
    .muted { color:#64748b; font-size:13px; }
    pre { background:#0b1220; color:#d1fae5; padding:12px; border-radius:8px; overflow:auto; }
    .status-dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:8px; vertical-align:middle; }
  </style>
</head>
<body>
  <h2>Lighting — <span style="color:#0ea5a4">{{ $device_code }}</span></h2>

  <div class="card" id="statusCard">
    <div style="display:flex;justify-content:space-between;align-items:center;">
      <div>
        <div class="muted">Realtime status</div>
        <h3 id="lampLabel"><span class="status-dot" id="lampDot" style="background:gray"></span> Lamp: <span id="lampStatus">—</span></h3>
        <div class="muted">Lux: <strong id="luxVal">—</strong></div>
        <div class="muted">Mode: <strong id="modeVal">—</strong></div>
        <div class="muted">Last updated: <span id="lastUpdated">—</span></div>
      </div>

      <div style="text-align:right;">
        <div class="muted">Quick manual</div>
        <div style="margin-top:8px;">
          <button class="btn-primary" id="btnOn">TURN ON</button>
          <button class="btn-danger" id="btnOff">TURN OFF</button>
        </div>
        <div style="margin-top:12px;">
          <button class="btn-ghost" id="btnRefresh">Refresh now</button>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <h4>Change mode</h4>
    <div class="row" style="margin-top:8px;">
      <select id="selectMode">
        <option value="MANUAL">MANUAL</option>
        <option value="AUTO_LUX">AUTO_LUX</option>
        <option value="AUTO_TIME">AUTO_TIME</option>
      </select>
      <button class="btn-primary" id="btnSetMode">Set Mode</button>
      <div class="muted" id="modeMsg"></div>
    </div>
  </div>

  <div class="card">
    <h4>Config (send to device)</h4>
    <div class="col">
      <div class="row">
        <div style="flex:1;">
          <label>Lux threshold</label>
          <input type="number" id="cfgLux" placeholder="e.g. 300">
        </div>
        <div style="width:12px"></div>
        <div style="flex:1;">
          <label>Auto ON delay (sec)</label>
          <input type="number" id="cfgOnDelay" placeholder="0">
        </div>
        <div style="width:12px"></div>
        <div style="flex:1;">
          <label>Auto OFF delay (sec)</label>
          <input type="number" id="cfgOffDelay" placeholder="0">
        </div>
      </div>

      <div class="row" style="margin-top:8px;">
        <div style="flex:1;">
          <label>On time (HH:MM)</label>
          <input type="text" id="cfgOnTime" placeholder="18:00">
        </div>
        <div style="width:12px"></div>
        <div style="flex:1;">
          <label>Off time (HH:MM)</label>
          <input type="text" id="cfgOffTime" placeholder="06:00">
        </div>
        <div style="width:12px"></div>
        <div style="flex:1;">
          <label>Active days (e.g. Mon-Fri or Mon,Wed,Fri)</label>
          <input type="text" id="cfgDays" placeholder="Mon-Sun">
        </div>
      </div>

      <div class="row" style="margin-top:8px; align-items:center;">
        <label style="margin-right:8px;">Allow manual override</label>
        <select id="cfgAllow">
          <option value="1">Yes</option>
          <option value="0">No</option>
        </select>
        <div style="flex:1"></div>
        <button class="btn-primary" id="btnSendConfig">Send Config</button>
      </div>

      <div id="cfgMsg" class="muted" style="margin-top:8px;"></div>
    </div>
  </div>

  <div class="card">
    <h4>Raw status & logs</h4>
    <pre id="rawOutput">—</pre>
  </div>

<script>
  const DEVICE = "{{ $device_code }}";
  const baseApi = `/api/lighting/${DEVICE}`; // adjust if your API prefix differs

  const elem = {
    lampStatus: document.getElementById('lampStatus'),
    lampDot: document.getElementById('lampDot'),
    luxVal: document.getElementById('luxVal'),
    modeVal: document.getElementById('modeVal'),
    lastUpdated: document.getElementById('lastUpdated'),
    rawOutput: document.getElementById('rawOutput'),
    btnOn: document.getElementById('btnOn'),
    btnOff: document.getElementById('btnOff'),
    btnRefresh: document.getElementById('btnRefresh'),
    selectMode: document.getElementById('selectMode'),
    btnSetMode: document.getElementById('btnSetMode'),
    modeMsg: document.getElementById('modeMsg'),
    cfgLux: document.getElementById('cfgLux'),
    cfgOnDelay: document.getElementById('cfgOnDelay'),
    cfgOffDelay: document.getElementById('cfgOffDelay'),
    cfgOnTime: document.getElementById('cfgOnTime'),
    cfgOffTime: document.getElementById('cfgOffTime'),
    cfgDays: document.getElementById('cfgDays'),
    cfgAllow: document.getElementById('cfgAllow'),
    btnSendConfig: document.getElementById('btnSendConfig'),
    cfgMsg: document.getElementById('cfgMsg')
  };

  async function fetchStatus(showRaw=true) {
    try {
        const res = await fetch(`${baseApi}/status`, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Status fetch failed: ' + res.status);
        const data = await res.json();

        // --- AUTO DETECT LUX FIELD ---
        const lux =
        data.current_lux ??
        data.lux ??
        data.sensor_lux ??
        data.reading ??
        null;

        elem.luxVal.textContent = lux !== null ? lux : '—';

        // lamp + mode
        elem.lampStatus.textContent = data.lamp_status ?? '—';
        elem.modeVal.textContent = data.mode ?? '—';
        elem.lastUpdated.textContent = new Date().toLocaleString();

        // lamp dot
        elem.lampDot.style.background =
        (data.lamp_status && data.lamp_status.toUpperCase() === 'ON')
            ? '#10b981'
            : '#ef4444';

        // raw
        if (showRaw)
        elem.rawOutput.textContent = JSON.stringify(data, null, 2);

        // settings handling
        if (data.settings) {
        const s = data.settings;
        elem.cfgLux.value = s.lux_threshold ?? '';
        elem.cfgOnDelay.value = s.auto_on_delay_sec ?? '';
        elem.cfgOffDelay.value = s.auto_off_delay_sec ?? '';
        elem.cfgOnTime.value = s.on_time ?? '';
        elem.cfgOffTime.value = s.off_time ?? '';
        elem.cfgDays.value = s.active_days ?? '';
        elem.cfgAllow.value = s.allow_manual_override ? '1' : '0';
        }

        // mode selector
        if (data.mode) elem.selectMode.value = data.mode;

        return data;
    } catch (err) {
        console.error(err);
        elem.rawOutput.textContent = 'Error: ' + err.message;
        return null;
    }
    }

  async function postJson(url, payload) {
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (!res.ok) throw new Error((data && data.error) ? data.error : res.statusText);
      return data;
    } catch (err) {
      throw err;
    }
  }

  // Manual ON
  elem.btnOn.addEventListener('click', async () => {
    elem.btnOn.disabled = true;
    try {
      await postJson(`${baseApi}/manual`, { command: 'ON' });
      elem.modeMsg.textContent = 'Sent ON';
      await fetchStatus(false);
    } catch (e) {
      elem.modeMsg.textContent = 'Error: ' + e.message;
    } finally { elem.btnOn.disabled = false; }
  });

  // Manual OFF
  elem.btnOff.addEventListener('click', async () => {
    elem.btnOff.disabled = true;
    try {
      await postJson(`${baseApi}/manual`, { command: 'OFF' });
      elem.modeMsg.textContent = 'Sent OFF';
      await fetchStatus(false);
    } catch (e) {
      elem.modeMsg.textContent = 'Error: ' + e.message;
    } finally { elem.btnOff.disabled = false; }
  });

  // Refresh
  elem.btnRefresh.addEventListener('click', () => fetchStatus());

  // Set Mode
  elem.btnSetMode.addEventListener('click', async () => {
    const m = elem.selectMode.value;
    elem.btnSetMode.disabled = true;
    try {
      await postJson(`${baseApi}/mode`, { mode: m });
      elem.modeMsg.textContent = 'Mode set to ' + m;
      await fetchStatus(false);
    } catch (e) {
      elem.modeMsg.textContent = 'Error: ' + e.message;
    } finally { elem.btnSetMode.disabled = false; }
  });

  // Send config
  elem.btnSendConfig.addEventListener('click', async () => {
    const payload = {
      lux_threshold: parseInt(elem.cfgLux.value) || null,
      auto_on_delay_sec: parseInt(elem.cfgOnDelay.value) || 0,
      auto_off_delay_sec: parseInt(elem.cfgOffDelay.value) || 0,
      on_time: elem.cfgOnTime.value || null,
      off_time: elem.cfgOffTime.value || null,
      active_days: elem.cfgDays.value || null,
      allow_manual_override: elem.cfgAllow.value === '1' ? 1 : 0
    };
    elem.btnSendConfig.disabled = true;
    elem.cfgMsg.textContent = 'Sending config...';
    try {
      await postJson(`${baseApi}/config`, payload);
      elem.cfgMsg.textContent = 'Config sent';
      await fetchStatus(false);
    } catch (e) {
      elem.cfgMsg.textContent = 'Error: ' + e.message;
    } finally { elem.btnSendConfig.disabled = false; }
  });

  // Polling
  (async function init() {
    await fetchStatus();
    setInterval(fetchStatus, 4000);
  })();
</script>
</body>
</html>
