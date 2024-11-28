<?php 
include 'header.php';
include 'db.php';

global $db;
                
if ($db->connect_errno) {
    //no db connection
    die("Failed to connect to the database: " . $db->connect_error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<!--AUTHOR REPORT START-->
<body>
    <div style="padding: 10px;">
        <div class ="AuthorReportContainer">
            <h1>Author Report</h1>
            <table border="1">
                <!--Setting column widths for Author-->
                <colgroup>
                    <col style="width: 150px">
                    <col style="width: 200px">
                    <col style="width: 200px">
                    <col style="width: 250px">
                    <col style="width: 200px">
                </colgroup>
                <thead>
                    <th colspan="9" style="border-bottom: 1px solid black; padding: 0; font-size: 32px;">Authors</th>
                </thead>
                <tbody>  
                    <tr class = "Bigger">
                        <th>Author ID</th>
                        <th>First Name</th>
                        <th>Last name</th>
                        <th>Alma Mater</th>
                        <th>Email</th>
                    </tr>

                    <?php //PHP Start
                    $query = "SELECT * FROM author";
                    $result = $db->query($query);

                    if (!$result) {
                        die("Query failed: " . $db->error);
                    }

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class = \"Bigger\">";
                        echo "<td>" . htmlspecialchars($row['author_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['alma_mater']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "</tr>";

                        $auth_id = $row['author_id'];
                        $query2 = "SELECT e.author_id, e.story_id, e.time_added
                                FROM author a
                                JOIN editornotes e ON a.author_id = e.author_id 
                                WHERE e.author_id = '$auth_id'";

                        $result2 = $db->query($query2);

                        if (!$result2) {
                            die("". $db->error);
                        }
                        
                        //Editor Notes Start
                        echo "<tr><td colspan='5'>";
                        echo "<table border='1' style='margin-left: 150px;'>";

                        echo
                        "<colgroup>
                            <col style=\"width: 150px\">
                            <col style=\"width: 150px\">
                            <col style=\"width: 200px\">
                        </colgroup>

                        <tr>
                            <th colspan=\"9\" style=\"border-bottom: 1px solid black; padding: 0;\">Editor Notes</th>
                        </tr>

                        <tr>
                            <th>Author ID</th>
                            <th>Story ID</th>
                            <th>Time Added</th>
                        </tr>";

                        while ($editornotes = $result2->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($editornotes['author_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($editornotes['story_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($editornotes['time_added']) . "</td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                        echo "</td></tr>";

                        //Written Start
                        echo "<tr><td colspan='5'>";
                        echo "<table border='1' style='margin-left: 150px;'>";

                        echo
                        "<colgroup>
                            <col style=\"width: 150px\">
                            <col style=\"width: 100px\">
                        </colgroup>

                        <tr>
                            <th colspan=\"9\" style=\"border-bottom: 1px solid black; padding: 0;\">Written</th>
                        </tr>

                        <tr>
                            <th>Author ID</th>
                            <th>Story ID</th>
                        </tr>";

                        $query3 = "SELECT w.author_id, w.story_id
                                FROM author a
                                JOIN written w ON w.author_id = a.author_id
                                WHERE w.author_id = '$auth_id'";

                        $result3 = $db->query($query3);

                        if (!$result3) {
                            die("". $db->error);
                        }

                        while ($written = $result3->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($written['author_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($written['story_id']) . "</td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                        echo "</td></tr>";
                        echo "<tr style=\"height: 10px\"><td></td></tr>";
                    }
                    ?> <!--PHP END-->

                </tbody>
            </table>
        </div>

<!--STORY REPORT START-->
        <div class ="StoryReportContainer">
            <h1>Story Report</h1>
            <table border="1">
                <!--Setting column widths for top level-->
                <colgroup>
                    <col style="width: 100px">
                    <col style="width: 650px">
                    <col style="width: 200px">
                    <col style="width: 300px">
                    <col style="width: 200px">
                    <col style="width: 150px">
                </colgroup>
                <thead>
                    <th colspan="9" style="border-bottom: 1px solid black; padding: 0; font-size: 32px;">Stories</th>
                </thead>
                <tbody>
                    
                    <tr class = "Bigger">
                        <th>Story ID</th>
                        <th>Headline</th>
                        <th>Views</th>
                        <th>Publish Date</th>
                        <th>Category ID</th>
                        <th>Source ID</th>
                    </tr>

                    <?php //PHP Start
                    $query = "SELECT * FROM story";
                    $result = $db->query($query);

                    if (!$result) {
                        die("Query failed: " . $db->error);
                    }

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class = \"Bigger\">";
                        echo "<td>" . htmlspecialchars($row['story_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['headline']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['views']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['publish_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['category_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['source_id']) . "</td>";
                        echo "</tr>";


                        //Analytics Start 
                        $st_id = $row['story_id'];
                        $query2 = "SELECT a.story_id, a.views, a.likes, a.shares, a.time_reading_in_minutes
                                FROM story s
                                JOIN analytics a ON s.story_id = a.story_id
                                JOIN image i ON s.story_id = i.story_id 
                                WHERE a.story_id = '$st_id'";

                        $result2 = $db->query($query2);

                        if (!$result2) {
                            die("". $db->error);
                        }
                        
                        echo "<tr><td colspan='5'>";
                        echo "<table border='1' style='margin-left: 100px;'>";

                        echo
                        "<colgroup>
                            <col style=\"width: 125px\">
                            <col style=\"width: 200px\">
                            <col style=\"width: 200px\">
                            <col style=\"width: 200px\">
                            <col style=\"width: 200px\">
                        </colgroup>

                        <tr>
                            <th colspan=\"9\" style=\"border-bottom: 1px solid black; padding: 0;\">Analytics</th>
                        </tr>

                        <tr>
                            <th>Story ID</th>
                            <th>Views</th>
                            <th>Likes</th>
                            <th>Shares</th>
                            <th>Reading Time</th>
                        </tr>";

                        while ($analytics = $result2->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($analytics['story_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($analytics['views']) . "</td>";
                            echo "<td>" . htmlspecialchars($analytics['likes']) . "</td>";
                            echo "<td>" . htmlspecialchars($analytics['shares']) . "</td>";
                            echo "<td>" . htmlspecialchars($analytics['time_reading_in_minutes']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "</td></tr>";

                        //Image Start
                        echo "<tr><td colspan='5'>";
                        echo "<table border='1' style='margin-left: 100px;'>";

                        echo
                        "<colgroup>
                            <col style=\"width: 125px\">
                            <col style=\"width: 200px\">
                            <col style=\"width: 400px\">
                            <col style=\"width: 150px\">
                        </colgroup>

                        <tr>
                            <th colspan=\"9\" style=\"border-bottom: 1px solid black; padding: 0;\">Image</th>
                        </tr>

                        <tr>
                            <th>Story ID</th>
                            <th>Image File</th>
                            <th>Alt Text</th>
                            <th>Date Uploaded</th>
                        </tr>";

                        $query3 = "SELECT i.story_id, i.image_file, i.alt_text, i.date_uploaded
                                FROM story s
                                JOIN analytics a ON s.story_id = a.story_id
                                JOIN image i ON s.story_id = i.story_id 
                                WHERE i.story_id = '$st_id'";

                        $result3 = $db->query($query3);

                        if (!$result3) {
                            die("". $db->error);
                        }

                        while ($image= $result3->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($image['story_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($image['image_file']) . "</td>";
                            echo "<td>" . htmlspecialchars($image['alt_text']) . "</td>";
                            echo "<td>" . htmlspecialchars($image['date_uploaded']) . "</td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                        echo "</td></tr>";
                        echo "<tr style=\"height: 10px\"><td></td></tr>";
                    }
                    ?> <!--PHP END-->

                </tbody>
            </table>
        </div>

<!--USER REPORT START-->
        <div class ="UserReportContainer">
            <h1>User Report</h1>
            <table border="1">
                <!--Setting column widths for top level-->
                <colgroup>
                    <col style="width: 125px">
                    <col style="width: 200px">
                    <col style="width: 200px">
                    <col style="width: 200px">
                    <col style="width: 200px">
                    <col style="width: 200px">
                    <col style="width: 200px">
                </colgroup>
                <thead>
                    <th colspan="9" style="border-bottom: 1px solid black; padding: 0; font-size: 32px;">Users</th>
                </thead>
                <tbody>
                    
                    <tr class = "Bigger">
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Display Name</th>
                        <th>Email</th>
                        <th>Card ID</th>
                        <th>Subsription Status</th>
                    </tr>

                    <?php //PHP Start
                    $query = "SELECT * FROM user";
                    $result = $db->query($query);

                    if (!$result) {
                        die("Query failed: " . $db->error);
                    }

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class = \"Bigger\">";
                        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['display_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['card_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['subscription_status']) . "</td>";
                        echo "</tr>";


                        //Card Start 
                        $ca_id = $row['card_id'];
                        $query2 = "SELECT ca.card_id, ca.card_num, ca.security_code, ca.expires_on, ca.zip
                                FROM user u
                                JOIN card ca ON u.card_id = ca.card_id
                                WHERE ca.card_id = '$ca_id'";

                        $result2 = $db->query($query2);

                        if (!$result2) {
                            die("". $db->error);
                        }
                        
                        echo "<tr><td colspan='5'>";
                        echo "<table border='1' style='margin-left: 125px;'>";

                        echo
                        "<colgroup>
                            <col style=\"width: 200px\">
                            <col style=\"width: 300px\">
                            <col style=\"width: 200px\">
                            <col style=\"width: 200px\">
                            <col style=\"width: 200px\">
                        </colgroup>

                        <tr>
                            <th colspan=\"9\" style=\"border-bottom: 1px solid black; padding: 0;\">Card Info</th>
                        </tr>

                        <tr>
                            <th>Card ID</th>
                            <th>Card Number</th>
                            <th>Security Code</th>
                            <th>Expires On</th>
                            <th>Zip Code</th>
                        </tr>";

                        while ($card = $result2->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($card['card_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($card['card_num']) . "</td>";
                            echo "<td>" . htmlspecialchars($card['security_code']) . "</td>";
                            echo "<td>" . htmlspecialchars($card['expires_on']) . "</td>";
                            echo "<td>" . htmlspecialchars($card['zip']) . "</td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                        echo "</td></tr>";

                        //Comments Start
                        echo "<tr><td colspan='5'>";
                        echo "<table border='1' style='margin-left: 125px;'>";

                        echo
                        "<colgroup>
                            <col style=\"width: 150px\">
                            <col style=\"width: 150px\">
                            <col style=\"width: 200px\">
                            <col style=\"width: 200px\">
                        </colgroup>

                        <tr>
                            <th colspan=\"9\" style=\"border-bottom: 1px solid black; padding: 0;\">Comments</th>
                        </tr>

                        <tr>
                            <th>User ID</th>
                            <th>Story ID</th>
                            <th>Time Posted</th>
                            <th>Reply Count</th>
                        </tr>";

                        $us_id = $row['user_id'];
                        $query3 = "SELECT co.user_id, co.story_id, co.time_posted, co.reply_count
                                FROM user u
                                JOIN comments co ON u.user_id = co.user_id 
                                WHERE co.user_id = '$us_id'";

                        $result3 = $db->query($query3);

                        if (!$result3) {
                            die("". $db->error);
                        }

                        while ($image= $result3->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($image['user_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($image['story_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($image['time_posted']) . "</td>";
                            echo "<td>" . htmlspecialchars($image['reply_count']) . "</td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                        echo "</td></tr>";
                        echo "<tr style=\"height: 10px\"><td></td></tr>";
                    }
                    ?> <!--PHP END-->

                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>
