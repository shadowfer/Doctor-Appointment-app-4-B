<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario de Citas</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .email-header {
            background: linear-gradient(135deg, #0f3460 0%, #16213e 100%);
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }
        .email-header h1 {
            margin: 0 0 5px 0;
            font-size: 26px;
        }
        .email-header p {
            margin: 0;
            font-size: 14px;
            color: #a0c4e8;
        }
        .email-body {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            color: #333;
            margin-bottom: 15px;
        }
        .summary-box {
            background: linear-gradient(135deg, #0f3460 0%, #16213e 100%);
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .summary-box .count {
            font-size: 36px;
            font-weight: bold;
        }
        .summary-box .label {
            font-size: 14px;
            color: #a0c4e8;
        }
        .info-text {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }
        .cta-text {
            font-size: 14px;
            color: #666;
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #fff8e1;
            border-radius: 6px;
        }
        .email-footer {
            text-align: center;
            padding: 20px;
            background: #f4f6f9;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="email-wrapper">
        <div class="email-header">
            <h1>🏥 MediLink</h1>
            <p>Reporte Diario de Citas</p>
        </div>

        <div class="email-body">
            <p class="greeting">
                Hola {{ $recipientName }},
            </p>
            <p class="info-text">
                A continuación se presenta el resumen de citas programadas para el día <strong>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong>.
            </p>

            <div class="summary-box">
                <div class="count">{{ $appointments->count() }}</div>
                <div class="label">Citas programadas para hoy</div>
            </div>

            @if($appointments->count() > 0)
                <p class="info-text">
                    Revisa el PDF adjunto para ver el listado completo con horarios, pacientes y doctores asignados.
                </p>
            @else
                <p class="info-text" style="text-align: center; font-style: italic;">
                    No hay citas programadas para este día.
                </p>
            @endif

            <div class="cta-text">
                📎 <strong>El reporte detallado en PDF se encuentra adjunto a este correo.</strong>
            </div>
        </div>

        <div class="email-footer">
            <p>Este es un correo automático generado por MediLink.</p>
            <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

</body>
</html>
