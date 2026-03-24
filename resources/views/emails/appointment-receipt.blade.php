<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Cita Médica</title>
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
            margin-bottom: 20px;
        }
        .info-card {
            background: #f8f9ff;
            border-left: 4px solid #e94560;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .info-card p {
            margin: 5px 0;
            font-size: 14px;
            color: #444;
        }
        .info-card strong {
            color: #0f3460;
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
            <h1>MediLink</h1>
            @if($recipientType === 'doctor')
                <p>Notificaci&oacute;n de nueva cita asignada</p>
            @else
                <p>Tu cita ha sido confirmada</p>
            @endif
        </div>

        <div class="email-body">
            <p class="greeting">
                @if($recipientType === 'doctor')
                    Estimado(a) <strong>Dr(a). {{ $appointment->doctor->user->name }}</strong>,
                @else
                    Estimado(a) paciente <strong>{{ $appointment->patient->user->name }}</strong>,
                @endif
            </p>

            <p style="font-size: 14px; color: #555; margin-bottom: 20px;">
                @if($recipientType === 'doctor')
                    Se le ha asignado una nueva cita m&eacute;dica en el sistema MediLink. A continuaci&oacute;n encontrar&aacute; los detalles y el comprobante en PDF adjunto.
                @else
                    Se ha registrado exitosamente su cita m&eacute;dica en el sistema MediLink. Adjunto a este correo encontrar&aacute; el comprobante en formato PDF con todos los detalles.
                @endif
            </p>

            <div class="info-card">
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}</p>
                <p><strong>Hora:</strong> {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}</p>

                @if($recipientType === 'doctor')
                    <p><strong>Paciente:</strong> {{ $appointment->patient->user->name }}</p>
                    <p><strong>Motivo:</strong> {{ $appointment->reason ?? 'No especificado' }}</p>
                @else
                    <p><strong>Doctor:</strong> Dr(a). {{ $appointment->doctor->user->name }}</p>
                    <p><strong>Especialidad:</strong> {{ $appointment->doctor->speciality->name ?? 'General' }}</p>
                @endif
            </div>

            <div class="cta-text">
                <strong>El comprobante PDF se encuentra adjunto a este correo.</strong><br>
                Por favor, cons&eacute;rvelo como referencia.
            </div>
        </div>

        <div class="email-footer">
            <p>Este es un correo autom&aacute;tico generado por MediLink.</p>
            <p>Por favor, no responda a este mensaje.</p>
        </div>
    </div>

</body>
</html>
