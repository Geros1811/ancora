

<!DOCTYPE html>
<html>
<head>
    <title>Select Month</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        select {
            width: 200px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Select Month</h1>
    <form method="POST" action="{{ route('general_pdf.generate') }}" >
        @csrf
        <input type="hidden" name="obraId" value="{{ $obraId }}">
        <label for="month">Month:</label>
        <select id="month" name="month" >
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
        
        <label for="year">Year:</label>
        <select name="year" id="year">
            <?php
            $currentYear = date("Y");
            for ($year = $currentYear; $year >= 2020; $year--) {
                echo "<option value='$year'>$year</option>";
            }
            ?>
        </select>

        <button type="submit">Generate PDF</button>
    </form>
    <a href="{{ route('obra.show', ['id' => $obraId]) }}" style="display: inline-block; margin-top: 10px; padding: 8px 12px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">Regresar a Detalles de Obra</a>
</body>
</html>
