<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - CICStem</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5f8d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .dashboard-container {
            background: white;
            border-radius: 24px;
            padding: 60px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        .role-badge {
            display: inline-block;
            padding: 8px 20px;
            background: #28A745;
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 48px;
            color: #212529;
            margin-bottom: 10px;
        }
        .welcome-text {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: left;
        }
        .user-info p {
            margin: 8px 0;
            font-size: 15px;
            color: #495057;
        }
        .user-info strong {
            color: #212529;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="role-badge">🎓 STUDENT</div>
        <h1>Welcome!</h1>
        <p class="welcome-text">You are logged in as a Student</p>
        
        <div class="user-info">
            <p><strong>Name:</strong> {{ Auth::guard('student')->user()->full_name }}</p>
            <p><strong>Email:</strong> {{ Auth::guard('student')->user()->email }}</p>
            <p><strong>SR Code:</strong> {{ Auth::guard('student')->user()->sr_code }}</p>
            <p><strong>Year Level:</strong> {{ Auth::guard('student')->user()->year_level }}</p>
            <p><strong>Course:</strong> {{ Auth::guard('student')->user()->course_program }}</p>
            <p><strong>Status:</strong> <span style="color: #28A745;">{{ Auth::guard('student')->user()->status }}</span></p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</body>
</html>