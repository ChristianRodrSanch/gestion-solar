<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nuevo mensaje de contacto</title>
</head>
<body>
    <p><strong>Nombre:</strong> {{ $name }}</p>
    <p><strong>Email:</strong> {{ $email }}</p>

    @if (!empty($phone))
        <p><strong>Teléfono:</strong> {{ $phone }}</p>
    @endif

    @if (!empty($subject))
        <p><strong>Asunto:</strong> {{ $subject }}</p>
    @endif

    <p><strong>Mensaje:</strong></p>
    <p>{!! nl2br(e($body)) !!}</p>
</body>
</html>
