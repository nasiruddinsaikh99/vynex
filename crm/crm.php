<?php
/**
 * Mini CRM - Single File (PHP + MySQLi + Tailwind)
 * Requirements covered:
 * - Single PHP file (no PDO/frameworks) using MySQLi
 * - Tailwind UI, live search, pagination (50/page), smart sort
 * - Right-side dynamic detail view without full reload (AJAX JSON in same file)
 * - Safe SQL (prepared statements), XSS-escaped output
 * - Show More/Less for long fields, color-coded email_status badge
 * - Email prompt builder + Copy button
 * - Notes system with AJAX save, CSRF protection, styled errors
 */

declare(strict_types=1);
session_start();
header_remove("X-Powered-By");

// ---------- CONFIG ----------
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'baila';
$DB_PASS = getenv('DB_PASS') ?: 'baila!23';
$DB_NAME = getenv('DB_NAME') ?: 'syntrex';
$PER_PAGE = 50;

// ---------- UTIL ----------
function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function is_ajax(): bool { return isset($_GET['action']) || (isset($_POST['action'])); }
function respond_json(array $payload, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
    return $_SESSION['csrf'];
}
function verify_csrf(string $token): bool {
    return hash_equals($_SESSION['csrf'] ?? '', $token);
}
function map_email_status_label($raw): string {
    $map = [
        0 => 'Fresh',
        1 => 'One Time Send',
        2 => 'Follow Up',
        3 => 'Active',
        4 => 'Inactive',
        5 => 'Bounced',
    ];
    if ($raw === null || $raw === '') return 'N/A';
    if (is_numeric($raw)) {
        $i = (int)$raw;
        return $map[$i] ?? 'N/A';
    }
    $t = trim((string)$raw);
    // Normalize some common variants
    $norm = strtolower($t);
    foreach ($map as $lbl) {
        if (strtolower($lbl) === $norm) return $lbl;
    }
    return $t; // fallback to given text
}
function badge_classes(string $label): string {
    $k = strtolower($label);
    return match (true) {
        $k === 'fresh' => 'bg-gray-100 text-gray-700 ring-1 ring-gray-200',
        $k === 'one time send' => 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200',
        $k === 'follow up' => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',
        $k === 'active' => 'bg-green-100 text-green-700 ring-1 ring-green-200',
        $k === 'inactive' => 'bg-zinc-100 text-zinc-700 ring-1 ring-zinc-200',
        $k === 'bounced' => 'bg-rose-100 text-rose-700 ring-1 ring-rose-200',
        default => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
    };
}

// ---------- DB CONNECT ----------
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $mysqli->set_charset('utf8mb4');
} catch (Throwable $e) {
    if (is_ajax()) {
        respond_json(['ok' => false, 'error' => 'DB connection failed', 'detail' => $e->getMessage()], 500);
    }
    $db_error = 'Database connection failed: ' . h($e->getMessage());
}

