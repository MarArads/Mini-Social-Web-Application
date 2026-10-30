<?php

$h = function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$when = function ($value) {
    return $value ? date('M j, Y g:i A', strtotime($value)) : '-';
};
$preview = function ($text, $length = 70) use ($h) {
    $text = trim(preg_replace('/\s+/', ' ', (string) $text));
    return $h(mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text);
};
$maxPerDay = 1;
foreach ($postsPerDay as $day) {
    $maxPerDay = max($maxPerDay, (int) $day['post_count']);
}
$exportQuery = http_build_query(array_filter([
    'from' => $filters['from'],
    'to' => $filters['to'],
    'author' => $filters['author'],
    'keyword' => $filters['keyword'],
    'min_likes' => $filters['min_likes'] ?: null,
    'only_photos' => $filters['only_photos'] ? '1' : null,
    'sort' => $sort,
], function ($v) { return $v !== '' && $v !== null; }));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URED | Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="layout/base.css">
    <link rel="stylesheet" href="layout/app.css">
    <link rel="stylesheet" href="layout/reports.css">
</head>
<body>
    <?php include __DIR__ . '/layout/navigation.php'; ?>

    <main class="page report-page">
        <h1 class="page-title"><i class="bi bi-bar-chart-line"></i> Reports</h1>
        <p class="report-meta">Generated on <?= $h(date('F j, Y g:i A')) ?></p>

        <?php if (!empty($loadError)): ?>
            <div class="alert alert-error" role="alert"><?= $h($loadError) ?></div>
        <?php endif; ?>

        
        <section class="report-section">
            <h2 class="report-heading">Summary report</h2>
            <p class="sql-tag">SQL: scalar subqueries, COUNT, ROUND, MAX, NULLIF</p>
            <div class="report-cards">
                <div class="report-card"><span class="report-number"><?= (int) ($summary['total_users'] ?? 0) ?></span><span class="report-label">Users</span></div>
                <div class="report-card"><span class="report-number"><?= (int) ($summary['total_posts'] ?? 0) ?></span><span class="report-label">Posts</span></div>
                <div class="report-card"><span class="report-number"><?= (int) ($summary['total_comments'] ?? 0) ?></span><span class="report-label">Comments</span></div>
                <div class="report-card"><span class="report-number"><?= (int) ($summary['total_likes'] ?? 0) ?></span><span class="report-label">Likes</span></div>
                <div class="report-card"><span class="report-number"><?= (int) ($summary['posts_with_photo'] ?? 0) ?></span><span class="report-label">Posts with a photo</span></div>
                <div class="report-card"><span class="report-number"><?= $h($summary['avg_posts_per_user'] ?? '0') ?></span><span class="report-label">Avg posts per user</span></div>
                <div class="report-card"><span class="report-number"><?= $h($summary['avg_likes_per_post'] ?? '0') ?></span><span class="report-label">Avg likes per post</span></div>
                <div class="report-card"><span class="report-number"><?= $h($summary['avg_comments_per_post'] ?? '0') ?></span><span class="report-label">Avg comments per post</span></div>
            </div>
            <p class="report-meta">Latest post: <?= $h($when($summary['latest_post_at'] ?? null)) ?></p>
        </section>

        
        <section class="report-section">
            <h2 class="report-heading">User engagement</h2>
            <p class="sql-tag">SQL: correlated subqueries, JOIN inside a subquery, ORDER BY a computed column</p>
            <div class="table-wrap">
                <table class="report-table">
                    <thead><tr><th>#</th><th>User</th><th>Posts</th><th>Comments written</th><th>Likes received</th></tr></thead>
                    <tbody>
                    <?php foreach ($engagement as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $h($row['full_name']) ?> <span class="muted">@<?= $h($row['username']) ?></span></td>
                            <td><?= (int) $row['posts'] ?></td>
                            <td><?= (int) $row['comments_written'] ?></td>
                            <td><?= (int) $row['likes_received'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($engagement)): ?><tr><td colspan="5" class="muted">No data yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        
        <section class="report-section">
            <h2 class="report-heading">Top posters</h2>
            <p class="sql-tag">SQL: LEFT JOIN, GROUP BY, HAVING, COUNT, MAX</p>
            <div class="table-wrap">
                <table class="report-table">
                    <thead><tr><th>#</th><th>User</th><th>Posts</th><th>Last post</th></tr></thead>
                    <tbody>
                    <?php foreach ($topPosters as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $h($row['full_name']) ?> <span class="muted">@<?= $h($row['username']) ?></span></td>
                            <td><?= (int) $row['post_count'] ?></td>
                            <td><?= $h($when($row['last_post_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($topPosters)): ?><tr><td colspan="4" class="muted">Nobody has posted yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        
        <section class="report-section">
            <h2 class="report-heading">Most liked posts</h2>
            <p class="sql-tag">SQL: JOIN + LEFT JOIN, GROUP BY, COUNT, ORDER BY, LIMIT</p>
            <div class="table-wrap">
                <table class="report-table">
                    <thead><tr><th>#</th><th>Post</th><th>Author</th><th>Likes</th></tr></thead>
                    <tbody>
                    <?php foreach ($mostLiked as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $preview($row['content']) ?></td>
                            <td>@<?= $h($row['username']) ?></td>
                            <td><?= (int) $row['like_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($mostLiked)): ?><tr><td colspan="4" class="muted">No posts yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        
        <section class="report-section">
            <h2 class="report-heading">Posts with above-average likes</h2>
            <p class="sql-tag">SQL: subquery in HAVING, derived table (subquery in FROM), AVG</p>
            <div class="table-wrap">
                <table class="report-table">
                    <thead><tr><th>Post</th><th>Author</th><th>Likes</th></tr></thead>
                    <tbody>
                    <?php foreach ($aboveAverage as $row): ?>
                        <tr>
                            <td><?= $preview($row['content']) ?></td>
                            <td>@<?= $h($row['username']) ?></td>
                            <td><?= (int) $row['like_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($aboveAverage)): ?><tr><td colspan="3" class="muted">No post is above the average yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        
        <section class="report-section">
            <h2 class="report-heading">Posts per day (latest 7 days with posts)</h2>
            <p class="sql-tag">SQL: GROUP BY a date function, COUNT, ORDER BY, LIMIT</p>
            <div class="table-wrap">
                <table class="report-table">
                    <thead><tr><th>Day</th><th>Posts</th><th class="bar-col"></th></tr></thead>
                    <tbody>
                    <?php foreach ($postsPerDay as $row): ?>
                        <tr>
                            <td><?= $h(date('M j, Y', strtotime($row['post_day']))) ?></td>
                            <td><?= (int) $row['post_count'] ?></td>
                            <td class="bar-col"><div class="report-bar" style="width: <?= (int) round(((int) $row['post_count'] / $maxPerDay) * 100) ?>%"></div></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($postsPerDay)): ?><tr><td colspan="3" class="muted">No posts yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        
        <section class="report-section">
            <h2 class="report-heading">Users by activity</h2>
            <p class="sql-tag">SQL: CASE, derived table, LEFT JOIN, GROUP BY</p>
            <div class="table-wrap">
                <table class="report-table">
                    <thead><tr><th>Group</th><th>Users</th></tr></thead>
                    <tbody>
                    <?php foreach ($segments as $row): ?>
                        <tr><td><?= $h($row['segment']) ?></td><td><?= (int) $row['user_count'] ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (empty($segments)): ?><tr><td colspan="2" class="muted">No users yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

      
        <section class="report-section">
            <h2 class="report-heading">Users who have not posted yet</h2>
            <p class="sql-tag">SQL: NOT EXISTS (correlated subquery)</p>
            <div class="table-wrap">
                <table class="report-table">
                    <thead><tr><th>User</th><th>Joined</th></tr></thead>
                    <tbody>
                    <?php foreach ($noPosts as $row): ?>
                        <tr>
                            <td><?= $h($row['full_name']) ?> <span class="muted">@<?= $h($row['username']) ?></span></td>
                            <td><?= $h($when($row['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($noPosts)): ?><tr><td colspan="2" class="muted">Everyone has posted at least once.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        
        <section class="report-section">
            <h2 class="report-heading">Detailed report: all posts</h2>
            <p class="sql-tag">SQL: JOIN, correlated subqueries, derived table, LIKE, prepared-statement filters, whitelisted ORDER BY</p>

            <?php if (!empty($filterProblem)): ?>
                <div class="alert alert-error" role="alert"><?= $h($filterProblem) ?></div>
            <?php endif; ?>

            <form class="post-form report-filters" method="GET" action="reports.php">
                <div class="filter-grid">
                    <label>From date <input class="form-input" type="date" name="from" value="<?= $h($filters['from']) ?>"></label>
                    <label>To date <input class="form-input" type="date" name="to" value="<?= $h($filters['to']) ?>"></label>
                    <label>Author <input class="form-input" type="text" name="author" maxlength="50" placeholder="Name or username" value="<?= $h($filters['author']) ?>"></label>
                    <label>Keyword in post <input class="form-input" type="text" name="keyword" maxlength="100" placeholder="Search the text" value="<?= $h($filters['keyword']) ?>"></label>
                    <label>Minimum likes <input class="form-input" type="number" name="min_likes" min="0" max="1000" value="<?= (int) $filters['min_likes'] ?>"></label>
                    <label>Sort by
                        <select class="form-input" name="sort">
                            <option value="newest"   <?= $sort === 'newest' ? 'selected' : '' ?>>Newest first</option>
                            <option value="oldest"   <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest first</option>
                            <option value="likes"    <?= $sort === 'likes' ? 'selected' : '' ?>>Most likes</option>
                            <option value="comments" <?= $sort === 'comments' ? 'selected' : '' ?>>Most comments</option>
                        </select>
                    </label>
                </div>
                <label class="check-row"><input type="checkbox" name="only_photos" value="1" <?= $filters['only_photos'] ? 'checked' : '' ?>> Only posts with a photo</label>
                <div class="form-actions">
                    <a href="reports.php" class="btn btn-ghost">Clear</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Apply filters</button>
                </div>
            </form>

            <div class="report-actions">
                <a href="report_export.php?<?= $h($exportQuery) ?>" class="btn btn-ghost btn-small"><i class="bi bi-download"></i> Download CSV</a>
                <button type="button" class="btn btn-ghost btn-small" onclick="window.print()"><i class="bi bi-printer"></i> Print / Save as PDF</button>
            </div>

            <p class="report-meta"><?= count($detailed) ?> post<?= count($detailed) === 1 ? '' : 's' ?> shown<?= count($detailed) === 100 ? ' (the first 100; the CSV has them all)' : '' ?></p>
            <div class="table-wrap">
                <table class="report-table">
                    <thead><tr><th>Date</th><th>Author</th><th>Post</th><th>Photo</th><th>Likes</th><th>Comments</th></tr></thead>
                    <tbody>
                    <?php foreach ($detailed as $row): ?>
                        <tr>
                            <td><?= $h($when($row['created_at'])) ?></td>
                            <td><?= $h($row['full_name']) ?> <span class="muted">@<?= $h($row['username']) ?></span></td>
                            <td><?= $preview($row['content'], 90) ?></td>
                            <td><?= $row['has_photo'] ? 'Yes' : 'No' ?></td>
                            <td><?= (int) $row['like_count'] ?></td>
                            <td><?= (int) $row['comment_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($detailed)): ?><tr><td colspan="6" class="muted">No posts match these filters.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/layout/foot.php'; ?>
</body>
</html>