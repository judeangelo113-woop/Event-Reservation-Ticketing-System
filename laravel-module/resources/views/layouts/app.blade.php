
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Event Ticketing System')</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #333;
        }

        nav {
            background: #1f2937;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .brand {
            color: white;
            font-size: 20px;
            font-weight: bold;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        nav a:hover {
            color: #93c5fd;
        }

        .container {
            max-width: 1150px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        h1 {
            margin-top: 0;
        }

        a.button {
            display: inline-block;
            padding: 9px 14px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a.button:hover {
            background: #1d4ed8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f1f5f9;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 7px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 16px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            padding: 12px;
            margin: 15px 0;
            background: #dcfce7;
            color: #166534;
            border-radius: 5px;
        }

        .error {
            padding: 12px;
            margin: 15px 0;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <nav>
        <div class="brand">Event Ticketing System</div>

        <div>
            <a href="{{ route('events.index') }}">Events</a>
            <a href="{{ route('announcements.index') }}">Announcements</a>
            <a href="http://localhost/EventTicketing/public/dashboard.php">
                Main Dashboard
            </a>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

</body>
</html>
