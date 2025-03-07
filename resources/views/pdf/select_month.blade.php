<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar PDF</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #3a4c72, #3a4c72);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }
        h1 {
            color: #3a4c72;
            margin-bottom: 15px;
            font-weight: 600;
        }
        label {
            display: block;
            font-weight: 500;
            margin-top: 10px;
            color: #333;
        }
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            background: #f9f9f9;
        }
        button, a {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            text-align: center;
        }
        button {
            background: #28a745;
            color: white;
        }
        button:hover {
            background: #218838;
        }
        a {
            background: #dc3545;
            color: white;
        }
        a:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generar PDF</h1>
        <form method="POST" action="{{ route('general_pdf.generate') }}">
            @csrf
            <input type="hidden" name="obraId" value="{{ $obraId }}">
            <label for="month">Mes:</label>
            <select id="month" name="month">
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
            </select>

            <label for="year">Año:</label>
            <select name="year" id="year">
                <?php
                $currentYear = date("Y");
                for ($year = $currentYear; $year >= 2020; $year--) {
                    echo "<option value='$year'>$year</option>";
                }
                ?>
            </select>

            <button type="submit">Generar PDF</button>
        </form>
        <a href="{{ route('obra.show', ['id' => $obraId]) }}">Regresar</a>
    </div>
</body>
</html>