// ---------- SUPPORT: Ensure notes table ----------
$notes_table_status = null;
if (empty($db_error)) {
    try {
        $mysqli->query(
            "CREATE TABLE IF NOT EXISTS crm_notes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                crm_id INT NOT NULL,
                notes TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (crm_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;"
        );
        $notes_table_status = "Notes table ready.";
    } catch (Throwable $e) {
        $notes_table_status = "Notes table issue: " . h($e->getMessage());
    }
}

// ---------- AJAX ROUTES ----------
if (is_ajax() && empty($db_error)) {
    try {
        $action = $_GET['action'] ?? $_POST['action'] ?? '';
        if ($action === 'list') {
            $page = max(1, (int)($_GET['page'] ?? 1));
            $q    = trim((string)($_GET['q'] ?? ''));
            $where = '';
            $params = [];
            $types = '';

            if ($q !== '') {
                $where = "WHERE (email LIKE ? OR website_name LIKE ? OR url LIKE ? OR email_name LIKE ?)";
                $needle = '%' . $q . '%';
                $params = [$needle, $needle, $needle, $needle];
                $types  = 'ssss';
            }

            // Count
            if ($where) {
                $stmt = $mysqli->prepare("SELECT COUNT(*) AS c FROM crm $where");
                $stmt->bind_param($types, ...$params);
            } else {
                $stmt = $mysqli->prepare("SELECT COUNT(*) AS c FROM crm");
            }
            $stmt->execute();
            $res = $stmt->get_result();
            $total = (int)($res->fetch_assoc()['c'] ?? 0);
            $stmt->close();

            $offset = ($page - 1) * $PER_PAGE;
            // ORDER BY: non-empty website_name first, then website_name asc, then email asc
            $order = "ORDER BY (website_name IS NULL OR website_name='') ASC, website_name ASC, email ASC";
            // LIMIT/OFFSET MUST be integers; we’ll cast and embed safely
            if ($where) {
                $stmt = $mysqli->prepare("SELECT id, website_name, email, url, email_status, industry, profession 
                                          FROM crm $where $order LIMIT $PER_PAGE OFFSET $offset");
                $stmt->bind_param($types, ...$params);
            } else {
                $stmt = $mysqli->prepare("SELECT id, website_name, email, url, email_status, industry, profession 
                                          FROM crm $order LIMIT $PER_PAGE OFFSET $offset");
            }
            $stmt->execute();
            $rows = [];
            $r = $stmt->get_result();
            while ($row = $r->fetch_assoc()) {
                $row['email_status_label'] = map_email_status_label($row['email_status']);
                $rows[] = $row;
            }
            $stmt->close();

            respond_json([
                'ok' => true,
                'page' => $page,
                'per_page' => $PER_PAGE,
                'total' => $total,
                'rows' => $rows
            ]);
        }
        elseif ($action === 'detail') {
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) respond_json(['ok' => false, 'error' => 'Invalid ID'], 400);

            $stmt = $mysqli->prepare("SELECT * FROM crm WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$row) respond_json(['ok' => false, 'error' => 'Customer not found'], 404);

            $row['email_status_label'] = map_email_status_label($row['email_status']);

            // Latest note (if any)
            $note = null;
            $updated_at = null;
            $stmt = $mysqli->prepare("SELECT notes, updated_at FROM crm_notes WHERE crm_id = ? ORDER BY updated_at DESC LIMIT 1");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            if ($nres = $stmt->get_result()->fetch_assoc()) {
                $note = $nres['notes'];
                $updated_at = $nres['updated_at'];
            }
            $stmt->close();

            respond_json([
                'ok' => true,
                'customer' => $row,
                'note' => $note,
                'note_updated_at' => $updated_at,
                'validation' => 'Detail loaded OK.'
            ]);
        }
        elseif ($action === 'save_note') {
            $id = (int)($_POST['id'] ?? 0);
            $note = trim((string)($_POST['note'] ?? ''));
            $tok = (string)($_POST['csrf'] ?? '');

            if ($id <= 0) respond_json(['ok' => false, 'error' => 'Invalid ID'], 400);
            if (!verify_csrf($tok)) respond_json(['ok' => false, 'error' => 'Security token invalid. Please refresh.'], 403);
            if ($note === '') respond_json(['ok' => false, 'error' => 'Note cannot be empty.'], 422);

            // Ensure record exists
            $stmt = $mysqli->prepare("SELECT 1 FROM crm WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $exists = (bool)$stmt->get_result()->fetch_row();
            $stmt->close();
            if (!$exists) respond_json(['ok' => false, 'error' => 'Customer not found.'], 404);

            $stmt = $mysqli->prepare("INSERT INTO crm_notes (crm_id, notes) VALUES (?, ?)");
            $stmt->bind_param('is', $id, $note);
            $stmt->execute();
            $stmt->close();

            respond_json(['ok' => true, 'message' => 'Note saved.', 'chars' => mb_strlen($note)]);
        }
        else {
            respond_json(['ok' => false, 'error' => 'Unknown action'], 400);
        }
    } catch (Throwable $e) {
        respond_json(['ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()], 500);
    }
}

// ---------- HTML ----------
$token = csrf_token();
?><!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf" content="<?= h($token) ?>">
  <title>Mini CRM — Syntrex</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Truncation helper */
    .truncate-clip[data-expanded="false"] { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .soft-shadow { box-shadow: 0 10px 30px rgba(0,0,0,.06); }
    .btn { @apply inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium ring-1 ring-slate-200 hover:bg-slate-50; }
    .btn-primary { @apply bg-slate-900 text-white hover:bg-slate-800 ring-0; }
    .badge { @apply inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold; }
  </style>
</head>
<body class="h-full bg-slate-50 text-slate-900">
  <div class="min-h-screen">
    <header class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-slate-200">
      <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-xl bg-slate-900 text-white grid place-items-center font-bold">CR</div>
          <h1 class="text-lg font-semibold">Mini CRM</h1>
          <span class="text-xs text-slate-500">PHP (MySQLi) • Tailwind • Single File</span>
        </div>
        <div class="text-xs text-slate-500">
          <?php if (!empty($notes_table_status)) : ?>
            <span class="px-2 py-1 rounded bg-slate-100"><?= h($notes_table_status) ?></span>
          <?php endif; ?>
          <?php if (!empty($db_error)) : ?>
            <span class="ml-2 px-2 py-1 rounded bg-rose-100 text-rose-700"><?= $db_error ?></span>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-5 grid grid-cols-12 gap-5">
      <!-- Sidebar -->
      <aside class="col-span-12 md:col-span-4 lg:col-span-3">
        <div class="soft-shadow rounded-2xl bg-white p-4 space-y-4">
          <div>
            <label for="search" class="block text-sm font-medium text-slate-700 mb-1">Search</label>
            <input id="search" type="text" placeholder="Email, name, or website..." class="w-full rounded-xl border-slate-200 focus:ring-2 focus:ring-slate-300" />
            <p class="mt-1 text-xs text-slate-500">Live server search. 50 per page.</p>
          </div>

          <div class="flex items-center justify-between">
            <div class="text-xs text-slate-600">
              <span id="list-count">0</span> results
            </div>
            <div class="flex items-center gap-2">
              <button id="prev" class="btn" disabled>&larr; Prev</button>
              <button id="next" class="btn" disabled>Next &rarr;</button>
            </div>
          </div>

          <ul id="list" class="divide-y divide-slate-100 rounded-xl overflow-hidden border border-slate-200"></ul>
        </div>
      </aside>

      <!-- Detail View -->
      <section class="col-span-12 md:col-span-8 lg:col-span-9">
        <div id="detail-card" class="soft-shadow rounded-2xl bg-white p-5">
          <div class="flex items-start justify-between">
            <div>
              <h2 id="detail-title" class="text-xl font-semibold">Select a customer</h2>
              <p class="text-sm text-slate-600" id="detail-sub">Choose from the list to view details and compose an email prompt.</p>
            </div>
            <div class="flex gap-2">
              <button id="toggle-edit" class="btn" disabled>Enable Quick Edit</button>
              <span id="status-badge" class="badge hidden"></span>
            </div>
          </div>

          <div id="fields" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4"></div>

          <div class="mt-6 border-t pt-4">
            <h3 class="font-semibold mb-2">Email Prompt</h3>
            <div class="flex items-center gap-2 mb-2">
              <button id="copy-btn" class="btn btn-primary">Copy Prompt</button>
              <span id="copy-msg" class="text-xs text-slate-600"></span>
            </div>
            <pre id="prompt" class="whitespace-pre-wrap text-sm bg-slate-50 border border-slate-200 rounded-xl p-3"></pre>
          </div>

          <div class="mt-6 border-t pt-4">
            <h3 class="font-semibold mb-2">Notes</h3>
            <div id="note-info" class="text-xs text-slate-500 mb-2"></div>
            <textarea id="note-text" class="w-full min-h-[120px] rounded-xl border-slate-200 focus:ring-2 focus:ring-slate-300" placeholder="Type notes here..."></textarea>
            <div class="mt-2 flex items-center gap-2">
              <button id="save-note" class="btn btn-primary" disabled>Save Notes</button>
              <button id="cancel-note" class="btn" disabled>Cancel</button>
              <span id="note-msg" class="text-xs"></span>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="max-w-7xl mx-auto px-4 pb-8">
      <p class="text-xs text-slate-500">Validation: UI actions report success/failure inline. Backend returns JSON with explicit status.</p>
    </footer>
  </div>

  <script>
  // --- Helpers ---
  const $ = sel => document.querySelector(sel);
  const listEl = $('#list');
  const prevBtn = $('#prev'), nextBtn = $('#next'), countEl = $('#list-count'), searchEl = $('#search');
  const fieldsEl = $('#fields'), titleEl = $('#detail-title'), subEl = $('#detail-sub'), statusBadge = $('#status-badge');
  const toggleEditBtn = $('#toggle-edit'), copyBtn = $('#copy-btn'), copyMsg = $('#copy-msg'), promptEl = $('#prompt');
  const noteText = $('#note-text'), saveNoteBtn = $('#save-note'), cancelNoteBtn = $('#cancel-note'), noteMsg = $('#note-msg'), noteInfo = $('#note-info');
  const csrf = document.querySelector('meta[name="csrf"]').getAttribute('content');
  let state = { page: 1, total: 0, per_page: 50, q: '', rows: [], selectedId: null, quickEdit: false, originalFields: null };

  const statusBadgeClass = (label) => {
    const k = (label || '').toLowerCase();
    if (k === 'fresh') return 'bg-gray-100 text-gray-700 ring-1 ring-gray-200';
    if (k === 'one time send') return 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200';
    if (k === 'follow up') return 'bg-amber-100 text-amber-700 ring-1 ring-amber-200';
    if (k === 'active') return 'bg-green-100 text-green-700 ring-1 ring-green-200';
    if (k === 'inactive') return 'bg-zinc-100 text-zinc-700 ring-1 ring-zinc-200';
    if (k === 'bounced') return 'bg-rose-100 text-rose-700 ring-1 ring-rose-200';
    return 'bg-slate-100 text-slate-700 ring-1 ring-slate-200';
  };

  const esc = (s) => {
    const d = document.createElement('div'); d.textContent = (s ?? ''); return d.innerHTML;
  }

  function debounce(fn, ms=300) {
    let t; return (...args) => { clearTimeout(t); t = setTimeout(()=>fn(...args), ms); };
  }

  // --- List Fetch ---
  async function fetchList(page=1) {
    const params = new URLSearchParams({ action:'list', page:String(page), q: state.q });
    const res = await fetch(location.pathname + '?' + params.toString(), { headers: { 'Accept': 'application/json' }});
    const data = await res.json();
    if (!data.ok) throw new Error(data.error || 'List load failed');
    state.page = data.page; state.total = data.total; state.per_page = data.per_page; state.rows = data.rows;
    renderList();
    if (state.rows.length && !state.selectedId) {
      selectCustomer(state.rows[0].id);
    }
  }

  function renderList() {
    countEl.textContent = state.total;
    prevBtn.disabled = (state.page <= 1);
    nextBtn.disabled = (state.page * state.per_page >= state.total);
    listEl.innerHTML = '';
    if (!state.rows.length) {
      listEl.innerHTML = `<li class="p-4 text-sm text-slate-500">No results.</li>`;
      return;
    }
    for (const r of state.rows) {
      const li = document.createElement('li');
      li.className = 'p-3 hover:bg-slate-50 cursor-pointer';
      li.innerHTML = `
        <div class="flex items-center justify-between gap-3">
          <div class="min-w-0">
            <div class="text-sm font-medium text-slate-900 truncate">${esc(r.website_name || r.email || 'Unknown')}</div>
            <div class="text-xs text-slate-600 truncate">${esc(r.email || 'N/A')}</div>
            ${r.url ? `<div class="text-xs text-slate-500 truncate">${esc(r.url)}</div>` : ``}
          </div>
          <span class="badge ${statusBadgeClass(r.email_status_label)}">${esc(r.email_status_label || 'N/A')}</span>
        </div>`;
      li.addEventListener('click', () => selectCustomer(r.id));
      listEl.appendChild(li);
    }
  }

  // --- Detail Fetch/Render ---
  async function selectCustomer(id) {
    state.selectedId = id;
    toggleEditBtn.disabled = true;
    saveNoteBtn.disabled = true;
    cancelNoteBtn.disabled = true;
    noteMsg.textContent = '';
    noteInfo.textContent = '';
    const params = new URLSearchParams({ action:'detail', id:String(id) });
    const res = await fetch(location.pathname + '?' + params.toString(), { headers: { 'Accept': 'application/json' }});
    const data = await res.json();
    if (!data.ok) { titleEl.textContent = 'Error loading customer'; subEl.textContent = data.error || 'Unknown error'; return; }
    renderDetail(data.customer, data.note, data.note_updated_at);
  }

  function renderDetail(c, note, noteUpdatedAt) {
    titleEl.textContent = c.website_name || c.email || 'Customer';
    subEl.textContent = c.url || c.secure_url || '—';
    // Status badge
    if (c.email_status_label) {
      statusBadge.className = `badge ${statusBadgeClass(c.email_status_label)}`;
      statusBadge.textContent = c.email_status_label;
      statusBadge.classList.remove('hidden');
    } else {
      statusBadge.classList.add('hidden');
    }

    // Build fields (all columns shown with labels)
    const labels = {
      id: 'ID', website_id: 'Website ID', url:'Website URL', secure_url: 'Secure URL',
      website_name:'Website Name', email:'Email', email_name:'Contact Name',
      facebook_page:'Facebook Page', website_phone:'Phone', email_from:'Email From',
      website_active:'Website Active', start_date:'Start Date', country:'Country',
      profession:'Profession', industry:'Industry', trade:'Trade', member_type:'Member Type',
      currency_code:'Currency', status:'Status (int)', email_status:'Email Status (raw)'
    };
    fieldsEl.innerHTML = '';
    state.originalFields = {}; // for quick edit reset
    Object.keys(labels).forEach(k => {
      const raw = c[k];
      const val = raw == null || raw === '' ? 'N/A' : String(raw);
      state.originalFields[k] = val;
      const isLink = (k === 'url' || k === 'secure_url' || k === 'facebook_page') && raw;
      const long = val.length > 50;
      const id = `field-${k}`;
      const block = document.createElement('div');
      block.className = 'rounded-xl border border-slate-200 p-3';
      block.innerHTML = `
        <div class="text-xs font-medium text-slate-500 mb-1">${esc(labels[k])}</div>
        <div class="text-sm leading-relaxed">
          <div id="${id}" class="${long ? 'truncate-clip' : ''}" data-expanded="false" data-key="${esc(k)}" contenteditable="false">
            ${isLink ? `<a href="${esc(val)}" target="_blank" rel="noopener" class="underline">${esc(val)}</a>` : esc(val)}
          </div>
          ${long ? `<button class="mt-1 text-xs text-slate-700 underline" data-toggle="${id}">Show More</button>` : ``}
        </div>`;
      fieldsEl.appendChild(block);
    });

    // Hook Show More/Less toggles
    fieldsEl.querySelectorAll('button[data-toggle]').forEach(btn => {
      btn.addEventListener('click', () => {
        const tId = btn.getAttribute('data-toggle');
        const el = document.getElementById(tId);
        const expanded = el.getAttribute('data-expanded') === 'true';
        el.setAttribute('data-expanded', expanded ? 'false' : 'true');
        btn.textContent = expanded ? 'Show More' : 'Show Less';
      });
    });

    // Notes area
    noteText.value = note || '';
    saveNoteBtn.disabled = false;
    cancelNoteBtn.disabled = false;
    noteInfo.textContent = noteUpdatedAt ? `Last updated: ${noteUpdatedAt}` : 'No saved notes yet.';

    // Enable Quick Edit (for prompt-only client edits)
    toggleEditBtn.disabled = false;
    state.quickEdit = false;
    toggleEditBtn.textContent = 'Enable Quick Edit';
    fieldsEl.querySelectorAll('[contenteditable]').forEach(el => { el.setAttribute('contenteditable','false'); });

    buildPromptFromFields();
  }

  // Build the email prompt from visible values (including any quick edits)
  function buildPromptFromFields() {
    if (!state.selectedId) { promptEl.textContent = ''; return; }
    const getVal = (key) => {
      const el = document.querySelector(`#field-${key}`);
      if (!el) return '';
      return el.textContent.trim() === 'N/A' ? '' : el.textContent.trim();
    };
    const email = getVal('email');
    const website_name = getVal('website_name');
    const industry = getVal('industry');
    const profession = getVal('profession');
    const url = getVal('url') || getVal('secure_url');
    const country = getVal('country');
    const member_type = getVal('member_type');
    const contact_name = getVal('email_name');

    const parts = [];
    if (website_name) parts.push(`${website_name}`);
    if (industry) parts.push(`Industry: ${industry}`);
    if (profession) parts.push(`Profession: ${profession}`);
    if (member_type) parts.push(`Member Type: ${member_type}`);
    if (country) parts.push(`Country: ${country}`);
    if (url) parts.push(`Website: ${url}`);

    const header = `Write an email to ${email || 'N/A'} (${parts.join(', ')})`;
    const body = [
      '',
      'Goal: Introduce our AI-powered DirectoryAgent to improve discovery, leads, and support on their site.',
      'Tone: Polite, concise, helpful. Avoid jargon. Offer a quick call or demo.',
      contact_name ? `Personalize: Greet ${contact_name} and reference their site.` : null,
      'CTA: Ask for a convenient time for a 15-minute call or permission to send a short demo video.',
    ].filter(Boolean).join('\n');

    promptEl.textContent = header + '\n' + body;
  }

  // Observe edits (when enabled) to update prompt live
  function onFieldInput(e) {
    if (!state.quickEdit) return;
    if (e.target.matches('[contenteditable]')) buildPromptFromFields();
  }
  fieldsEl.addEventListener('input', onFieldInput);

  // Toggle quick edit
  toggleEditBtn.addEventListener('click', () => {
    state.quickEdit = !state.quickEdit;
    toggleEditBtn.textContent = state.quickEdit ? 'Disable Quick Edit' : 'Enable Quick Edit';
    fieldsEl.querySelectorAll('[contenteditable]').forEach(el => {
      const key = el.getAttribute('data-key');
      // allow edits except links; convert links to text when enabling
      if (state.quickEdit) {
        // If link, replace with text content
        if (el.querySelector('a')) el.innerHTML = esc(el.textContent.trim());
        el.setAttribute('contenteditable','true');
        el.classList.add('outline-none','ring-1','ring-slate-200','rounded');
      } else {
        el.setAttribute('contenteditable','false');
        el.classList.remove('ring-1','ring-slate-200','rounded');
        // reset to original visual value (does not save to DB)
        const k = el.getAttribute('data-key');
        if (k && state.originalFields && state.originalFields[k] !== undefined) el.textContent = state.originalFields[k];
      }
    });
    buildPromptFromFields();
  });

  // Copy prompt
  copyBtn.addEventListener('click', async () => {
    copyMsg.textContent = '';
    try {
      await navigator.clipboard.writeText(promptEl.textContent);
      copyMsg.textContent = 'Copied!';
      copyMsg.className = 'text-xs text-green-700';
    } catch (e) {
      // Fallback
      const area = document.createElement('textarea');
      area.value = promptEl.textContent;
      document.body.appendChild(area);
      area.select();
      try {
        document.execCommand('copy');
        copyMsg.textContent = 'Copied!';
        copyMsg.className = 'text-xs text-green-700';
      } catch (e2) {
        copyMsg.textContent = 'Copy failed. Select and copy manually.';
        copyMsg.className = 'text-xs text-rose-700';
      } finally {
        document.body.removeChild(area);
      }
    }
  });

  // Save/Cancel notes
  saveNoteBtn.addEventListener('click', async () => {
    if (!state.selectedId) return;
    noteMsg.textContent = '';
    const note = noteText.value.trim();
    if (!note) {
      noteMsg.textContent = 'Note cannot be empty.';
      noteMsg.className = 'text-xs text-rose-700';
      return;
    }
    const form = new FormData();
    form.append('action','save_note');
    form.append('id', String(state.selectedId));
    form.append('note', note);
    form.append('csrf', csrf);
    const res = await fetch(location.pathname, { method:'POST', body: form });
    const data = await res.json();
    if (!data.ok) {
      noteMsg.textContent = data.error || 'Save failed.';
      noteMsg.className = 'text-xs text-rose-700';
      return;
    }
    noteMsg.textContent = 'Saved! (' + (data.chars || note.length) + ' chars)';
    noteMsg.className = 'text-xs text-green-700';
    // Refresh detail meta (updated_at)
    selectCustomer(state.selectedId);
  });

  cancelNoteBtn.addEventListener('click', () => {
    noteMsg.textContent = 'Canceled.';
    noteMsg.className = 'text-xs text-slate-600';
    // Re-load detail to restore last saved text (non-destructive)
    if (state.selectedId) selectCustomer(state.selectedId);
  });

  // Pagination
  prevBtn.addEventListener('click', () => {
    if (state.page > 1) fetchList(state.page - 1).catch(showTopError);
  });
  nextBtn.addEventListener('click', () => {
    if (state.page * state.per_page < state.total) fetchList(state.page + 1).catch(showTopError);
  });

  // Search (debounced)
  const doSearch = debounce(() => {
    state.q = searchEl.value.trim();
    state.selectedId = null;
    fetchList(1).catch(showTopError);
  }, 350);
  searchEl.addEventListener('input', doSearch);

  function showTopError(err) {
    titleEl.textContent = 'Error';
    subEl.textContent = (err && err.message) ? err.message : 'Something went wrong.';
  }

  // Init
  (function init(){
    <?php if (!empty($db_error)) : ?>
      // DB error already shown in header; disable UI
      prevBtn.disabled = true; nextBtn.disabled = true; searchEl.disabled = true;
      titleEl.textContent = 'Database error';
      subEl.textContent = 'Please check connection settings in the PHP file.';
    <?php else: ?>
      fetchList(1).catch(showTopError);
    <?php endif; ?>
  })();
  </script>
</body>
</html>
