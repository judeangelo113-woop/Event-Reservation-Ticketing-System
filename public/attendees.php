<?php

require_once __DIR__ . '/../classes/Attendee.php';


try {

    $attendeeModel = new Attendee();

    $search = trim($_GET['search'] ?? '');

    $attendees = $attendeeModel->getAllAttendees($search);

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

    <title>Attendee Management</title>

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

    <div class="page-header">

        <div>
            <h1>Attendee Management</h1>

            <p class="page-description">
                Manage attendee information and registration records.
            </p>
        </div>

        <a href="../admin/create-attendee.php" class="btn btn-primary">
    +           Add New Attendee
        </a>

    </div>

    <form method="GET" action="" class="filter-form">

        <input
            type="text"
            name="search"
            placeholder="Search name or email..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <button type="submit">
            Search
        </button>

        <?php if ($search !== ''): ?>

            <a href="attendees.php" class="clear-filter">
                Clear
            </a>

        <?php endif; ?>

    </form>

    <br>

    <?php if (isset($error)): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php elseif (empty($attendees)): ?>

        <p>No attendees found.</p>

    <?php else: ?>

        <div class="table-container">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($attendees as $attendee): ?>

                        <tr>

                            <td>
                                <?= (int) $attendee['id'] ?>
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
                                <?= htmlspecialchars($attendee['phone'] ?? '') ?>
                            </td>

                            <td>

                                <a
                                    href="../admin/update-attendee.php?id=<?= (int) $attendee['id'] ?>"
                                    class="btn btn-secondary"
                                >
                                    Edit
                                </a>

                                <a
                                    href="../admin/delete-attendee.php?id=<?= (int) $attendee['id'] ?>"
                                    class="btn btn-danger"
                                >
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

<script src="../assets/js/navigation.js"></script>

</body>

</html>