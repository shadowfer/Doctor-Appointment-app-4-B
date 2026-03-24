<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Cita Médica</title>
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
            background: #16213e;
            color: #fff;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 13px;
            margin-top: 10px;
            letter-spacing: 1px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f3460;
            border-left: 4px solid #e94560;
            padding-left: 10px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px 12px;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }
        .info-table td:first-child {
            font-weight: bold;
            color: #333;
            width: 40%;
        }
        .info-table td:last-child {
            color: #555;
        }
        .highlight-box {
            background: #0f3460;
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 25px 0;
        }
        .highlight-box .date {
            font-size: 20px;
            font-weight: bold;
        }
        .highlight-box .time {
            font-size: 16px;
            margin-top: 5px;
            color: #e94560;
        }
        .status-confirmed {
            display: inline-block;
            background: #27ae60;
            color: #fff;
            padding: 4px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: bold;
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
        <div class="badge">COMPROBANTE DE CITA</div>
    </div>

    <div class="highlight-box">
        <div class="date">{{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('l, d \\d\\e F \\d\\e Y') }}</div>
        <div class="time">{{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}</div>
    </div>

    <div class="section">
        <div class="section-title">Datos del Paciente</div>
        <table class="info-table">
            <tr>
                <td>Nombre:</td>
                <td>{{ $appointment->patient->user->name }}</td>
            </tr>
            <tr>
                <td>Correo electr&oacute;nico:</td>
                <td>{{ $appointment->patient->user->email }}</td>
            </tr>
            <tr>
                <td>Tel&eacute;fono:</td>
                <td>{{ $appointment->patient->user->phone ?? 'No registrado' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Datos del Doctor</div>
        <table class="info-table">
            <tr>
                <td>Doctor:</td>
                <td>Dr(a). {{ $appointment->doctor->user->name }}</td>
            </tr>
            <tr>
                <td>Especialidad:</td>
                <td>{{ $appointment->doctor->speciality->name ?? 'General' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalles de la Cita</div>
        <table class="info-table">
            <tr>
                <td>Fecha:</td>
                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Hora inicio:</td>
                <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</td>
            </tr>
            <tr>
                <td>Hora fin:</td>
                <td>{{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}</td>
            </tr>
            <tr>
                <td>Duraci&oacute;n:</td>
                <td>{{ $appointment->duration ?? 15 }} minutos</td>
            </tr>
            <tr>
                <td>Motivo:</td>
                <td>{{ $appointment->reason ?? 'No especificado' }}</td>
            </tr>
            <tr>
                <td>Estado:</td>
                <td>
                    @php
                        $statusMap = [
                            1 => 'Programada',
                            2 => 'Completada',
                            3 => 'Cancelada',
                        ];
                        $statusText = $statusMap[$appointment->status] ?? 'Desconocido';
                    @endphp
                    <span class="status-confirmed">{{ $statusText }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p><strong>MediLink</strong> &mdash; Sistema de Gesti&oacute;n de Citas M&eacute;dicas</p>
        <p>Documento generado autom&aacute;ticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Este comprobante es un documento digital v&aacute;lido.</p>
    </div>

</body>
</html>
