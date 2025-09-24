<?php
/****************************************************
 * Syntrex Mini CRM (single-file) — PHP + MySQLi + Tailwind
 * - DB: syntrex, Table: crm (schema provided by user)
 * - Extra table created if missing: crm_notes (for persistent notes)
 * - No email sending; focuses on list, detail, status, notes, and copyable AI prompts
 * --------------------------------------------------
 * Quick start:
 *   1) Put this file on your server as index.php
 *   2) Update DB credentials below
 *   3) Ensure database "syntrex" and table "crm" exist (schema per your message)
 *   4) Open in browser
 ****************************************************/

// ---------- CONFIG ----------

if (! isset($_COOKIE['adfasdfasdfasdfa'])) {
    exit;
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

$DB_HOST = 'localhost';
$DB_USER = 'baila';
$DB_PASS = 'baila!23';
$DB_NAME = 'syntrex';

// ---------- CONNECT ----------
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
if ($mysqli->connect_errno) {
    die("DB connection failed: " . $mysqli->connect_error);
}

// Create DB if not exists
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");
$mysqli->select_db($DB_NAME);

// Create crm_notes table if not exists (for persistent notes)
$mysqli->query("
CREATE TABLE IF NOT EXISTS crm_notes (
  crm_id INT PRIMARY KEY,
  notes TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");

// Helper: safely escape for HTML
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Status map (id:int -> label:string, style)
$STATUS_MAP = [
    0 => ['Fresh', 'bg-gray-100 text-gray-700'],
    1 => ['One-time Sent', 'bg-blue-100 text-blue-700'],
    2 => ['Follow-up', 'bg-amber-100 text-amber-700'],
    3 => ['Interested', 'bg-green-100 text-green-700'],
    4 => ['Not Interested', 'bg-red-100 text-red-700'],
    5 => ['Invalid', 'bg-zinc-200 text-zinc-700'],
    6 => ['Do Not Contact', 'bg-rose-100 text-rose-700'],
];

// Build WHERE filters for list
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$flt_status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : '';
$flt_country = isset($_GET['country']) ? trim($_GET['country']) : '';
$flt_industry = isset($_GET['industry']) ? trim($_GET['industry']) : '';

// Simple pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 30;
$offset = ($page - 1) * $perPage;

// Actions (create/update/status/notes) – same-page handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Insert new contact (minimal fields)
    if ($action === 'create') {
        $email = trim($_POST['email'] ?? '');
        if ($email === '') {
            header("Location: ?err=Email%20is%20required");
            exit;
        }

        // Determine next id (since original schema may not auto-increment)
        $nextId = 1;
        if ($res = $mysqli->query("SELECT COALESCE(MAX(id),0)+1 AS nid FROM crm")) {
            $row = $res->fetch_assoc();
            $nextId = (int)$row['nid'];
            $res->free();
        }

        $stmt = $mysqli->prepare("
            INSERT INTO crm (
              id, website_id, url, email, website_name, facebook_page, website_phone,
              email_from, website_active, start_date, country, profession, industry,
              trade, member_type, currency_code, status, email_status, secure_url, email_name
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");
        // Collect fields (allow nullables)
        $website_id   = $_POST['website_id']   ?? null;
        $url          = $_POST['url']          ?? null;
        $website_name = $_POST['website_name'] ?? null;
        $facebook     = $_POST['facebook_page']?? null;
        $phone        = $_POST['website_phone']?? null;
        $email_from   = $_POST['email_from']   ?? null;
        $active       = $_POST['website_active']?? null;
        $start_date   = $_POST['start_date']   ?? null;
        $country      = $_POST['country']      ?? null;
        $profession   = $_POST['profession']   ?? null;
        $industry     = $_POST['industry']     ?? null;
        $trade        = $_POST['trade']        ?? null;
        $member_type  = $_POST['member_type']  ?? null;
        $currency     = $_POST['currency_code']?? null;
        $status       = (int)($_POST['status'] ?? 0);
        $email_status = $_POST['email_status'] ?? '';
        $secure_url   = $_POST['secure_url']   ?? null;
        $email_name   = $_POST['email_name']   ?? null;

        $stmt->bind_param(
            "issssssssssssssissss",
            $nextId, $website_id, $url, $email, $website_name, $facebook, $phone,
            $email_from, $active, $start_date, $country, $profession, $industry,
            $trade, $member_type, $currency, $status, $email_status, $secure_url, $email_name
        );
        $stmt->execute();
        $stmt->close();

        header("Location: ?id={$nextId}&ok=Created");
        exit;
    }

    // Update base fields of a contact
    if ($action === 'update' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        $stmt = $mysqli->prepare("
            UPDATE crm SET
              website_id=?, url=?, email=?, website_name=?, facebook_page=?, website_phone=?,
              email_from=?, website_active=?, start_date=?, country=?, profession=?, industry=?,
              trade=?, member_type=?, currency_code=?, status=?, email_status=?, secure_url=?, email_name=?
            WHERE id=?
        ");
        $website_id   = $_POST['website_id']   ?? null;
        $url          = $_POST['url']          ?? null;
        $email        = $_POST['email']        ?? null;
        $website_name = $_POST['website_name'] ?? null;
        $facebook     = $_POST['facebook_page']?? null;
        $phone        = $_POST['website_phone']?? null;
        $email_from   = $_POST['email_from']   ?? null;
        $active       = $_POST['website_active']?? null;
        $start_date   = $_POST['start_date']   ?? null;
        $country      = $_POST['country']      ?? null;
        $profession   = $_POST['profession']   ?? null;
        $industry     = $_POST['industry']     ?? null;
        $trade        = $_POST['trade']        ?? null;
        $member_type  = $_POST['member_type']  ?? null;
        $currency     = $_POST['currency_code']?? null;
        $status       = (int)($_POST['status'] ?? 0);
        $email_status = $_POST['email_status'] ?? '';
        $secure_url   = $_POST['secure_url']   ?? null;
        $email_name   = $_POST['email_name']   ?? null;

        $stmt->bind_param(
            "sssssssssssssssissssi",
            $website_id, $url, $email, $website_name, $facebook, $phone,
            $email_from, $active, $start_date, $country, $profession, $industry,
            $trade, $member_type, $currency, $status, $email_status, $secure_url, $email_name, $id
        );
        $stmt->execute();
        $stmt->close();

        header("Location: ?id={$id}&ok=Updated");
        exit;
    }

    // Update only status + email_status quickly
    if ($action === 'update_status' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $status = (int)($_POST['status'] ?? 0);
        $email_status = $_POST['email_status'] ?? '';

        $stmt = $mysqli->prepare("UPDATE crm SET status=?, email_status=? WHERE id=?");
        $stmt->bind_param("isi", $status, $email_status, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: ?id={$id}&ok=Status%20Saved");
        exit;
    }

    // Save notes
    if ($action === 'save_notes' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $notes = $_POST['notes'] ?? '';

        // Upsert
        $stmt = $mysqli->prepare("INSERT INTO crm_notes (crm_id, notes) VALUES (?,?)
                                  ON DUPLICATE KEY UPDATE notes=VALUES(notes), updated_at=CURRENT_TIMESTAMP");
        $stmt->bind_param("is", $id, $notes);
        $stmt->execute();
        $stmt->close();

        header("Location: ?id={$id}&ok=Notes%20Saved");
        exit;
    }
}

// Build WHERE and fetch list
$where = [];
$params = [];
$types = "";

if ($q !== '') {
    $where[] = "(email LIKE CONCAT('%',?,'%') OR website_name LIKE CONCAT('%',?,'%') OR url LIKE CONCAT('%',?,'%') OR industry LIKE CONCAT('%',?,'%') OR profession LIKE CONCAT('%',?,'%'))";
    array_push($params, $q, $q, $q, $q, $q);
    $types .= "sssss";
}
if ($flt_status !== '') {
    $where[] = "status = ?";
    $params[] = $flt_status;
    $types .= "i";
}
if ($flt_country !== '') {
    $where[] = "country LIKE CONCAT('%',?,'%')";
    $params[] = $flt_country;
    $types .= "s";
}
if ($flt_industry !== '') {
    $where[] = "industry LIKE CONCAT('%',?,'%')";
    $params[] = $flt_industry;
    $types .= "s";
}

$whereSql = $where ? ("WHERE ".implode(" AND ", $where)) : "";

// Count
$countSql = "SELECT COUNT(*) AS c FROM crm $whereSql";
$stmt = $mysqli->prepare($countSql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$countRes = $stmt->get_result()->fetch_assoc();
$total = (int)$countRes['c'];
$stmt->close();
$totalPages = max(1, (int)ceil($total / $perPage));

// List query
$listSql = "SELECT id, email, website_name, url, industry, country, status, email_status
            FROM crm $whereSql
            ORDER BY id DESC
            LIMIT ? OFFSET ?";
$stmt = $mysqli->prepare($listSql);
if ($types) {
    $allParams = array_merge($params, [$perPage, $offset]);
    $stmt->bind_param($types."ii", ...$allParams);
} else {
    $stmt->bind_param("ii", $perPage, $offset);
}
$stmt->execute();
$listRes = $stmt->get_result();
$rows = $listRes->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Selected contact
$selId = isset($_GET['id']) ? (int)$_GET['id'] : (count($rows) ? (int)$rows[0]['id'] : 0);
$sel = null;
$selNotes = '';

if ($selId) {
    $stmt = $mysqli->prepare("SELECT * FROM crm WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $selId);
    $stmt->execute();
    $res = $stmt->get_result();
    $sel = $res->fetch_assoc();
    $stmt->close();

    // notes
    $stmt = $mysqli->prepare("SELECT notes FROM crm_notes WHERE crm_id=?");
    $stmt->bind_param("i", $selId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($n = $res->fetch_assoc()) $selNotes = $n['notes'];
    $stmt->close();
}

// Compose AI prompt for this row
function compose_prompt(array $r): string {
    // Short BD context line (owner-friendly, not salesy)
    $bd_line = "They’re associated with a Brilliant Directories–powered website (BD: hosted platform for membership/directory sites with profiles, posts, search, and lead routing).";

    $lines = [];
    $lines[] = "Write a friendly, concise outreach email to the contact below.";
    $lines[] = "Goal: start a conversation, reference their website briefly, and propose relevant value. Avoid sounding generic; keep it human.";
    $lines[] = "";
    $lines[] = "Contact:";
    $lines[] = "- Name: ".trim(($r['email_name'] ?? '') ?: 'N/A');
    $lines[] = "- Email: ".trim(($r['email'] ?? '') ?: 'N/A');
    $lines[] = "- Website: ".trim(($r['website_name'] ?? '') ?: 'N/A')." (URL: ".trim(($r['secure_url'] ?: $r['url'] ?: 'N/A')).")";
    $lines[] = "- Country: ".trim(($r['country'] ?? '') ?: 'N/A');
    $lines[] = "- Industry: ".trim(($r['industry'] ?? '') ?: 'N/A');
    $lines[] = "- Profession/Trade: ".trim(($r['profession'] ?? '') ?: (($r['trade'] ?? '') ?: 'N/A'));
    $lines[] = "- Member Type: ".trim(($r['member_type'] ?? '') ?: 'N/A');
    $lines[] = "- Website active: ".trim(($r['website_active'] ?? '') ?: 'N/A')."; Started: ".trim(($r['start_date'] ?? '') ?: 'N/A');
    $lines[] = "";
    $lines[] = "Context:";
    $lines[] = "- $bd_line";
    $lines[] = "- Keep tone: helpful, respectful, specific, 120–180 words.";
    $lines[] = "- Include a single clear CTA (e.g., reply with a time or share the right contact).";
    $lines[] = "";
    $lines[] = "Draft now.";
    return implode("\n", $lines);
}

$prompt = $sel ? compose_prompt($sel) : "Select a contact to generate the email prompt.";

// Tailwind CDN
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Syntrex Mini CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .chip { display:inline-block; padding:0.25rem 0.5rem; border-radius:9999px; font-size:.75rem; font-weight:600; }
    .truncate-2{ display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;}
  </style>
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight">Syntrex Mini CRM</h1>
        <p class="text-sm text-slate-500">Customer list from <span class="font-mono">syntrex.crm</span> • Single-page app (PHP + MySQLi)</p>
      </div>
      <a href="#" onclick="document.getElementById('newForm').classList.toggle('hidden');return false;"
         class="inline-flex items-center rounded-xl bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800">
        + New Contact
      </a>
    </div>

    <!-- Alerts -->
    <?php if(isset($_GET['ok'])): ?>
      <div class="mb-4 rounded-lg bg-green-50 text-green-700 px-4 py-3">
        ✅ <?= e($_GET['ok']) ?>
      </div>
    <?php endif; ?>
    <?php if(isset($_GET['err'])): ?>
      <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-4 py-3">
        ⚠️ <?= e($_GET['err']) ?>
      </div>
    <?php endif; ?>

    <!-- New contact form -->
    <div id="newForm" class="hidden mb-6">
      <form method="post" class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 rounded-2xl shadow">
        <input type="hidden" name="action" value="create" />
        <div>
          <label class="block text-sm mb-1">Email *</label>
          <input name="email" required class="w-full border rounded-xl px-3 py-2" placeholder="owner@example.com" />
        </div>
        <div>
          <label class="block text-sm mb-1">Website Name</label>
          <input name="website_name" class="w-full border rounded-xl px-3 py-2" placeholder="Acme Inc." />
        </div>
        <div>
          <label class="block text-sm mb-1">URL</label>
          <input name="url" class="w-full border rounded-xl px-3 py-2" placeholder="https://example.com" />
        </div>

        <div>
          <label class="block text-sm mb-1">Industry</label>
          <input name="industry" class="w-full border rounded-xl px-3 py-2" placeholder="Healthcare / B2B / ..." />
        </div>
        <div>
          <label class="block text-sm mb-1">Country</label>
          <input name="country" class="w-full border rounded-xl px-3 py-2" placeholder="India" />
        </div>
        <div>
          <label class="block text-sm mb-1">Profession</label>
          <input name="profession" class="w-full border rounded-xl px-3 py-2" placeholder="Dentist / Supplier / ..." />
        </div>

        <div>
          <label class="block text-sm mb-1">Status</label>
          <select name="status" class="w-full border rounded-xl px-3 py-2">
            <?php foreach ($STATUS_MAP as $k=>$v): ?>
              <option value="<?= $k ?>"><?= e($v[0]) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm mb-1">Email Status (free text)</label>
          <input name="email_status" class="w-full border rounded-xl px-3 py-2" placeholder="Not sent / Sent 2025-08-20 / Follow-up due next week" />
        </div>

        <div class="md:col-span-3 text-right">
          <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800">Create</button>
        </div>
      </form>
    </div>

    <!-- Main layout -->
    <div class="flex gap-4">
      <!-- Left: List / Filters (20%) -->
      <aside class="basis-3/10 min-w-[260px] bg-white rounded-2xl shadow p-4 h-[78vh] overflow-auto">
        <form class="space-y-3 mb-4" method="get">
          <div>
            <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search email/name/url/industry"
                   class="w-full border rounded-xl px-3 py-2" />
          </div>
          <div class="grid grid-cols-2 gap-2">
            <select name="status" class="border rounded-xl px-3 py-2">
              <option value="">Status</option>
              <?php foreach ($STATUS_MAP as $k=>$v): ?>
                <option value="<?= $k ?>" <?= ($flt_status!=='' && (int)$flt_status===$k)?'selected':'' ?>><?= e($v[0]) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="country" value="<?= e($flt_country) ?>" placeholder="Country" class="border rounded-xl px-3 py-2" />
          </div>
          <div class="flex gap-2">
            <input type="text" name="industry" value="<?= e($flt_industry) ?>" placeholder="Industry" class="border rounded-xl px-3 py-2 w-full" />
            <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm">Filter</button>
          </div>
        </form>

        <div class="text-xs text-slate-500 mb-2"><?= $total ?> result(s)</div>

        <ul class="space-y-2">
          <?php if(!$rows): ?>
            <li class="text-sm text-slate-500">No contacts found.</li>
          <?php endif; ?>
          <?php foreach ($rows as $r):
            $st = $STATUS_MAP[$r['status']] ?? $STATUS_MAP[0];
          ?>
            <li>
              <a href="?<?= http_build_query(array_merge($_GET, ['id'=>$r['id']])) ?>"
                 class="block border rounded-xl p-3 hover:bg-slate-50 <?= ($selId===$r['id'])?'ring-2 ring-slate-900':'' ?>">
                <div class="flex items-center justify-between">
                  <div class="font-medium text-sm"><?= e($r['website_name'] ?: $r['email']) ?></div>
                  <span class="chip <?= e($st[1]) ?>"><?= e($st[0]) ?></span>
                </div>
                <div class="text-xs text-slate-600 truncate"><?= e($r['email']) ?></div>
                <div class="text-xs text-slate-500 truncate-2"><?= e($r['url'] ?: '-') ?></div>
                <div class="text-[11px] text-slate-500 mt-1"><?= e($r['industry'] ?: '-') ?> · <?= e($r['country'] ?: '-') ?></div>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="mt-4 flex items-center justify-between text-sm">
          <?php $prev = max(1,$page-1); $next = min($totalPages,$page+1);
            $qs = $_GET; $qs['page']=$prev; $prevUrl='?'.http_build_query($qs);
            $qs['page']=$next; $nextUrl='?'.http_build_query($qs);
          ?>
          <a href="<?= e($prevUrl) ?>" class="px-3 py-2 border rounded-xl <?= $page==1?'pointer-events-none opacity-50':'' ?>">Prev</a>
          <div>Page <?= $page ?> / <?= $totalPages ?></div>
          <a href="<?= e($nextUrl) ?>" class="px-3 py-2 border rounded-xl <?= $page==$totalPages?'pointer-events-none opacity-50':'' ?>">Next</a>
        </div>
        <?php endif; ?>
      </aside>

      <!-- Right: Detail (80%) -->
      <main class="basis-7/10 bg-white rounded-2xl shadow p-6 h-[78vh] overflow-auto">
        <?php if(!$sel): ?>
          <div class="text-slate-500">Select a contact to view details and generate the email prompt.</div>
        <?php else:
          $st = $STATUS_MAP[$sel['status']] ?? $STATUS_MAP[0];
        ?>
          <div class="flex items-start justify-between gap-4">
            <div>
              <h2 class="text-xl font-semibold">
                <?= e($sel['website_name'] ?: ($sel['email_name'] ?: $sel['email'])) ?>
              </h2>
              <div class="mt-1 text-sm text-slate-600 space-x-2">
                <span class="chip <?= e($st[1]) ?>"><?= e($st[0]) ?></span>
                <?php if($sel['email_status']): ?><span class="chip bg-slate-100 text-slate-700"><?= e($sel['email_status']) ?></span><?php endif; ?>
              </div>
            </div>
            <div class="text-right">
              <?php if($sel['url'] || $sel['secure_url']): ?>
                <a target="_blank" href="<?= e($sel['secure_url'] ?: $sel['url']) ?>"
                   class="inline-flex items-center rounded-xl border px-3 py-2 text-sm hover:bg-slate-50">Open Website</a>
              <?php endif; ?>
            </div>
          </div>

          <!-- Meta grid -->
          <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-slate-50">
              <div class="text-xs text-slate-500">Email</div>
              <div class="font-medium"><?= e($sel['email'] ?: '-') ?></div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50">
              <div class="text-xs text-slate-500">Industry</div>
              <div class="font-medium"><?= e($sel['industry'] ?: '-') ?></div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50">
              <div class="text-xs text-slate-500">Profession / Trade</div>
              <div class="font-medium"><?= e($sel['profession'] ?: ($sel['trade'] ?: '-')) ?></div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50">
              <div class="text-xs text-slate-500">Country</div>
              <div class="font-medium"><?= e($sel['country'] ?: '-') ?></div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50">
              <div class="text-xs text-slate-500">Member Type</div>
              <div class="font-medium"><?= e($sel['member_type'] ?: '-') ?></div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50">
              <div class="text-xs text-slate-500">Facebook</div>
              <div class="font-medium">
                <?php if($sel['facebook_page']): ?>
                  <a class="text-slate-900 underline" target="_blank" href="<?= e($sel['facebook_page']) ?>">Open</a>
                <?php else: ?>-<?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Quick update: status -->
          <form method="post" class="mt-6 flex flex-wrap items-end gap-3 bg-slate-50 p-4 rounded-xl">
            <input type="hidden" name="action" value="update_status" />
            <input type="hidden" name="id" value="<?= (int)$sel['id'] ?>" />
            <div>
              <label class="block text-xs mb-1">Status</label>
              <select name="status" class="border rounded-xl px-3 py-2">
                <?php foreach ($STATUS_MAP as $k=>$v): ?>
                  <option value="<?= $k ?>" <?= ((int)$sel['status']===$k)?'selected':'' ?>><?= e($v[0]) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="flex-1 min-w-[200px]">
              <label class="block text-xs mb-1">Email Status (free text)</label>
              <input name="email_status" value="<?= e($sel['email_status']) ?>"
                     class="w-full border rounded-xl px-3 py-2" placeholder="e.g., Sent 2025-08-20; waiting reply" />
            </div>
            <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800">Save</button>
          </form>

          <!-- Compose Prompt -->
          <div class="mt-6">
            <div class="flex items-center justify-between mb-2">
              <h3 class="font-semibold">AI Draft Prompt</h3>
              <div class="text-xs text-slate-500">Copy and paste into your writing model</div>
            </div>
            <textarea id="promptBox" class="w-full border rounded-xl p-3 h-44"><?= e($prompt) ?></textarea>
            <div class="mt-2 flex items-center gap-2">
              <button onclick="copyPrompt()" class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800">Copy Prompt</button>
              <?php if($sel['email']): ?>
              <a href="mailto:<?= e($sel['email']) ?>?subject=Quick%20introduction&body="
                 class="text-sm underline text-slate-700">Open mailto:</a>
              <?php endif; ?>
            </div>
          </div>

          <!-- Notes -->
          <div class="mt-6">
            <h3 class="font-semibold mb-2">Notes</h3>
            <form method="post">
              <input type="hidden" name="action" value="save_notes" />
              <input type="hidden" name="id" value="<?= (int)$sel['id'] ?>" />
              <textarea name="notes" class="w-full border rounded-xl p-3 h-36" placeholder="Call outcomes, objections, follow-up dates, etc."><?= e($selNotes) ?></textarea>
              <div class="mt-2 text-right">
                <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800">Save Notes</button>
              </div>
            </form>
          </div>

          <!-- Edit full record (optional) -->
          <details class="mt-8">
            <summary class="cursor-pointer text-sm text-slate-600">Edit full record</summary>
            <form method="post" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 border rounded-2xl">
              <input type="hidden" name="action" value="update" />
              <input type="hidden" name="id" value="<?= (int)$sel['id'] ?>" />
              <?php
                // Helper to render input quickly
                function renderInput($label,$name,$val,$ph=''){ echo '
                <div><label class="block text-xs mb-1">'.e($label).'</label>
                <input name="'.e($name).'" value="'.e($val).'" placeholder="'.e($ph).'" class="w-full border rounded-xl px-3 py-2"/></div>'; }
              ?>
              <?php
                renderInput('Website ID','website_id',$sel['website_id'],'');
                renderInput('URL','url',$sel['url'],'https://...');
                renderInput('Secure URL','secure_url',$sel['secure_url'],'https://...');
                renderInput('Email','email',$sel['email'],'owner@example.com');
                renderInput('Email Name','email_name',$sel['email_name'],'');
                renderInput('Website Name','website_name',$sel['website_name'],'Acme Inc.');
                renderInput('Facebook Page','facebook_page',$sel['facebook_page'],'https://facebook.com/...');                
                renderInput('Website Phone','website_phone',$sel['website_phone'],'+91...');
                renderInput('Email From','email_from',$sel['email_from'],'you@yourdomain.com');
                renderInput('Website Active','website_active',$sel['website_active'],'yes/no/unknown');
                renderInput('Start Date','start_date',$sel['start_date'],'YYYY-MM-DD');
                renderInput('Country','country',$sel['country'],'India');
                renderInput('Profession','profession',$sel['profession'],'Dentist...');
                renderInput('Industry','industry',$sel['industry'],'Healthcare...');
                renderInput('Trade','trade',$sel['trade'],'');
                renderInput('Member Type','member_type',$sel['member_type'],'Free/Paid/Claim...');
                renderInput('Currency Code','currency_code',$sel['currency_code'],'INR/USD');
              ?>
              <div>
                <label class="block text-xs mb-1">Status</label>
                <select name="status" class="w-full border rounded-xl px-3 py-2">
                  <?php foreach ($STATUS_MAP as $k=>$v): ?>
                    <option value="<?= $k ?>" <?= ((int)$sel['status']===$k)?'selected':'' ?>><?= e($v[0]) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="md:col-span-2">
                <label class="block text-xs mb-1">Email Status</label>
                <input name="email_status" value="<?= e($sel['email_status']) ?>" class="w-full border rounded-xl px-3 py-2" />
              </div>
              <div class="md:col-span-3 text-right">
                <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800">Save Changes</button>
              </div>
            </form>
          </details>
        <?php endif; ?>
      </main>
    </div>
  </div>

  <script>
    function copyPrompt(){
      const el = document.getElementById('promptBox');
      el.select(); el.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(el.value).then(()=>{
        const btns = document.querySelectorAll('button');
        alert('Prompt copied');
      });
    }
  </script>
</body>
</html>
