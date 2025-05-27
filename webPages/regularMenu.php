<!--<?php
session_start();

require_once('../config.php');

$userId = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE user_id = $userId";

$result = $conn->query($sql);
$currentUser = [];
if($result && $result->num_rows > 0){
    if($row = $result->fetch_assoc())
    {
        $currentUser[] = $row;
    }
}

$sql = "SELECT admin_logs.announcements, admin_logs.done_at, users.username AS admin_username
        FROM admin_logs
        JOIN users ON admin_logs.admin_id = users.user_id
        ORDER BY admin_logs.done_at DESC
        LIMIT 1";
$result = $conn->query($sql);

$announcement = "No announcements yet.";

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $announcement = $row['announcements'];
    $dateYeah = $row['done_at'];
    $admin_username = $row['admin_username'];
}

$sql = "SELECT title, image_url, description, location FROM items
WHERE date_reported = (SELECT MIN(date_reported) FROM items) AND status = 'unclaimed'";

$result = $conn->query($sql);
if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $old_title = $row['title'];
        $old_img = $row['image_url'];
        $old_descrip = $row['description'];
        $old_loc = $row['location'];
    }
}
$sql = "SELECT title, image_url, description, location FROM items
WHERE date_reported = (SELECT MAX(date_reported) FROM items) AND status = 'unclaimed'";

$result = $conn->query($sql);
if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $new_title = $row['title'];
        $new_img = $row['image_url'];
        $new_descrip = $row['description'];
        $new_loc = $row['location'];
    }
}

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

$sql = "SELECT *, items.title AS item_title FROM claims 
        JOIN items ON claims.item_id = items.item_id 
        WHERE claimer_id = $userId";

$result_for_claims = $conn->query($sql);
$user_claims = [];

if($result_for_claims && $result_for_claims->num_rows > 0){
    while($row = $result_for_claims->fetch_assoc()){
        $user_claims[] = $row;
    }
}

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

$search = '';
if (isset($_POST['clear'])) {
    $items = [];
} else {
    $search = $_POST['search'] ?? '';
    $searchEscaped = $conn->real_escape_string($search);

    $sql = "SELECT * FROM items
            JOIN users ON items.user_id = users.user_id
            WHERE title LIKE '%$searchEscaped%' 
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

$search_claim = '';
if (isset($_POST['clear_claim'])) {
    $items = [];
} else {
    $search_claim = $_POST['search_claim'] ?? '';
    $searchEscaped = $conn->real_escape_string($search_claim);

    $sql = "SELECT items.*, users.username AS user_username, claims.status AS claim_status FROM items
            LEFT JOIN claims ON items.item_id = claims.item_id
            LEFT JOIN users ON claims.claimer_id = users.user_id
            WHERE title LIKE '%$searchEscaped%' 
            OR description LIKE '%$searchEscaped%' 
            OR location LIKE '%$searchEscaped%' 
            OR category LIKE '%$searchEscaped%'
            OR claims.status IS NOT NULL";

    $result = $conn->query($sql);
    $claim_items = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $claim_items[] = $row;
        }
    }
}

