<!--<?php
session_start();

require_once('../config.php');

$userId = $_SESSION['user_id'];
//this is for current users
$sql = "SELECT * FROM users WHERE user_id = $userId";
$result = $conn->query($sql);
$currentUser = [];
if($result && $result->num_rows > 0){
    if($row = $result->fetch_assoc())
    {
        $currentUser[] = $row;
    }
}

$sql = "SELECT * FROM users";
$result = $conn->query($sql);
$allUsers = [];
//this for deleting a users
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $allUsers[] = $row;
    }
}

//for dashboard
//for pie chart
$sql = "SELECT location, COUNT(item_id) AS count FROM items GROUP BY location";
$result = $conn->query($sql);

$locations = [];
$counts = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row['location'];
        $counts[] = (int)$row['count'];
    }
}

//for total number of users
$sql = "SELECT COUNT(user_id) AS num_users FROM users";
$result = $conn->query($sql);
$totalUsers = 0;
if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $totalUsers = $row['num_users'];
    }
}

//for total number of approved claims
$sql = "SELECT COUNT(claim_id) AS num_approved FROM claims
WHERE status = 'approved'";
$result = $conn->query($sql);
$totalApproved = 0;
if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $totalApproved = $row['num_approved'];
    }
}

//for total number of rejected claims
$sql = "SELECT COUNT(claim_id) AS num_rejected FROM claims
WHERE status = 'rejected'";
$result = $conn->query($sql);
$totalRejected = 0;
if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $totalRejected = $row['num_rejected'];
    }
}

//for total number of claim requests
$sql = "SELECT COUNT(claim_id) AS num_claims FROM claims";
$result = $conn->query($sql);
$totalClaims = 0;
if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $totalClaims = $row['num_claims'];
    }
}

//for average number of claims per week
$sql = "SELECT ROUND(AVG(claims_per_week)) AS average_claims_per_week_continuous FROM
(SELECT COUNT(claim_id) AS claims_per_week FROM claims
WHERE request_date >= CURDATE() - INTERVAL 52 WEEK
 AND request_date < CURDATE() - INTERVAL (WEEKDAY(CURDATE()) + 1) DAY
 GROUP BY YEAR(request_date), WEEK(request_date, 3)
 ) AS weekly_claim_counts";
 $result = $conn->query($sql);
 $averageClaims = 0;
 if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $averageClaims = $row['average_claims_per_week_continuous'];
    }
}

//for total number of items
$sql = "SELECT COUNT(item_id) AS num_items FROM items";
$result = $conn->query($sql);
$totalItems = 0;
if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $totalItems = $row['num_items'];
    }
}

//for average number of added items per week
$sql = "SELECT ROUND(AVG(items_per_week)) AS average_itemms_per_week_continuous FROM
(SELECT COUNT(item_id) AS items_per_week FROM items
WHERE date_reported >= CURDATE() - INTERVAL 52 WEEK
 AND date_reported < CURDATE() - INTERVAL (WEEKDAY(CURDATE()) + 1) DAY
 GROUP BY YEAR(date_reported), WEEK(date_reported, 3)
 ) AS weekly_item_counts";
 $result = $conn->query($sql);
 $averageItems = 0;
 if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $averageItems = $row['average_itemms_per_week_continuous'];
    }
}

//this one is for showing all users who aplied to become admin
$sql = "SELECT * FROM admin_request
JOIN users ON admin_request.user_id = users.user_id";
$result = $conn->query($sql);
$allUserUsers = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $allUserUsers[] = $row;
    }
}

//newest admin log
$sql = "SELECT users.username AS admin_username, action, done_at, announcements FROM admin_logs
JOIN users ON admin_logs.admin_id = users.user_id
WHERE done_at = (SELECT MAX(done_at) FROM admin_logs)";
$result = $conn->query($sql);
if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $newest_admin_username = $row['admin_username'];
        $newest_announcement = $row['announcements'];   
        $newest_action = $row['action'];
        $newest_datetime = $row['done_at'];
    }
}

//the items of the specific user
$sql = "SELECT *, users.username FROM items
    JOIN users ON items.user_id = users.user_id
    WHERE items.user_id = $userId";
$result = $conn->query($sql);
$userItems = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userItems[] = $row;
    }
}

