<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        php-elasticsearch-vue
    </title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" rel="stylesheet">

</head>

<script>
    window.Laravel = <?php echo json_encode([
        'csrfToken' => csrf_token(),
    ]); ?>
</script>
<body>

<div id="app">

</div>

<script src="{{ mix('js/manifest.js') }}"></script>
<script src="{{ mix('js/vendor.js') }}"></script>
<script src="{{ mix('js/app.js') }}"></script>

</body>
</html>