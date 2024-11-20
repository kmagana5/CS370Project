<?php include 'header.php'; ?>
<main>
    <!-- Hero Section -->
    <section class="hero text-white d-flex align-items-center" style="background: url('assets/caracalhome.jpg') center center/cover no-repeat; height: 70vh;">
        <div class="container text-center" style="background: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
            <h1 class="display-4">Caracal News Admin Panel</h1>
            <p class="lead">Your hub for importing stories and updates about Caracals</p>
            <a href="importStoryData.php" class="btn btn-light btn-lg">Get Started</a>
        </div>
    </section>


    <!-- Imports and reports ections -->
    <section class="container mt-5">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Import Stories</h5>
                        <p class="card-text">Easily manage and import your stories with our intuitive interface.</p>
                        <a href="importStoryData.php" class="btn btn-primary">Go to Import</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Import Authors</h5>
                        <p class="card-text">Here is where you add the information about the amazing authors keeping this incredibly niche news website running.</p>
                        <a href="importAuthorData.php" class="btn btn-primary">View Authors</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Import Comments</h5>
                        <p class="card-text">Add data relating to the comments our wonderful users make about caracals</p>
                        <a href="importCommentData.php" class="btn btn-primary">View Comments</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">View Reports</h5>
                        <p class="card-text">Access the reports we have been assigned.</p>
                        <a href="#" class="btn btn-primary">View Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script src="js/bootstrap.bundle.js"></script>
</body>
</html>
