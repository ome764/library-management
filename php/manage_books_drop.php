<?php
// php/manage_books_drop.php
// Inspect and optionally remove foreign-key constraints referencing `books`.
// Usage (local dev): open in browser: http://localhost/library-management/php/manage_books_drop.php
// WARNING: Destructive actions require adding `&confirm=1` to the URL and are irreversible unless you have a backup.

require_once __DIR__ . '/db.php';
$dbName = $db ?? null;
if (!$dbName) $dbName = (isset($GLOBALS['db']) ? $GLOBALS['db'] : null);
header('Content-Type: text/html; charset=utf-8');
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$action = $_GET['action'] ?? '';
$confirm = isset($_GET['confirm']) && $_GET['confirm'] == '1';
$messages = [];

try {
    // Find constraints referencing books
    $sql = "SELECT TABLE_SCHEMA, TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_NAME = 'books' AND REFERENCED_TABLE_SCHEMA = :schema";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':schema'=>$dbName]);
    $refs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle actions
    if ($action === 'drop_fk' && $confirm) {
        $table = $_GET['table'] ?? '';
        $constraint = $_GET['constraint'] ?? '';
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $constraint)) {
            $messages[] = "Invalid table or constraint name.";
        } else {
            $pdo->exec("ALTER TABLE `" . $table . "` DROP FOREIGN KEY `" . $constraint . "`");
            $messages[] = "Dropped foreign key `" . h($constraint) . "` on table `" . h($table) . "`.";
            // refresh refs
            $stmt->execute([':schema'=>$dbName]); $refs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    if ($action === 'drop_child_table' && $confirm) {
        $table = $_GET['table'] ?? '';
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
            $messages[] = "Invalid table name.";
        } else {
            $pdo->exec("DROP TABLE IF EXISTS `" . $table . "`");
            $messages[] = "Dropped child table `" . h($table) . "`.";
            $stmt->execute([':schema'=>$dbName]); $refs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    if ($action === 'drop_parent_disable_fks' && $confirm) {
        // WARNING: this will drop books while foreign key checks are disabled — may leave orphaned data
        $pdo->beginTransaction();
        try {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            $pdo->exec('DROP TABLE IF EXISTS `books`');
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            $pdo->commit();
            $messages[] = 'Dropped `books` table with FOREIGN_KEY_CHECKS disabled.';
            // refresh refs
            $stmt->execute([':schema'=>$dbName]); $refs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $pdo->rollBack();
            $messages[] = 'Error: ' . $e->getMessage();
        }
    }

} catch (Exception $e) {
    echo "<h2>Error connecting or querying information schema</h2><pre>" . h($e->getMessage()) . "</pre>";
    exit;
}

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manage DROP `books` — Inspector</title>
  <link rel="stylesheet" href="/library-management/assets/css/style.css">
  <style>body{padding:20px;background:transparent;color:var(--text-light)} .ok{color:#b7f0b7} .warn{color:#ffd1a8}</style>
</head>
<body class="bg-epic-main">
  <main class="container">
    <h1>Inspect foreign keys referencing `books`</h1>
    <p class="note">Database: <strong><?php echo h($dbName); ?></strong></p>

    <?php if($messages): ?>
      <div class="card">
        <?php foreach($messages as $m): ?>
          <div><?php echo h($m); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if(empty($refs)): ?>
      <div class="card">
        <h3 class="ok">No foreign-key constraints reference `books`.</h3>
        <p>You can safely DROP TABLE `books` now (e.g. run the SQL file).</p>
        <p><a class="btn primary" href="?action=drop_parent_disable_fks&confirm=1">Drop `books` now (force)</a> — <span class="warn">This disables FK checks temporarily.</span></p>
      </div>
    <?php else: ?>
      <div class="card">
        <h3>Found <?php echo count($refs); ?> referencing constraint(s):</h3>
        <table style="width:100%;border-collapse:collapse;color:var(--text-light)">
          <thead><tr><th>Child Table</th><th>Constraint</th><th>Column</th><th>Referenced Column</th><th>Actions</th></tr></thead>
          <tbody>
          <?php foreach($refs as $r): ?>
            <tr>
              <td><?php echo h($r['TABLE_NAME']); ?></td>
              <td><?php echo h($r['CONSTRAINT_NAME']); ?></td>
              <td><?php echo h($r['COLUMN_NAME']); ?></td>
              <td><?php echo h($r['REFERENCED_COLUMN_NAME']); ?></td>
              <td>
                <a class="btn" href="?action=drop_fk&table=<?php echo urlencode($r['TABLE_NAME']); ?>&constraint=<?php echo urlencode($r['CONSTRAINT_NAME']); ?>&confirm=1">Drop FK</a>
                <a class="btn ghost" href="?action=drop_child_table&table=<?php echo urlencode($r['TABLE_NAME']); ?>&confirm=1">Drop child table</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <hr>
        <p class="warn">If you are sure and have a backup, you can force-drop `books` by disabling FK checks:</p>
        <p><a class="btn primary" href="?action=drop_parent_disable_fks&confirm=1">Disable FK checks and drop `books`</a></p>
      </div>
    <?php endif; ?>

    <section style="margin-top:18px" class="card">
      <h4>Manual SQL (read-only):</h4>
      <pre>-- find referencing constraints
SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_NAME = 'books' AND REFERENCED_TABLE_SCHEMA = '<?php echo h($dbName); ?>';

-- remove a foreign key on a child table
ALTER TABLE child_table DROP FOREIGN KEY fk_name;

-- or drop child table
DROP TABLE child_table;

-- Force drop parent (NOT recommended without backup)
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS books;
SET FOREIGN_KEY_CHECKS=1;
</pre>
      <p class="note">Backup recommendation: use <code>mysqldump</code> to export DB before destructive actions.</p>
    </section>
  </main>
</body>
</html>
