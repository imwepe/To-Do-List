<?php

$con = mysqli_connect("localhost", "root", "", "tasktracker");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['add_task'])) {
    $title = $_POST['title'];
    $defaultStatus = "Not yet started";

    if (!empty($title)) {
      $stmt = $con->prepare("INSERT INTO todos(title, status) VALUES(?, ?)");
      $stmt->bind_param("ss", $title, $defaultStatus);
      $res = $stmt->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
  }
}

if (isset($_POST['delete_task'])) {
  $taskId = $_POST['task_id'];
  if (!empty($taskId)) {
      // Assuming $con is your database connection object
      $del = $con->prepare("DELETE FROM todos WHERE id = ?");
      if ($del) {
          $del->bind_param('i', $taskId);
          if ($del->execute()) {
              header("Location: " . $_SERVER['PHP_SELF']);
              die();
          } else {
              echo "Error executing the deletion query: " . $con->error;
          }
          $del->close();
      } else {
          echo "Error preparing the deletion query: " . $con->error;
      }
  }

  header("Location: " . $_SERVER['PHP_SELF']);
}


  if (isset($_POST['update_task'])) {
    $taskId = $_POST['task_id'];
    $newStatus = $_POST['task_status'];

    if (!empty($taskId)) {
      $updateStatus = $con->prepare("UPDATE todos SET status = ? WHERE id = ?");
      $updateStatus->bind_param('si', $newStatus, $taskId);
      $updateStatus->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    die();
  }

  if (isset($_POST['update_title'])) {
    $taskId = $_POST['task_id'];
    $newTitle = $_POST['new_title'];

    if (!empty($taskId) && !empty($newTitle)) {
      $updateTitle = $con->prepare("UPDATE todos SET title = ? WHERE id = ?");
      $updateTitle->bind_param('si', $newTitle, $taskId);
      $updateTitle->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    die();
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Task Tracker</title>
  <link rel="stylesheet" href="app.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  <div class="wrapper">
    <div class="main-section">

    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
    <h1 style="margin: 0;"><i class='bx bxs-edit-alt'></i>To Do List</h1>
    <a href="login.php">
      <h1 style="text-align: end; margin: 0;">Login</h1>
    </a>
    </div>


      <div class="add-section">
        <form action="taskTracker.php" method="POST" autocomplete="off">
          <input type="text" name="title" placeholder="Enter Task Name..." />
          <button class="add1" type="submit" name="add_task">Add</button>
        </form>
      </div>

      <div class="show-todo-section">
        <?php
        $todos = $con->query("SELECT * FROM todos ORDER BY CASE WHEN status = 'Done' THEN 1 ELSE 0 END, id DESC");

        while ($todo = $todos->fetch_assoc()) {
          $taskId = $todo['id'];
          $status = $todo['status'];
          $title = $todo['title'];
          ?>

          <div class="todo-item">
            <form action="taskTracker.php" method="POST">
              <h2>
                <input type="text" name="new_title" value="<?php echo $title; ?>">
              </h2>
              <div class="status">
                <?php echo "<p>Progress: $status</p>"; ?>
              </div>
              <select class="status-select" name="task_status">
                <option value="Not yet started" <?php echo ($status === 'Not yet started') ? 'selected' : ''; ?>>
                  Not yet started
                </option>
                <option value="In progress" <?php echo ($status === 'In progress') ? 'selected' : ''; ?>>
                  In progress
                </option>
                <option value="Waiting on" <?php echo ($status === 'Waiting on') ? 'selected' : ''; ?>>
                  Waiting on
                </option>
                <option value="Done" <?php echo ($status === 'Done') ? 'selected' : ''; ?>>
                  Done
                </option>
              </select>
              <br>
              <input type="hidden" name="task_id" value="<?php echo $taskId; ?>">
              <button type="submit" class="taskclick" name="update_task">Update Status</button>
              <button type="submit" class="taskclick" name="delete_task">Delete</button>
              <button type="submit" class="taskclick" name="update_title">Update Task</button>

            </form>
            <br>

            <small>created at
              <?php echo $todo['date_time'] ?>
            </small>

          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</body>

</html>