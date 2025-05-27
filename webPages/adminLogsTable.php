<?php
session_start();
require_once('../config.php');

$sql = "SELECT admin_logs.*, users.username AS admin_username, items.title AS target_item_name FROM admin_logs
JOIN users ON admin_logs.admin_id = users.user_id
LEFT JOIN items ON admin_logs.target_item_id = items.item_id";

$result = $conn->query($sql);
$adminLogs = [];
if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $adminLogs[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found Admin Logs</title>
    <link rel="stylesheet" href="../allCss/adminLogsTable.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/jpg" href="../stylingPhotos/icons/lostAndFound_icon.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <form action="adminMenu.php" method="post">
        <button>Go Back</button>
    </form>
    <div>
        <h1>Admin Logs</h1>
            <?php if(empty($adminLogs)):  ?>
                No Admin Logs
            <?php  else: ?>
            <table border="1">
                <tr>
                    <th>Admin Name</th>
                    <th>Action</th>
                    <th>Announcements</th>
                    <th>Target Item</th>
                    <th>Date and Time</th>
                </tr>
                <?php foreach($adminLogs AS $adminLog): ?>
                    <tr>
                        <td> <?=  htmlspecialchars($adminLog['admin_username']) ?> </td>
                        <td> <?=  htmlspecialchars($adminLog['action']) ?> </td>
                        <?php  if(!empty($adminLog['announcements'])): ?>
                            <td > <?=  htmlspecialchars($adminLog['announcements']) ?> </td>
                        <?php  else: ?>
                            <td class="announced"> No announcement made </td>
                        <?php  endif; ?>
                        <?php  if(!empty($adminLog['target_item_name'])): ?>
                            <td > <?= htmlspecialchars($adminLog['target_item_name']) ?> </td>
                        <?php  else: ?>
                            <td class="targettedItem"> No Targetted Item </td>
                        <?php  endif; ?>
                        <td> <?=  htmlspecialchars($adminLog['done_at']) ?> </td>
                    </tr>
                <?php  endforeach; ?>
            </table>
        <?php  endif; ?>
    </div>
</body>
</html>