//all claims
$sql = "SELECT *, items.title AS item_title, claims.status AS claim_status FROM claims 
        JOIN items ON claims.item_id = items.item_id 
        WHERE claims.status = 'approved' OR claims.status = 'rejected'";
$result = $conn->query($sql);

$claims = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $claims[] = $row;
    }
}

//all messages made by users
$sql = "SELECT *, receivers.username AS receiver_username, senders.username AS sender_username FROM messages
    JOIN users AS receivers ON messages.receiver_id = receivers.user_id
    JOIN users AS senders ON messages.sender_id = senders.user_id
    WHERE sender_id = $userId OR receiver_id = $userId
    ORDER BY sent_at DESC";
$result_for_messages = $conn->query($sql);
$user_messages = [];

if($result_for_messages && $result_for_messages->num_rows > 0)
{
    While($row = $result_for_messages->fetch_assoc())
    {
        $user_messages[] = $row;
    }
}

//all items for searching
$search = '';
if (isset($_POST['clear'])) {
    $items = [];
} else {
    $search = $_POST['search'] ?? '';
    $searchEscaped = $conn->real_escape_string($search);

        $sql = "SELECT *, users.user_id AS items_user_id FROM items
                JOIN users ON items.user_id = users.user_id
                WHERE title LIKE '%$searchEscaped%' 
                OR status LIKE '%$searchEscaped%'
                OR description LIKE '%$searchEscaped%' 
                OR location LIKE '%$searchEscaped%' 
                OR category LIKE '%$searchEscaped%'";

    $result = $conn->query($sql);
    $items = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
}

//all claims for searching
$search_claim = '';
if (isset($_POST['clear_claim'])) {
    $item_claims = [];
} else {
    $search_claim = $_POST['search_claim'] ?? '';
    $searchEscaped = $conn->real_escape_string($search_claim);

        $sql = "SELECT items.*, users.*, claims.status AS claim_status, claims.item_id AS claim_item_id FROM items
        JOIN users ON items.user_id = users.user_id
        JOIN claims ON items.item_id = claims.item_id
        WHERE title LIKE '%$searchEscaped%'
            OR description LIKE '%$searchEscaped%' 
            OR location LIKE '%$searchEscaped%'
            OR category LIKE '%$searchEscaped%'
            OR claims.status LIKE '%$searchEscaped%'";

    $result = $conn->query($sql);
    $item_claims = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $item_claims[] = $row;
        }
    }
}

//all users for searching usernames
$search_user = '';
if (isset($_POST['clear_user'])) {
    $usernames = [];
} else {
    $search_user = $_POST['search_user'] ?? '';
    $searchEscaped = $conn->real_escape_string($search_user);

    $sql = "SELECT * FROM users
            WHERE username LIKE '%$searchEscaped%'";

    $result = $conn->query($sql);
    $usernames = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usernames[] = $row;
        }
    }
}

//for all items listed by category
$catResult = $conn->query("SELECT DISTINCT category FROM items");
$categories = [];
if ($catResult && $catResult->num_rows > 0) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

//for errors
$error = [ 'requesting_error' => $_SESSION['request_error'] ?? ''];

function showError($error)
{
    return !empty($error) ? "<p class='error-message'>$error</p>" : ''; 
}

