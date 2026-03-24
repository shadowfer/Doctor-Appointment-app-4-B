<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario de Citas - {{ $date }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1a2e;
            background: #ffffff;
            padding: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #0f3460;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 28px;
            color: #0f3460;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            color: #666;
        }
        .badge {
            display: inline-block;
            background: #e94560;
            color: #fff;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 13px;
            margin-top: 10px;
            letter-spacing: 1px;
        }
        .subtitle {
            text-align: center;
            font-size: 18px;
            color: #16213e;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .recipient {
            text-align: center;
            font-size: 14px;
            color: #555;
            margin-bottom: 25px;
        }
        .stats {
            background: #0f3460;
            color: #fff;
            padding: 15px 30px;
            border-radius: 10px;
            text-align: center;
            margin: 0 auto 25px auto;
            width: 100%;
        }
        .stats .number {
            font-size: 36px;
            font-weight: bold;
        }
        .stats .label {
            font-size: 14px;
            color: #ccc;
        }
        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .appointments-table th {
            background: #0f3460;
            color: #fff;
            padding: 10px 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .appointments-table td {
            padding: 10px 12px;
            font-size: 13px;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        .appointments-table tr:nth-child(even) {
            background: #f8f9ff;
        }
        .empty-message {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 30px;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            font-size: 11px;
            color: #999;
        }
        .footer p { margin-bottom: 3px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>MediLink</h1>
        <p>Sistema de Gesti&oacute;n de Citas M&eacute;dicas</p>
        <div class="badge">REPORTE DIARIO</div>
    </div>

    <div class="subtitle">
        Citas programadas para el {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d \\d\\e F \\d\\e Y') }}
    </div>

    <div class="recipient">
        Destinatario: <strong>{{ $recipientName }}</strong>
    </div>

    <div class="stats">
        <div class="number">{{ $appointments->count() }}</div>
        <div class="label">Citas Programadas</div>
    </div>

    @if($appointments->count() > 0)
        <table class="appointments-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Doctor</th>
                    <th>Especialidad</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $index => $appointment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</td>
                        <td>{{ $appointment->patient->user->name }}</td>
                        <td>Dr(a). {{ $appointment->doctor->user->name }}</td>
                        <td>{{ $appointment->doctor->speciality->name ?? 'General' }}</td>
                        <td>{{ $appointment->reason ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-message">
            No hay citas programadas para este d&iacute;a.
        </div>
    @endif

    <div class="footer">
        <p><strong>MediLink</strong> &mdash; Sistema de Gesti&oacute;n de Citas M&eacute;dicas</p>
        <p>Reporte generado autom&aacute;ticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

</body>
</html>
