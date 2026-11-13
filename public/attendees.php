
<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../classes/Attendee.php';

$search = trim($_GET['search'] ?? '');
$attendees = [];
$totalAttendees = 0;
$totalPages = 1;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 5;
$offset = 0;
$error = null;

try {

    $attendeeModel = new Attendee();

    // Count filtered attendees
    $totalAttendees = $attendeeModel->countAllAttendees($search);

    // Calculate pagination
    $totalPages = max(1, (int) ceil($totalAttendees / $perPage));
    $currentPage = min($currentPage, $totalPages);
    $offset = ($currentPage - 1) * $perPage;

    // Retrieve attendees
    $attendees = $attendeeModel->getAllAttendees(
        $search,
        $perPage,
        $offset
    );

} catch (Exception $e) {

    $error = $e->getMessage();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css">

    <title>Attendee Management | EventlyPH</title>
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container attendees-page">

    <!-- PAGE HEADER -->
    <section class="attendees-heading">

        <div>
            <span class="attendees-eyebrow">USER MANAGEMENT</span>
            <h1>Attendee Management</h1>
            <p>View, search, and manage registered attendees.</p>
        </div>

        <a href="../admin/create-attendee.php" class="attendees-add-button">
            + Add New Attendee
        </a>

    </section>

    <!-- SUMMARY -->
    <section class="attendees-summary">

        <div class="attendees-summary-icon">👥</div>

        <div>
            <span>Total Registered Attendees</span>
            <h2><?= number_format($totalAttendees) ?></h2>
        </div>

    </section>

    <!-- SEARCH -->
    <section class="attendees-content-card">

        <div class="attendees-card-heading">
            <div>
                <h2>Attendee Records</h2>
                <p>Search and manage attendee information.</p>
            </div>
        </div>

        <form method="GET" action="" class="attendees-search-form">

            <div class="attendees-search-input">
                <span>⌕</span>
                <input
                    type="text"
                    name="search"
                    placeholder="Search by name or email..."
                    value="<?= htmlspecialchars($search) ?>"
                >
            </div>

            <button type="submit" class="attendees-search-button">
                Search
            </button>

            <?php if ($search !== ''): ?>
                <a href="attendees.php" class="attendees-clear-button">
                    Clear
                </a>
            <?php endif; ?>

        </form>

        <!-- ERROR -->
        <?php if ($error !== null): ?>

            <div class="attendees-error">
                <?= htmlspecialchars($error) ?>
            </div>

        <!-- EMPTY STATE -->
        <?php elseif (empty($attendees)): ?>

            <div class="attendees-empty">
                <div>👤</div>
                <h3>No attendees found</h3>

                <p>
                    <?php if ($search !== ''): ?>
                        Try using a different search term.
                    <?php else: ?>
                        There are currently no registered attendees.
                    <?php endif; ?>
                </p>
            </div>

        <?php else: ?>

            <!-- ATTENDEE TABLE -->
            <div class="attendees-table-wrapper">

                <table class="attendees-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email Address</th>
                            <th>Phone Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($attendees as $attendee): ?>

                            <tr>

                                <td>
                                    <span class="attendees-id">
                                        #<?= (int) $attendee['id'] ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($attendee['first_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($attendee['last_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($attendee['email']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($attendee['phone'] ?? 'N/A') ?>
                                </td>

                                <td>
                                    <div class="attendees-actions">

                                        <a
                                            href="../admin/update-attendee.php?id=<?= (int) $attendee['id'] ?>"
                                            class="attendees-edit-button"
                                        >
                                            Edit
                                        </a>

                                        <a
                                            href="../admin/delete-attendee.php?id=<?= (int) $attendee['id'] ?>"
                                            class="attendees-delete-button"
                                        >
                                            Delete
                                        </a>

                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

        <!-- PAGINATION INFORMATION -->
        <?php if ($error === null && $totalAttendees > 0): ?>

            <div class="attendees-pagination-footer">

                <div class="attendees-pagination-info">
                    Showing
                    <strong><?= $offset + 1 ?></strong>
                    to
                    <strong><?= min($offset + $perPage, $totalAttendees) ?></strong>
                    of
                    <strong><?= $totalAttendees ?></strong>
                    attendees
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>

                    <div class="attendees-pagination">

                        <?php
                        $queryParams = [];

                        if ($search !== '') {
                            $queryParams['search'] = $search;
                        }
                        ?>

                        <?php if ($currentPage > 1): ?>

                            <?php
                            $queryParams['page'] = $currentPage - 1;
                            ?>

                            <a href="?<?= htmlspecialchars(http_build_query($queryParams)) ?>">
                                Previous
                            </a>

                        <?php endif; ?>

                        <?php for ($page = 1; $page <= $totalPages; $page++): ?>

                            <?php
                            $queryParams['page'] = $page;
                            ?>

                            <a
                                href="?<?= htmlspecialchars(http_build_query($queryParams)) ?>"
                                class="<?= $page === $currentPage ? 'active' : '' ?>"
                            >
                                <?= $page ?>
                            </a>

                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>

                            <?php
                            $queryParams['page'] = $currentPage + 1;
                            ?>

                            <a href="?<?= htmlspecialchars(http_build_query($queryParams)) ?>">
                                Next
                            </a>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>