$conn->close();
?>-->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found Management System</title>
    <link rel="stylesheet" href="../allCss/adminMenu.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/jpg" href="../stylingPhotos/icons/lostAndFound_icon.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="bigCont">
        <div class="menuButton" id="menuButt">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div class="sideBar" id="sideBar">
            <ul>
                <li><a onclick="showElem('perInfo')">Personal Information</a></li>
                <li><a onclick="showElem('changePass')">Change Password</a></li>
                <li><a onclick="showElem('updateInfo')">Update Information</a></li>
                <li><a onclick="showElem('verifyToBeAdmin')">Verify application to be admin</a></li>
                <form action="../processesForBoth/logout.php">
                    <button>Log-out</button>
                </form>
            </ul>
        </div>
        <div class="elemSide" id="perInfo">
            <?php  foreach($currentUser AS $user): ?>
            <ul>
                <li>Full Name: <?= $user['first_name'] ?> <?= $user['first_name'] ?></li> <br>
                <li>Username: <?=  $user['username'] ?></li> <br>
                <li>Email: <?= $user['email'] ?></li> <br>
                <li>Role: <?=  $user['role'] ?>
            </ul>
            <?php endforeach; ?>
        <button class="elemSideButt" onclick="removeElem('perInfo')">Exit</button>
        </div>
        <div class="elemSide" id="changePass">
                <form action="../processesForBoth/changePass.php" method="post">
                    <input type="hidden" name="userid_changepass" value="<?= $_SESSION['user_id'] ?>">
                    <label>Current Password: </label><input type="text" name="current_pass" placeholder="?" required> <br>
                    <label>New Password: </label><input type="text" name="new1_pass" placeholder="??" required> <br>
                    <label>Repeat New Password: </label><input type="text" name="new2_pass" placeholder="???" required> <br>
                    <button type="submit" name="change_pass" class="change_pass_butt">Change Now</button>
                </form>
            <button class="elemSideButt" onclick="removeElem('changePass')">Exit</button>
        </div>
        <div class="elemSide" id="updateInfo">
        <form action="../processesForBoth/updateInfo.php" method="post">
                    Enter only which you want to update. <br>
                    <input type="hidden" name="userid_update" value="<?= $_SESSION['user_id'] ?>">
                    <label>New First Name: </label><input type="text" name="first_name" placeholder="?" value="<?= $currentUser[0]['first_name'] ?>"> <br>
                    <label>New Last Name: </label><input type="text" name="last_name" placeholder="?" value="<?= $currentUser[0]['last_name'] ?>"> <br>
                    <label>New Email: </label><input type="text" name="email" placeholder="?" value="<?= $currentUser[0]['email'] ?>"> <br>
                    <label>New Username: </label><input type="text" name="username" placeholder="?" value="<?= $currentUser[0]['username'] ?>"> <br>
                    <button type="submit" name="updateInfo" class="change_pass_butt">Update Now</button>
                </form>
            <button class="elemSideButt" onclick="removeElem('updateInfo')">Exit</button>
        </div>
        <div id="addItem" class="elemSide">
                <form action="../adminUserProcesses/admin_actions.php" method="post" enctype="multipart/form-data" >
                    <label>Title:</label><input type="text" name="title" required>
                    <label>Description:</label><input type="text" name="description" required>
                    <label>Location:</label><input type="text" name="location" required>
                    <label>Category:</label><input type="text" name="category" required>
                    <label>Image:</label><input type="file" name="image" accept="image/*">
                    <button type="submit" name="addItem" class="addItem_butt">Submit Item</button>
                </form>
            <button class="elemSideButt" onclick="removeElem('addItem')">Exit</button>
        </div>
        <?php if(isset($_POST['update_item_id'])):?>
        <div id="updateItem" class="elemSide active">
            <form action="../adminUserProcesses/admin_actions.php" method="post">
                <input type="hidden" name="update_item_id" value="<?= $_POST['update_item_id'] ?>">
                <input type="hidden" name="status" value="unclaimed">
                <label>Title:</label><input type="text" name="title" value="<?= $_POST['title'] ?>" ><br>
                <label>Description:</label><input type="text" name="description" value="<?= $_POST['description'] ?>" ><br>
                <label>Location:</label><input type="text" name="location" value="<?= $_POST['location'] ?>"><br>
                <label>Category:</label><input type="text" name="category" value="<?= $_POST['category'] ?>" ><br>
                <label>Image:</label><input type="file" name="image" accept="image/*"><br>
                <button type="submit" name="update_item" class="addItem_butt">Update</button>
            </form>
            <button class="elemSideButt" onclick="removeElem('updateItem')">Exit</button>
        </div>
        <?php endif; ?>
        <div id="verifyToBeAdmin" class="elemSide">
            <div class="elemside active verifyAdminScroll">
                <table border="1">
                    <tr>
                        <th>username</th>
                        <th>full name</th>
                        <th>Reason </th>
                        <th>date created</th>
                        <th>make admin</th>
                    </tr>
                    <?php  foreach($allUserUsers AS $allUserUser): ?>
                        <tr>
                            <td><?=  htmlspecialchars($allUserUser['username']) ?></td>
                            <td><?=  htmlspecialchars($allUserUser['first_name']) ?> <?=  htmlspecialchars($allUserUser['last_name']) ?></td>
                            <td><?= htmlspecialchars($allUserUser['message'])  ?> </td>
                            <td><?=  htmlspecialchars($allUserUser['date_created']) ?></td>
                            <td>
                                <form action="../adminUserProcesses/admin_actions.php" method="post">
                                    <input type="hidden" name="user_id" value="<?=  htmlspecialchars($allUserUser['user_id']) ?>">
                                    <button type="submit" name="make_admin" class="verifyClaim_butt" onclick="return confirm('Are you sure you want to make this user an admin?')">
                                        Make Admin </button>
                                </form>
                            </td>
                        </tr>
                    <?php  endforeach; ?>
                </table>
            </div>
            <button class="elemSideButt" onclick="removeElem('verifyToBeAdmin')">Exit</button>
        </div>
        <?php if(isset($_POST['requestClaimButt'])): ?>
            <div class="elemSide active" id="requestBox">
                <?= showError($error['requesting_error']); ?>
                <form action="../adminUserProcesses/admin_actions.php" method="post">
                    <input type="hidden" name="claim_item_id" value="<?= $_POST['item_id_for_claims'] ?>">
                    <button type="submit" name="approve" class="verifyClaim_butt">Approve Claim</button>
                    <button type="submit" name="reject" class="verifyClaim_butt">Reject Claim</button>
                </form>
                <button class="elemSideButt" onclick="removeElem('requestBox')">Exit</button> 
            </div>
        <?php endif; ?>
        <?php if(isset($_POST['messageButt'])): ?>
            <div class="elemSide active" id="messageBox">
                <form action="../adminUserProcesses/admin_actions.php" method="post">
                    <input type="hidden" name="receiver_id" value="<?= $_POST['receiver_user_id'] ?>">  
                    <input type="hidden" name="sender_user_id" value="<?= $_SESSION['user_id'] ?>">
                    <label>What is your message?</label> <input type="text" name="message_content" placehoder="text here" required> <br> <br>
                    <label>Item/s:</label>
                    <select name="user_item">
                        <?php foreach($items as $item): ?>
                            <?php if($_POST['receiver_user_id'] == $item['user_id']): ?>
                                <option value="<?= htmlspecialchars($item['item_id']) ?>">
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select> <br> <br>
                    <button type="submit" name="send_message" class="addItem_butt">Send Message</button>
                </form>
                <button class="elemSideButt" onclick="removeElem('messageBox')">Exit</button> 
            </div>
        <?php endif; ?>
        <div class="navBar">
                <a href="#home">Home</a>
                <a href="#Items">Items</a></li>
                <a href="#requestClaim">Manage Claims</a>
                <a href="#messages">Messages & User Management</a>
                <a href="#contactUs">Our Contacts</a>
        </div>
            <div class="home page">
                <section id="home">
                    <div class="page1h1 h1Box">
                        <h1 style="font-size: 30px">Welcome to Administrator <?= $_SESSION['first_name']; ?> <?= $_SESSION['last_name'] ?> </h1>
                        <br>
                        <h1 style="font-size: 40px">Dashboard</h1>
                    </div>
                    <div class="page1Content">
                        <div class="numPeople sBox">
                            Total Number of users: 
                            <?=  htmlspecialchars($totalUsers) ?>
                        </div>
                        <div class="statClaims sBox">
                            Total Number of Claims Requests: 
                            <?= htmlspecialchars($totalClaims)  ?> <br> <br>
                            Average Number of Claims Requests Per Week: 
                            <?=  htmlspecialchars($averageClaims) ?>
                        </div>
                        <div class="statItems sBox">
                            Total Number of Added Items: 
                            <?= htmlspecialchars($totalItems)  ?> <br> <br>
                            Average Number of Added Items Per Week: 
                            <?=  htmlspecialchars($averageItems) ?>
                        </div>
                        <div class="statApproval sBox">
                            Total Number of Approved Claim Requests: 
                            <?= htmlspecialchars($totalApproved)  ?> <br> <br>
                            Total Number of rejected Claims Requests: 
                            <?=  htmlspecialchars($totalRejected) ?>
                        </div>
                        <div class="idkBox sBox">
                            <h1>Newest Admin Log</h1> <br>
                            <?php if(!empty($newest_action)): ?>
                            <?=  htmlspecialchars($newest_action) ?> <br>
                            <?=  htmlspecialchars($newest_announcement) ?> <br>
                            username: <?=  htmlspecialchars($newest_admin_username) ?> <br>
                            datetime: <?=  htmlspecialchars($newest_datetime) ?>
                            <?php else: ?>
                                No Latest Admin Log
                            <?php endif; ?>
                        </div>
                        <div class="iknowBox sBox">
                            <h1>Admin Logs</h1>
                            <form action="adminLogsTable.php" method="post">
                                <input type="submit" value="See Admin Logs"></input>
                            </form>
                        </div>
                        <div class="pie sBox">
                                <canvas id="pieChart"></canvas>
                        </div>
                        <div class="anBox sBox">
                            <h2>Post Announcement</h2>
                                <form action= "../adminUserProcesses/admin_actions.php" method="post">
                                    <input type="hidden" name="admin_name" value="<?= $currentUser[0]['username'] ?>">
                                    <label>Content:</label><br>
                                    <textarea name="content" rows="5" cols="10" required></textarea><br><br>

                                    <input type="submit" name="post_announcement" value="Post">
                                </form>
                        </div>
                    </div>
                </section>
            </div>
            <div class="items page">
                <section id="Items">
                    <div class="page2h1 h1Box">
                        <h1>Here are the Lost and Found Items</h1>
                    </div> 
                    <div class="search1b searchBox">
                        <button onclick="showElem('addItem')">Add New Item</button>
                            <form  action="#Items" method="post">
                                <label>Search with Your Keyword</label>
                                <input class="searchBar" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Enter keyword">
                                <button type="submit">Search</button>
                                <button type="submit" name="clear">Clear Search</button>
                            </form>
                    </div>
                    <div class="page2Content">
                        <div class="tableBox page2Box1">
                            <table border="2px">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th>Location</th>
                                    <th>Date Reported</th>
                                    <th>Status</th>
                                    <th>Person Reported</th>
                                </tr>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['title']) ?></td>
                                    <td><img src="<?= htmlspecialchars($item['image_url']) ?>" alt="item" width="100"></td>
                                    <td><?= htmlspecialchars($item['description']) ?></td>
                                    <td><?= htmlspecialchars($item['location']) ?></td>
                                    <td><?= htmlspecialchars($item['date_reported']) ?></td>
                                    <td><?= htmlspecialchars($item['status']) ?></td>
                                    <td><?= htmlspecialchars($item['username']) ?></td>
                                </tr>
                                <?php endforeach; ?>

                                <?php if (empty($filteredItems)): ?>
                                <tr><td colspan="6">No items found.</td></tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="page2Part">
                                <div class="categoryList page2Box2">
                                    <h3>All Items by Category</h3> <br>
                                    <ul class="ulOutside">
                                        <?php foreach ($categories as $cat): ?>
                                            <li><?= htmlspecialchars($cat) ?>:
                                                <ul>
                                                    <?php foreach ($items as $item): ?>
                                                        <?php if ($item['category'] === $cat): ?>
                                                            <li><?= htmlspecialchars($item['title']) ?></li>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="userItemBox page2Box3">
                                    <h3>Manage All Items</h3>
                                    <?php if (!empty($items)): ?>
                                        <?php foreach ($items as $item): ?>
                                            <div class="userItemCard">
                                                <strong><?= htmlspecialchars($item['title']) ?></strong><br>
                                                <img src="<?= htmlspecialchars($item['image_url']) ?>" width="100"><br>
                                                <?= htmlspecialchars($item['description']) ?><br>
                                                <form method="post" action="adminMenu.php#Items" style="display:inline;">
                                                    <input type="hidden" name="update_item_id" value="<?= $item['item_id'] ?>">
                                                    <input type="hidden" name="status" value="<?= $item['status'] ?>">
                                                    <input type="hidden" name="location" value="<?= $item['location'] ?>">
                                                    <input type="hidden" name="title" value="<?= htmlspecialchars($item['title']) ?>">
                                                    <input type="hidden" name="description" value="<?= $item['description'] ?>">
                                                    <input type="hidden" name="category" value="<?= $item['category'] ?>">
                                                    <button type="submit">Update</button>
                                                </form>
                                                <form method="post" action="../processesForBoth/delete_item.php" style="display:inline;">
                                                    <input type="hidden" name="delete_item_user_id" value="<?= $item['items_user_id'] ?>">
                                                    <input type="hidden" name="delete_item_id" value="<?= $item['item_id'] ?>">
                                                    <button type="submit" onclick="return confirm('Delete this item?')">Delete</button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>You haven't added any items yet.</p>
                                    <?php endif; ?>
                                </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="claim page">
                <section id="requestClaim">
                    <div class="page3h1 h1Box">
                        <h1>Manage All Claims</h1>
                    </div>
                    <div class="page3Content contentBox">
                    <div class="search2b searchBox">
                        <form  action="#requestClaim" method="post">
                            <label>Search with Your Keyword</label>
                            <input class="searchBar" name="search_claim" value="<?= htmlspecialchars($search) ?>" placeholder="Enter keyword">
                            <button type="submit">Search</button>
                            <button type="submit" name="clear_claim">Clear Search</button>
                        </form>
                        <table border="2px">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th>Location</th>
                                    <th>Date Reported</th>
                                    <th>Status</th>
                                    <th>Claimers</th>
                                    <th>Request Claim Button</th>
                                </tr>
                                <?php foreach ($item_claims as $item_claim): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item_claim['title']) ?></td>
                                    <td><img src="<?= htmlspecialchars($item_claim['image_url']) ?>" alt="item" width="100"></td>
                                    <td><?= htmlspecialchars($item_claim['description']) ?></td>
                                    <td><?= htmlspecialchars($item_claim['location']) ?></td>
                                    <td><?= htmlspecialchars($item_claim['date_reported']) ?></td>
                                    <td><?= htmlspecialchars($item_claim['claim_status']) ?></td>
                                    <td><?= htmlspecialchars($item_claim['username']) ?></td>
                                    <td>
                                        <form action="#requestClaim" method="post">
                                            <input type="hidden" name="item_id_for_claims" value="<?= $item_claim['claim_item_id']?>">
                                            <button type="submit" name="requestClaimButt" class="claimButton">Verify Claim</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>

                                <?php if (empty($filteredItems)): ?>
                                <tr><td colspan="6">No items found.</td></tr>
                                <?php endif; ?>
                        </table>
                    </div>
                        <div class="uBox">
                            <h2>Approved & Rejected Claims</h2>
                            <table border="1">
                                <tr>
                                    <th>Item</th>
                                    <th>Item Status</th>
                                    <th>Claim Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                                <?php foreach ($claims as $claim): ?>
                                    <tr>    
                                        <td><?= htmlspecialchars($claim['title']) ?> <br>
                                    <img src="<?= htmlspecialchars($claim['image_url']) ?>" alt="Item Image" width="80" height="80">    </td>
                                        <td><?= htmlspecialchars($claim['claim_status']) ?></td>
                                        <td><?= htmlspecialchars($claim['status']) ?></td>
                                        <td><?= htmlspecialchars($claim['request_date']) ?></td>
                                        <td>
                                            <form action="../adminUserProcesses/admin_actions.php" method="post" style="display:inline-block;">
                                                <input type="hidden" name="claim_id" value="<?= $claim['claim_id'] ?>">
                                                <button type="submit" name="delete_claim" onclick="return confirm('Delete this claim?')" class="verifyClaim_butt">Delete</button>
                                                <button type="submit" name="revert_claim" class="verifyClaim_butt">cancel claim status</button>
                                                <button type="submit" name="make_claimed" class="verifyClaim_butt">already claimed</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <div class="messages page">
                <section id="messages">
                    <div class="page3h1 h1Box">
                        <h1>Your Messages and Manage All Users</h1>
                    </div>
                    <div class="page3Content contentBox">
                        <div class="search3b searchBox">
                            <form  action="#messages" method="post">
                                <label>Search Username</label>
                                <input class="searchBar" name="search_user" value="<?= htmlspecialchars($search_user) ?>" placeholder="Enter Username You Want To Search">
                                <button type="submit">Search</button>
                                <button type="submit" name="clear_user">Clear Search</button>
                            </form>
                                <?php if(!empty($usernames)): ?>
                                    <table border="0.5px" class="messages_table">
                                        <tr>
                                            <th>
                                                <h1>Users</h1>
                                            </th>
                                            <th>
                                                their Email
                                            </th>
                                            <th>
                                            </th>
                                        </tr>
                                    <?php foreach($usernames as $username): ?>
                                        <?php  if($username['user_id'] != $userId): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($username['username'])?></td>
                                            <td><?= htmlspecialchars($username['email']) ?></td>
                                            <td>
                                                <form action="adminMenu.php#messages" method="post">
                                                    <input type="hidden" name="receiver_user_id" value="<?= $username['user_id'] ?>">
                                                    <button type="submit" name="messageButt" >Message</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php  endif; ?>
                                    <?php endforeach; ?>
                                    </table>
                                <?php else: ?>
                                    There is no such Username, refer to the item in items section
                                <?php endif; ?>
                        </div>
                        <div class="messUsersCont uBox">
                            <div class="yourMessagesBox uBox">
                                <?php if(!empty($user_messages)): ?>
                                    <table border="1px">
                                        <tr>
                                            <th>Sender</th>
                                            <th>Message</th>
                                            <th>Receiver</th>
                                            <th>Date Sent</th>
                                        </tr>
                                        <?php foreach($user_messages as $user_message): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user_message['sender_username']) ?></td>
                                                <td><?= htmlspecialchars($user_message['content']) ?></td>
                                                <td><?= htmlspecialchars($user_message['receiver_username']) ?></td>
                                                <td><?= htmlspecialchars($user_message['sent_at']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                <?php else: ?>
                                    You have No Messages
                                <?php endif; ?>
                            </div>
                            <div class="allUsersBox uBox">
                            <h2>All Users</h2> <br> <br>
                            Note: Once confirmed, delete button would delete the user instantly and all related data, 
                                please be patient, with confirming.
                                <table border="1">
                                    <tr>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                    <?php foreach ($allUsers as $user): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['username']) ?></td>
                                            <td><?= htmlspecialchars($user['role']) ?></td>
                                            <td>
                                                <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                                    <form action="../adminUserProcesses/admin_actions.php" method="post" onsubmit="return confirm('Are you sure you want to delete this user and everything related?');">
                                                        <input type="hidden" name="delete_user_id" value="<?= $user['user_id'] ?>">
                                                        <button type="submit">Delete</button>
                                                    </form>
                                                <?php else: ?>
                                                    You Brothah
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <section id="contactUs">
                <div class="contact page">
                        <div class="left_contacts_box contact_box">

                        <div class="lostAndFoundIcon"></div>
                        <i class="fa-brands fa-social"> Social Media Platforms</i> <br> <br>
                            <i class="fa-brands fa-facebook"> Facebook:</i>  @Lost_and_found <br> <br> <br>
                            <i class="fa-brands fa-instagram"> Instagram:</i> @Lost_and_found <br> <br> <br>
                            <i class="fa-solid fa-phone"> Cell Number:</i>  09317306705
                        </div>
                        <div class="right_contacts_box contact_box">
                        <i class="fa-brands fa-github"> Github:</i> lost_and_found <br> <br> <br>
                        <i class="fa-solid fa-envelope"> Email:</i> lost_and_found@gmail.com <br> <br>
                        <div class="lostAndFoundContent">
                            Lost And Found is a gathering place for those who have lost something
                             and has found something that which they think belongs to others,
                              we help in connecting people so that we never have to worry about our lost and found items!
                        </div>
                </div>
            </section>
    </div>
    <script src="../allJavascripts/lostAndFound.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const locations = <?php echo json_encode($locations); ?>;
        const counts = <?php echo json_encode($counts); ?>;

        const ctx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: locations,
                datasets: [{
                    label: 'Items by Location',
                    data: counts,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#7ED6DF', '#F8EFBA',
                        '#E056FD', '#CAD3C8', '#F97F51', '#3B3B98', '#B33771'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        right: 50
                    }
                },
                plugins: {
                    legend: {
                         position: 'right',
                         labels: {
                                font: {
                                    family: 'Arial', 
                                    size: 14,
                                    weight: 'bold',  
                                },
                                color: 'white'
                            }
                    },
                    title: {
                        display: true,
                        text: 'Distribution of Items by Location',
                            font: {
                                family: 'Arial', 
                                size: 14,
                                weight: 'bold',  
                            },
                            color: 'white'
                    }
                }
            }
        });
    </script>
</body>
</html>