$search_user = '';
if (isset($_POST['clear_user'])) {
    $items = [];
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

$catResult = $conn->query("SELECT DISTINCT category FROM items");
$categories = [];
if ($catResult && $catResult->num_rows > 0) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

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
    <link rel="stylesheet" href="../allCss/regularMenu.css?v=<?php echo time(); ?>">
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
                <li><a onclick="showElem('applyAdmin')">Apply to be an Admin</a></li>
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
                    Change only which you want to update. <br>
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
                <form action="../processesForBoth/add_item.php" method="post" enctype="multipart/form-data" >
                    <label>Title:</label><input type="text" name="title" required>
                    <label>Description:</label><input type="text" name="description" required>
                    <label>Location:</label><input type="text" name="location" required>
                    <label>Category:</label><input type="text" name="category" required>
                    <label>Image:</label><input type="file" name="image" accept="image/*">
                    <button type="submit" name="addItem" class="addItem_butt">Submit Item</button>
                </form>
            <button class="elemSideButt" onclick="removeElem('addItem')">Exit</button>
        </div>
        <?php if(isset($_POST['item_id'])):?>
        <div id="updateItem" class="elemSide active">
            <form action="../regularUserProcesses/update_item.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="item_id" value="<?= $_POST['item_id'] ?>">
                <input type="hidden" name="status" value="unclaimed">
                <label>Title:</label><input type="text" name="title" value="<?= $_POST['title'] ?>" >
                <label>Description:</label><input type="text" name="description" value="<?= $_POST['description'] ?>" ><br>
                <label>Location:</label><input type="text" name="location" value="<?= $_POST['location'] ?>"><br>
                <label>Category:</label><input type="text" name="category" value="<?= $_POST['category'] ?>" ><br>
                <label>Image:</label><input type="file" name="image" accept="image/*"><br>
                <button type="submit" name="update_item" class="addItem_butt">Update</button>
            </form>
            <button class="elemSideButt" onclick="removeElem('updateItem')">Exit</button>
        </div>
        <?php endif; ?>
        <div id="applyAdmin" class="elemSide" style="flex-direction: column;">
            <div class="active verifyAdminScroll" style="min-height: 50%;">
                <table border="1">
                        <tr>
                            <th>username</th>
                            <th>full name</th>
                            <th>Reason </th>
                            <th>date created</th>
                        </tr>
                        <?php  foreach($allUserUsers AS $allUserUser): ?>
                            <tr>
                                <td><?=  htmlspecialchars($allUserUser['username']) ?></td>
                                <td><?=  htmlspecialchars($allUserUser['first_name']) ?> <?=  htmlspecialchars($allUserUser['last_name']) ?></td>
                                <td><?= htmlspecialchars($allUserUser['message'])  ?> </td>
                                <td><?=  htmlspecialchars($allUserUser['date_created']) ?></td>
                            </tr>
                        <?php  endforeach; ?>
                    </table>
                </div>
            <form action="../regularUserProcesses/request_admin.php" method="post">
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                <label>Why do you want to become an admin?</label><br>
                <textarea name="request_message" maxlength="255" required placeholder="Write your reason here..."></textarea><br>
                <button type="submit" class="request_admin_butt" name="apply_admin">Submit Request</button>
            </form>
            <button class="elemSideButt" onclick="removeElem('applyAdmin')">Exit</button>
        </div>
        <?php if(isset($_POST['requestClaimButt'])): ?>
            <div class="elemSide active" id="requestBox">
                <?= showError($error['requesting_error']); ?>
                <form action="../regularUserProcesses/requestClaim.php" method="post">
                    <input type="hidden" name="claim_item_id" value="<?= $_POST['item_id_for_claims'] ?>">
                    <input type="hidden" name="claimer_id" value="<?= $_SESSION['user_id'] ?>">
                    <label>Claim Message: </label><input type="text" name="claimMessage" placeholder="Maximum 255 characters" required> <br> <br>
                    <button type="submit" name="request" class="addItem_butt">Request Now</button>
                </form>
                <button class="elemSideButt" onclick="removeElem('requestBox')">Exit</button> 
            </div>
        <?php endif; ?>
        <?php if(isset($_POST['messageButt'])): ?>
            <div class="elemSide active" id="messageBox">
                <form action="../regularUserProcesses/sendMessage.php" method="post">
                    <input type="hidden" name="receiver_id" value="<?= $_POST['receiver_user_id'] ?>">  
                    <input type="hidden" name="sender_user_id" value="<?= $_SESSION['user_id'] ?>">
                    <label>What is your message?</label> <input type="text" name="message_content" placehoder="text here" required> <br> <br>
                    <label>Item/s:</label>
                    <select name="user_item">
                        <?php foreach($items as $item): ?>
                            <?php if($_POST['receiver_user_id'] == $item['user_id']): ?>
                                <option value="<?= $item['item_id'] ?>">
                                    <?= htmlspecialchars($item['title']) ?>
                                </option>
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
                <a href="#requestClaim">Request Claim</a>
                <a href="#messages">Messages</a>
                <a href="#contactUs">Contact Us</a>
        </div>
            <div class="home page">
                <section id="home">
                    <div class="page1h1 h1Box">
                        <h1>Welcome to Regular Menu, <?= $_SESSION['first_name']; ?> <?= $_SESSION['last_name'] ?> </h1> 
                    </div>
                    <div class="page1Content contentBox">
                        <div class="anBox sBox">
                            <h1>Announcement</h1> <br>
                            <?php  if(!empty($admin_username)): ?>
                            <?= htmlspecialchars($announcement) ?> <br> <br> <br>
                            <?= htmlspecialchars($dateYeah) ?> <br> <br>
                            <?=  htmlspecialchars($admin_username) ?>
                            <?php else:  ?>
                                No Announcements Yet
                            <?php  endif; ?>
                        </div>
                        <div class="idkBox sBox">
                            <h1>Oldest Item</h1> <br>
                            <?php  if(empty($old_title)): ?>
                                No Items Yet
                            <?php else: ?>
                                <?=  htmlspecialchars($old_title) ?> <br> <br>
                                <img src="<?= htmlspecialchars($old_img) ?>" alt="item" width="100"> <br>
                                <?=  htmlspecialchars($old_descrip) ?> <br> <br>
                                <?=  htmlspecialchars($old_loc) ?>
                            <?php  endif; ?>
                        </div>
                        <div class="idk2Box sBox">
                           <h1>Newest Item</h1>
                           <?php  if(empty($new_title)): ?>
                                No Items Yet
                            <?php else: ?>
                            <?=  htmlspecialchars($new_title) ?> <br> <br>
                                <img src="<?= htmlspecialchars($new_img) ?>" alt="item" width="100"> <br>
                                <?=  htmlspecialchars($new_descrip) ?> <br> <br>
                                <?=  htmlspecialchars($new_loc) ?>
                            <?php  endif; ?>
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
                                    <h3>Your Added Items</h3>
                                    <?php if (!empty($userItems)): ?>
                                        <?php foreach ($userItems as $item): ?>
                                            <div class="userItemCard">
                                                <strong><?= htmlspecialchars($item['title']) ?></strong><br>
                                                <img src="<?= htmlspecialchars($item['image_url']) ?>" width="100"><br>
                                                <?= htmlspecialchars($item['description']) ?><br>
                                                <form method="post" action="regularMenu.php#Items" style="display:inline;">
                                                    <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                                    <input type="hidden" name="status" value="<?= $item['status'] ?>">
                                                    <input type="hidden" name="location" value="<?= $item['location'] ?>">
                                                    <input type="hidden" name="title" value="<?= htmlspecialchars($item['title']) ?>">
                                                    <input type="hidden" name="description" value="<?= $item['description'] ?>">
                                                    <input type="hidden" name="category" value="<?= $item['category'] ?>">
                                                    <button type="submit">Update</button>
                                                </form>
                                                <form method="post" action="../processesForBoth/delete_item.php" style="display:inline;">
                                                    <input type="hidden" name="delete_item_id" value="<?= $item['item_id'] ?>">
                                                    <input type="hidden" name="delete_item_user_id" value="<?= $item['user_id'] ?>">
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
                        <h1>Request to Claim Items Now</h1>
                    </div>
                    <div class="page3Content contentBox">
                    <div class="search2b searchBox">
                        <form  action="#requestClaim" method="post">
                            <label>Search with Your Keyword</label>
                            <input class="searchBar" name="search_claim" value="<?= htmlspecialchars($search_claim) ?>" placeholder="Enter keyword">
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
                                    <th>Claimer</th>
                                    <th>Request Claim Button</th>
                                </tr>
                                <?php foreach ($claim_items as $claim_item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($claim_item['title']) ?></td>
                                    <td><img src="<?= htmlspecialchars($claim_item['image_url']) ?>" alt="item" width="100"></td>
                                    <td><?= htmlspecialchars($claim_item['description']) ?></td>
                                    <td><?= htmlspecialchars($claim_item['location']) ?></td>
                                    <td><?= htmlspecialchars($claim_item['date_reported']) ?></td>
                                    <td><?= htmlspecialchars($claim_item['claim_status']) ?></td>
                                    <td><?= htmlspecialchars($claim_item['user_username']) ?></td>
                                    <td>
                                        <form action="#requestClaim" method="post">
                                            <input type="hidden" name="item_id_for_claims" value="<?= $item['item_id']?>">
                                            <button type="submit" name="requestClaimButt" class="claimButton">Request Claim of Item</button>
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
                            <?php if(!empty($user_claims)): ?>
                                <table border="1px">
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Claim Message</th>
                                        <th>Status</th>
                                        <th>Request date</th>
                                    </tr>
                                    <?php foreach($user_claims as $user_claim): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user_claim['item_title']) ?></td>
                                            <td><?= htmlspecialchars($user_claim['claim_message']) ?></td>
                                            <td><?= htmlspecialchars($user_claim['status']) ?></td>
                                            <td><?= htmlspecialchars($user_claim['request_date']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            <?php else: ?>
                                You have no Claims Yet
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>
            <div class="messages page">
                <section id="messages">
                    <div class="page3h1 h1Box">
                        <h1>Message a User</h1>
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
                                                <form action="regularMenu.php#messages" method="post">
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
                        <div class="claimBox uBox">
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
</body>
</html>