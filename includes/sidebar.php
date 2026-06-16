        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fa-solid fa-cloud-sun-rain text-primary"></i> AIoT Weather</h3>
            </div>

            <ul class="list-unstyled components">
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <a href="dashboard.php"><i class="fa-solid fa-chart-pie me-2"></i> Dashboard</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'monitoring.php' ? 'active' : '' ?>">
                    <a href="monitoring.php"><i class="fa-solid fa-satellite-dish me-2"></i> Monitoring Cuaca</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'dataset.php' ? 'active' : '' ?>">
                    <a href="dataset.php"><i class="fa-solid fa-database me-2"></i> Dataset</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'visualization.php' ? 'active' : '' ?>">
                    <a href="visualization.php"><i class="fa-solid fa-chart-line me-2"></i> Visualisasi</a>
                </li>
                
                <li class="nav-header mt-3 mb-1 px-3 text-uppercase text-muted" style="font-size: 0.8rem; font-weight: bold;">Machine Learning</li>
                
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'training.php' ? 'active' : '' ?>">
                    <a href="training.php"><i class="fa-solid fa-brain me-2"></i> Training Model</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'evaluation.php' ? 'active' : '' ?>">
                    <a href="evaluation.php"><i class="fa-solid fa-square-poll-vertical me-2"></i> Evaluasi Model</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'prediction.php' ? 'active' : '' ?>">
                    <a href="prediction.php"><i class="fa-solid fa-wand-magic-sparkles me-2"></i> Prediksi Cuaca</a>
                </li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>">
                    <a href="history.php"><i class="fa-solid fa-clock-rotate-left me-2"></i> Riwayat Prediksi</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content" class="w-100 bg-light">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <span class="ms-3 fw-bold text-secondary">Sistem Monitoring & Prediksi Cuaca Berbasis AIoT</span>
                </div>
            </nav>
            <div class="container-fluid px-4 pb-5">
