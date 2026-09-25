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

    <title>Attendee Management</title>

</head>

<body>

    <h1>Attendee Management</h1>

    <form method="GET" action="">

        <input
            type="text"
            name="search"
            placeholder="Search name or email..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">
            Search
        </button>

        <?php if ($search !== ''): ?>

            <a href="attendees.php">
                Clear
            </a>

        <?php endif; ?>

    </form>

    <br>

    <a href="create-attendee.php">
        Add New Attendee
    </a>

    <br><br>

    <?php if (isset($error)): ?>

        <p>
            <?php echo htmlspecialchars($error); ?>
        </p>

    <?php elseif (empty($attendees)): ?>

        <p>
            No attendees found.
        </p>

    <?php else: ?>

        <table border="1" cellpadding="8">

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
                            <?php echo $attendee['id']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($attendee['first_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($attendee['last_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($attendee['email']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($attendee['phone'] ?? ''); ?>
                        </td>

                        <td>

                            <a href="update-attendee.php?id=<?php echo $attendee['id']; ?>">
                                Edit
                            </a>

                            |

                            <a href="delete-attendee.php?id=<?php echo $attendee['id']; ?>">
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</body>

</html>