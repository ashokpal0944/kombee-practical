<!DOCTYPE html>
<html>
<head>
    <title>User Data</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

    <h1>User Data</h1>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Country</th>
                <th>State</th>
                <th>City</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $user)
                <tr>
                    <td>{{ $user['User ID'] }}</td>
                    <td>{{ $user['First Name'] }}</td>
                    <td>{{ $user['Last Name'] }}</td>
                    <td>{{ $user['Email'] }}</td>
                    <td>{{ $user['Contact Number'] }}</td>
                    <td>{{ $user['Country'] }}</td>
                    <td>{{ $user['State'] }}</td>
                    <td>{{ $user['City'] }}</td>
                    <td>{{ $user['Role'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
