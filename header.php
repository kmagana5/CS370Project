<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="assets/caracal.jpg" class="img-fluid">
                Caracal News
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="importStoryData.php">Import Stories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="importAuthorData.php">Import Authors</a>
                    </li>
                    <li>
                        <a class="nav-link" href="importCommentData.php" >Import Comments</a>
                    </li>
                    <li>
                        <a class="nav-link" href="">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="createDatabase.php">Create Database</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<script src="js/bootstrap.bundle.js"></script>
<script>
    function hideMessage(elementId, delay = 5000) {
        setTimeout(function() {
            var messageElement = document.getElementById(elementId);
            if (messageElement) {
                messageElement.style.transition = 'opacity 1s';
                messageElement.style.opacity = '0';
                setTimeout(function() {
                    messageElement.style.display = 'none';
                }, 1000);
            }
        }, delay);
    }
</script>

</body>
</html>
