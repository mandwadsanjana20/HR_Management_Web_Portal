<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login1.php");  // redirect if not logged in
    exit();
}

$admin_name = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>HR Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS v4.5.2 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: #fff;
            overflow: hidden;
          animation: pageFadeSlideIn 1.2s ease forwards;

        }
 @keyframes pageFadeSlideIn {
  0% {
    opacity: 0;
    transform: translateY(40px);
    filter: blur(15px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
    filter: blur(0);
  }
}
        .main-wrapper {
            display: flex;
            height: 100vh; /* Full viewport height */
            overflow: hidden;
        }

        /* Sidebar styles */
        .sidebar {
            width: 280px;
            background-color: #f8f9fa; /* light gray */
            padding: 1.5rem 1rem;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            font-weight: 600;
            color: #212529;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .sidebar a i {
            margin-right: 12px;
            font-size: 18px;
            width: 22px;
            text-align: center;
        }
        .sidebar a:hover {
            color: #007bff;
            text-decoration: none;
        }

        /* Profile section */
        .profile {
            text-align: center;
            margin-bottom: 2rem;
        }
        .profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 0.5rem;
        }
        .profile h3 {
            font-weight: 700;
            color: #343a40;
        }
        .profile hr {
            width: 60%;
            margin: 0.75rem auto 1.5rem;
            border-color: #dee2e6;
        }

        /* Main content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
            overflow: hidden;
        }

        #subTabs {
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #dee2e6;
            background: #fff;
            display: none;
            flex-wrap: wrap;
        }

        #subTabs button {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            border-radius: 20px;
            border: none;
            background: linear-gradient(to right, #1f1f1fff, #5c5d5dff);
            color: white;
            transition: background-color 0.3s ease;
            cursor: pointer;
            padding: 6px 14px;
            font-size: 14px;
        }
        #subTabs button:hover {
            background: #dc3545; /* Bootstrap red */
            color: white;
        }

        #contentFrame {
            flex: 1;
            width: 102%;
            border: none;
        }

        /* Scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.1);
            border-radius: 4px;
        }
    </style>

    <script>
        function loadPage(page) {
            document.getElementById('contentFrame').src = page;
        }

        // Define sub-tabs for each section
        const sectionTabs = {
            'employees': `
                <button class="btn btn-outline-primary btn-sm" onclick="loadPage('Emp_data.php')">Employee Details</button>
                <button class="btn btn-outline-primary btn-sm" onclick="loadPage('fetch_bank_details.php')">Employee Bank Details</button>
            `,
            'jobs': `
                <button class="btn btn-outline-primary btn-sm" onclick="loadPage('manage_jobs.php')">Manage Jobs</button>
                <button class="btn btn-outline-primary btn-sm" onclick="loadPage('add_job.php')">Add New Post</button>
                <button class="btn btn-outline-primary btn-sm" onclick="loadPage('admin_approval.php')">Shortlist/Reject candidates</button>
                <button class="btn btn-outline-primary btn-sm" onclick="loadPage('rejected_candidates.php')">Rejected candidates</button>

                `,
            'attendance': `
                <button class="btn btn-outline-success btn-sm" onclick="loadPage('admin_attendance.php')">Attendance By Monthly</button>
                <button class="btn btn-outline-success btn-sm" onclick="loadPage('attendance_hr.php')">Attendance By Weekly</button>
            `,
            'interview': `
                <button class="btn btn-outline-success btn-sm" onclick="loadPage('interview.php')">Schedule Interview</button>
                <button class="btn btn-outline-success btn-sm" onclick="loadPage('interview_status.php')">Generate Offer letter</button>
                <button class="btn btn-outline-success btn-sm" onclick="loadPage('selection_results.php')">Selected Employees</button>
                <button class="btn btn-outline-success btn-sm" onclick="loadPage('rejection.php')">Rejected Candidates</button>

            `,
            'projects': `
                <button class="btn btn-outline-success btn-sm" onclick="loadPage('assign_project.php')">Assign Projects</button>
                <button class="btn btn-outline-success btn-sm" onclick="loadPage('add_project.php')">Add New Projects</button>
                <button class="btn btn-outline-success btn-sm" onclick="loadPage('track_project_status.php')">Track Project Status</button>
            `,
             'payroll': `
               
            `,
            'leaves': `
               <button class="btn btn-outline-success btn-sm" onclick="loadPage('admin_leave.php')">Leave Requests</button>
                <button class="btn btn-outline-success btn-sm" onclick="loadPage('adminleave_history.php')">Leave History</button>
            `,
            'reports': `
               
            `,
        };

        function showSubTabs(sectionKey) {
            const subTabsDiv = document.getElementById("subTabs");
            if (sectionTabs[sectionKey]) {
                subTabsDiv.innerHTML = sectionTabs[sectionKey];
                subTabsDiv.style.display = 'flex';
            } else {
                subTabsDiv.innerHTML = '';
                subTabsDiv.style.display = 'none';
            }
        }
    </script>

</head>
<body>
<div class="main-wrapper">

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="profile">
            <img src="pp2.jpg" alt="Admin Profile Image">
            <h3>HR</h3>
            <hr>
        </div>
        <a href="#" onclick="showSubTabs('overview'); loadPage('dashboard_overview.php'); return false;">
            <i class="fas fa-tachometer-alt"></i> Dashboard Overview
        </a>
        <a href="#" onclick="showSubTabs('candidates'); loadPage('candidates_data.php'); return false;">
            <i class="fas fa-users"></i> Candidates
        </a>
        <a href="#" onclick="showSubTabs('employees'); loadPage('Emp_data.php'); return false;">
            <i class="fas fa-user-tie"></i> Employees
        </a>
        <a href="#" onclick="showSubTabs('jobs'); loadPage('manage_jobs.php'); return false;">
            <i class="fas fa-briefcase"></i> Job Postings
        </a>
        <a href="#" onclick="showSubTabs('interview'); loadPage('interview.php'); return false;">
            <i class="fas fa-comments"></i> Interviews
        </a>
         <a href="#" onclick="showSubTabs('projects'); loadPage('track_project_status.php'); return false;">
            <i class="fas fa-tasks"></i> Projects
        </a>
        <a href="#" onclick="showSubTabs('attendance'); loadPage('admin_attendance.php'); return false;">
            <i class="fas fa-calendar-check"></i> Attendance
        </a>
        <a href="#" onclick="showSubTabs('leaves'); loadPage('admin_leave.php'); return false;">
            <i class="fas fa-plane-departure"></i> Leaves
        </a>
        <a href="#"onclick="showSubTabs('payroll'); loadPage('hr_payroll.php'); return false;">
            <i class="fas fa-money-check-alt"></i> Payroll
        </a>
        <a href="#"onclick="showSubTabs('reports'); loadPage('hr_reports.php'); return false;">
            <i class="fas fa-chart-line"></i> Reports
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>

    <!-- Main Content -->
    <div class="main-content d-flex flex-column">
        <div class="text-center my-3">
            <img src="tech.jpg" alt="Tech Logo" height="60" width="150" />
        </div>

        <div id="subTabs" class="px-3 py-2"></div>

        <iframe id="contentFrame" src="dashboard_overview.php" style="flex-grow: 1;"></iframe>
    </div>

</div>
</body>
</html>
