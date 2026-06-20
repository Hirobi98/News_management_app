<?php
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Management System - Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark border-end sidebar-custom" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 text-white fs-4 fw-bold text-uppercase border-bottom">
                <i class="bi bi-newspaper me-2"></i>News Admin
            </div>
            <div class="list-group list-group-flush mt-3">
                <a href="#" class="list-group-item list-group-item-action bg-dark text-light p-3 nav-link-custom active" data-target="dashboard-section"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-light p-3 nav-link-custom" data-target="articles-section"><i class="bi bi-journal-text me-2"></i>Articles</a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-light p-3 nav-link-custom" data-target="authors-section"><i class="bi bi-pen me-2"></i>Authors</a>

            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Top navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom topbar-custom px-4 py-3">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary btn-sm" id="sidebarToggle"><i class="bi bi-list fs-5"></i></button>
                    <h5 class="ms-3 mb-0 fw-bold text-secondary" id="page-title">Dashboard</h5>
                </div>
                
                <ul class="navbar-nav ms-auto d-flex flex-row align-items-center">
                    <li class="nav-item me-3">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm rounded-pill" placeholder="Search..." aria-label="Search">
                            <button class="btn btn-primary rounded-pill ms-1 btn-sm" type="button"><i class="bi bi-search"></i></button>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name=Admin+User&background=0D8ABC&color=fff" alt="Profile" class="rounded-circle me-2" width="35" height="35">
                            <span class="d-none d-md-inline fw-semibold">Admin</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a>
                            <a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                        </div>
                    </li>
                </ul>
            </nav>

            <!-- Main Content Container -->
            <div class="container-fluid p-4 main-content-area">
                
                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Article added successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <!-- Dashboard Section -->
                <section id="dashboard-section" class="content-section active-section">
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="card stat-card border-0 shadow-sm bg-primary text-white h-100">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase mb-2 opacity-75">Total Articles</h6>
                                        <h2 class="mb-0 fw-bold">1,245</h2>
                                    </div>
                                    <div class="icon-box bg-white bg-opacity-25 rounded p-3">
                                        <i class="bi bi-journal-text fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card border-0 shadow-sm bg-success text-white h-100">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase mb-2 opacity-75">Total Users</h6>
                                        <h2 class="mb-0 fw-bold">8,530</h2>
                                    </div>
                                    <div class="icon-box bg-white bg-opacity-25 rounded p-3">
                                        <i class="bi bi-people fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card border-0 shadow-sm bg-warning text-dark h-100">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase mb-2 opacity-75">Total Comments</h6>
                                        <h2 class="mb-0 fw-bold">3,120</h2>
                                    </div>
                                    <div class="icon-box bg-dark bg-opacity-10 rounded p-3">
                                        <i class="bi bi-chat-left-text fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card border-0 shadow-sm bg-info text-white h-100">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase mb-2 opacity-75">Total Views</h6>
                                        <h2 class="mb-0 fw-bold">45.2K</h2>
                                    </div>
                                    <div class="icon-box bg-white bg-opacity-25 rounded p-3">
                                        <i class="bi bi-eye fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-secondary">Recent Articles</h6>
                                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="document.querySelector('[data-target=\'articles-section\']').click()">View All</a>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0 align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-4">Title</th>
                                                    <th>Category</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $recent_query = "SELECT * FROM news_items ORDER BY date DESC LIMIT 5";
                                                $recent_result = @$conn->query($recent_query);
                                                if ($recent_result && $recent_result->num_rows > 0) {
                                                    while($row = $recent_result->fetch_assoc()) {
                                                        $status_class = ($row['status'] == 'Published') ? 'success' : 'warning';
                                                        echo "<tr>";
                                                        echo "<td class='ps-4 fw-medium'>" . htmlspecialchars($row['news_title']) . "</td>";
                                                        echo "<td><span class='badge bg-secondary'>" . htmlspecialchars($row['category']) . "</span></td>";
                                                        echo "<td>" . date('M d, Y', strtotime($row['date'])) . "</td>";
                                                        echo "<td><span class='badge bg-" . $status_class . " bg-opacity-10 text-" . $status_class . "'>" . htmlspecialchars($row['status']) . "</span></td>";
                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='4' class='text-center text-muted py-3'>No recent articles</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="mb-0 fw-bold text-secondary">Recent Comments</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex mb-3 pb-3 border-bottom">
                                        <div class="flex-shrink-0">
                                            <div class="bg-light rounded-circle p-2"><i class="bi bi-person text-secondary"></i></div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1 text-dark fs-6">John Doe</h6>
                                            <p class="mb-0 text-muted small">"Great article on AI! Really insightful."</p>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-light rounded-circle p-2"><i class="bi bi-person text-secondary"></i></div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1 text-dark fs-6">Jane Smith</h6>
                                            <p class="mb-0 text-muted small">"I disagree with the points made about the market."</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Articles Section -->
                <section id="articles-section" class="content-section d-none">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-secondary">Manage Articles</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addArticleModal"><i class="bi bi-plus-lg me-1"></i> Add New Article</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Category</th>
                                            <th>Date</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $all_query = "SELECT * FROM news_items ORDER BY id DESC";
                                        $all_result = @$conn->query($all_query);
                                        if ($all_result && $all_result->num_rows > 0) {
                                            while($row = $all_result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>#" . htmlspecialchars($row['id']) . "</td>";
                                                echo "<td class='fw-medium'>" . htmlspecialchars($row['news_title']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['author_name']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                                echo "<td>" . date('Y-m-d', strtotime($row['date'])) . "</td>";
                                                echo "<td class='text-end'>
                                                        <button class='btn btn-sm btn-outline-secondary me-1'><i class='bi bi-pencil'></i></button>
                                                        <button class='btn btn-sm btn-outline-danger'><i class='bi bi-trash'></i></button>
                                                      </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center text-muted py-4'>No articles found. Click 'Add New Article' to create one.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Authors Section -->
                <section id="authors-section" class="content-section d-none">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-secondary">Manage Authors</h5>
                            <button class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Author</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Bio Summary</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>#A01</td>
                                            <td class="fw-medium"><img src="https://ui-avatars.com/api/?name=Alex+Johnson" alt="AJ" class="rounded-circle me-2" width="30">Alex Johnson</td>
                                            <td>alex@example.com</td>
                                            <td class="text-truncate" style="max-width: 200px;">Senior financial analyst covering global markets...</td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#A02</td>
                                            <td class="fw-medium"><img src="https://ui-avatars.com/api/?name=Sarah+Lee" alt="SL" class="rounded-circle me-2" width="30">Sarah Lee</td>
                                            <td>sarah@example.com</td>
                                            <td class="text-truncate" style="max-width: 200px;">Tech journalist passionate about AI and robotics...</td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>

    <!-- Add Article Modal -->
    <div class="modal fade" id="addArticleModal" tabindex="-1" aria-labelledby="addArticleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="add_article.php" method="POST">
              <div class="modal-header">
                <h5 class="modal-title" id="addArticleModalLabel">Add New Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <div class="mb-3">
                      <label for="news_title" class="form-label">Title</label>
                      <input type="text" class="form-control" id="news_title" name="news_title" required>
                  </div>
                  <div class="mb-3">
                      <label for="author_name" class="form-label">Author Name</label>
                      <input type="text" class="form-control" id="author_name" name="author_name" required>
                  </div>
                  <div class="mb-3">
                      <label for="news_description" class="form-label">News Description</label>
                      <textarea class="form-control" id="news_description" name="news_description" rows="3" required></textarea>
                  </div>
                  <div class="mb-3">
                      <label for="category" class="form-label">Category</label>
                      <select class="form-select" id="category" name="category" required>
                          <option value="Finance">Finance</option>
                          <option value="Technology">Technology</option>
                          <option value="Politics">Politics</option>
                          <option value="Sports">Sports</option>
                          <option value="Entertainment">Entertainment</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label for="status" class="form-label">Status</label>
                      <select class="form-select" id="status" name="status">
                          <option value="Published">Published</option>
                          <option value="Draft">Draft</option>
                      </select>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Article</button>
              </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
    
    <script>
        // Check for success URL parameter to show modal if needed or switch tab
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if(urlParams.has('success')) {
                // Switch to articles section automatically
                const articlesTab = document.querySelector('[data-target="articles-section"]');
                if(articlesTab) {
                    articlesTab.click();
                }
                
                // Clean up URL without refreshing
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
</body>
</html>
