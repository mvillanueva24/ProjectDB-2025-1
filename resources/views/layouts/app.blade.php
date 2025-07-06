<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Películas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: #f4f6f8; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #0001; margin-top: 40px; }
        h1, h2 { color: #007bff; }
        .btn-primary { background: #007bff; border: none; }
        .btn-warning { color: #fff; background: #ffc107; border: none; }
        .btn-danger { background: #dc3545; border: none; }
        .btn-success { background: #28a745; border: none; }
        .btn-info { background: #17a2b8; border: none; }
        .form-group label { font-weight: 500; }
        .table th, .table td { vertical-align: middle; }
        .btn-group .btn { margin-left: 5px; }
        
        /* Estilos para el dropdown */
        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: none;
        }
        .dropdown-header {
            font-weight: 600;
            color: #007bff;
            background-color: #f8f9fa;
        }
        .dropdown-item {
            padding: 8px 20px;
            transition: background-color 0.2s;
        }
        .dropdown-item:hover {
            background-color: #e9ecef;
        }
        .dropdown-item i {
            margin-right: 8px;
            width: 16px;
            text-align: center;
        }
        .dropdown-divider {
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html> 