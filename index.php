<?php include 'header.php'; ?>
<main>
    <!--test comment 2-->
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
                        <h5 class="card-title">Import Starter Data</h5>
                        <p class="card-text">Easily import your Sources, Categories, Advertisers, and Subscription Data!</p>
                        <a href="createDatabase.php" class="btn btn-primary">Go to Import</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Import First CSV</h5>
                        <p class="card-text">Easily import Stories, Images, and Analytics!</p>
                        <a href="importCSV1.php" class="btn btn-primary">Go to Import</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Import Second CSV</h5>
                        <p class="card-text">Easily import Users and their data!</p>
                        <a href="importCSV2.php" class="btn btn-primary">Go to Import</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Import Third CSV</h5>
                        <p class="card-text">Easily import Authors and their projects they worked on!</p>
                        <a href="importCSV3.php" class="btn btn-primary">Go to Import</a>
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
