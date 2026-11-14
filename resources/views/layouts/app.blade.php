<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyNet | Control de Clientes y Cuentas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tipografías estilo dibujado a mano -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&family=Patrick+Hand&display=swap" rel="stylesheet">
    @livewireStyles

    <style>
        :root {
            --ink: #1e293b;
            --paper: #fdfbf7;
            --grid: #e2e8f0;
            --blue-sketch: #3b82f6;
            --green-sketch: #10b981;
            --yellow-sketch: #f59e0b;
            --red-sketch: #ef4444;
        }

        body {
            background-color: var(--paper);
            /* Fondo sutil tipo cuaderno cuadriculado */
            background-image: 
                linear-gradient(to right, var(--grid) 1px, transparent 1px),
                linear-gradient(to bottom, var(--grid) 1px, transparent 1px);
            background-size: 24px 24px;
            color: var(--ink);
            font-family: 'Patrick Hand', cursive, sans-serif;
            font-size: 1.12rem;
        }

        h1, h2, h3, h4, .sketch-title {
            font-family: 'Architects Daughter', cursive, sans-serif;
            letter-spacing: 0.5px;
        }

        /* Barra superior dibujada a mano */
        .sketch-navbar {
            background-color: #ffffff;
            border-bottom: 3px solid var(--ink);
            box-shadow: 0 4px 0px var(--ink);
        }

        /* Tarjetas con bordes irregulares estilo Excalidraw */
        .sketch-card {
            background: #ffffff;
            border: 2.5px solid var(--ink);
            border-radius: 255px 15px 225px 15px / 15px 225px 15px 255px;
            box-shadow: 5px 5px 0px var(--ink);
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
        }

        .sketch-card:hover {
            transform: translateY(-3px) rotate(-0.3deg);
            box-shadow: 7px 8px 0px var(--ink);
        }

        .sketch-header-blue {
            background-color: #dbeafe;
            border-bottom: 2.5px solid var(--ink);
            border-radius: 250px 12px 0 0 / 12px 220px 0 0;
            color: var(--ink);
            font-weight: bold;
        }

        .sketch-header-green {
            background-color: #d1fae5;
            border-bottom: 2.5px solid var(--ink);
            border-radius: 250px 12px 0 0 / 12px 220px 0 0;
            color: var(--ink);
            font-weight: bold;
        }

        .sketch-header-plain {
            background-color: #f8fafc;
            border-bottom: 2.5px solid var(--ink);
            border-radius: 250px 12px 0 0 / 12px 220px 0 0;
            color: var(--ink);
            font-weight: bold;
        }

        /* Inputs estilo boceto */
        .sketch-input {
            background-color: #fffef9;
            border: 2px solid var(--ink);
            border-radius: 12px 4px 10px 6px / 6px 10px 4px 12px;
            font-family: 'Patrick Hand', cursive, sans-serif;
            font-size: 1.05rem;
            transition: all 0.15s ease;
        }

        .sketch-input:focus {
            background-color: #ffffff;
            border-color: var(--blue-sketch);
            box-shadow: 3px 3px 0px var(--ink);
            outline: none;
            transform: scale(1.01);
        }

        /* Botones táctiles con microinteracción al hacer clic */
        .sketch-btn {
            border: 2px solid var(--ink);
            border-radius: 15px 5px 12px 6px / 6px 12px 5px 15px;
            box-shadow: 3px 3px 0px var(--ink);
            font-family: 'Architects Daughter', cursive, sans-serif;
            font-weight: bold;
            color: var(--ink);
            transition: transform 0.12s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.12s ease, background-color 0.15s ease;
        }

        .sketch-btn:hover {
            transform: translate(-1px, -2px) rotate(0.8deg);
            box-shadow: 4px 5px 0px var(--ink);
            color: var(--ink);
        }

        .sketch-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 0px 0px 0px var(--ink);
        }

        .btn-sketch-blue { background-color: #93c5fd; }
        .btn-sketch-blue:hover { background-color: #60a5fa; }

        .btn-sketch-green { background-color: #6ee7b7; }
        .btn-sketch-green:hover { background-color: #34d399; }

        .btn-sketch-yellow { background-color: #fcd34d; }
        .btn-sketch-yellow:hover { background-color: #fbbf24; }

        .btn-sketch-red { background-color: #fca5a5; }
        .btn-sketch-red:hover { background-color: #f87171; }

        .btn-sketch-gray { background-color: #e2e8f0; }

        /* Etiquetas y badges */
        .sketch-badge {
            border: 2px solid var(--ink);
            border-radius: 12px 8px 14px 6px;
            background-color: #fef08a;
            color: var(--ink);
            padding: 2px 8px;
            font-weight: bold;
            display: inline-block;
            transform: rotate(-2deg);
        }

        /* Tabla limpia estilo libreta */
        .sketch-table th {
            border-bottom: 2px solid var(--ink) !important;
            font-family: 'Architects Daughter', cursive;
            font-size: 0.95rem;
        }

        .sketch-table td {
            border-bottom: 1.5px dashed #94a3b8 !important;
            transition: background-color 0.15s ease;
        }

        .sketch-table tbody tr {
            animation: popIn 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .sketch-table tbody tr:hover td {
            background-color: #fefce8;
        }

        /* Alerta animada */
        .sketch-alert {
            border: 2px solid var(--ink);
            border-radius: 10px 4px 12px 5px;
            background-color: #dcfce7;
            box-shadow: 3px 3px 0px var(--ink);
            animation: popIn 0.25s ease-out;
        }

        @keyframes popIn {
            0% { opacity: 0; transform: scale(0.95) translateY(6px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
    </style>
</head>
<body>
    <nav class="navbar sketch-navbar mb-4 py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="navbar-brand mb-0 h1 sketch-title fs-4 text-dark">
                ✎ SkyNet <span class="fs-6 fw-normal">| Control de Clientes y Cuentas</span>
            </span>
            <span class="sketch-badge small">Modo Reactivo ⚡</span>
        </div>
    </nav>

    <div class="container pb-5">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>