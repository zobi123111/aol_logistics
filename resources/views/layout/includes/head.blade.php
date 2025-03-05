<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    

    <title>{{ env('PROJECT_NAME')}}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ url('assets/img/SiteLogo.png') }}" rel="icon">
    <link href="{{ url('assets/img/SiteLogo.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- CSS Files (Styles) -->
    <link href="{{ url('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ url('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
   
    
    <link href="{{ url('assets/css/style.css') }}" rel="stylesheet">
 
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        (function() {
            const savedMode = localStorage.getItem('darkMode');

            if (savedMode === 'enabled') {
                document.documentElement.classList.add('dark-mode');
                
            } else {
                document.documentElement.classList.remove('dark-mode');
            }
        })();
  
        // function toggleMode () {
        //     const modeIcon = document.getElementById('mode-icon');

        //     const currentMode = document.documentElement.classList.contains('dark-mode') ? 'enabled' : 'disabled';

        //     console.log(currentMode);
        //     if (currentMode === 'enabled') {

        //         document.documentElement.classList.remove('dark-mode');
        //         localStorage.setItem('darkMode', 'disabled');
        //     } else {
        //         document.documentElement.classList.add('dark-mode');
        //         localStorage.setItem('darkMode', 'enabled');
        //     }
        // }

        // window.onload = function() {
        //     const modeButton = document.getElementById('mode-button');
        //     if (modeButton) {
        //         modeButton.addEventListener('click', toggleMode);
        //     }
        // };

        function toggleMode() {
            const modeIcon = document.getElementById('mode-icon');
            
            document.documentElement.classList.toggle('dark-mode');
            
            if (document.documentElement.classList.contains('dark-mode')) {
                modeIcon.classList.remove('bi-moon');
                modeIcon.classList.add('bi-sun');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                modeIcon.classList.remove('bi-sun');
                modeIcon.classList.add('bi-moon');
                localStorage.setItem('darkMode', 'disabled');
            }
        }

        window.onload = function() {
            const modeIcon = document.getElementById('mode-icon');
            if (localStorage.getItem('darkMode') === 'enabled') {
                document.documentElement.classList.add('dark-mode');
                modeIcon.classList.remove('bi-moon');
                modeIcon.classList.add('bi-sun');
            } else {
                document.documentElement.classList.remove('dark-mode');
                modeIcon.classList.remove('bi-sun');
                modeIcon.classList.add('bi-moon');
            }

            const modeButton = document.getElementById('mode-button');
            if (modeButton) {
                modeButton.addEventListener('click', toggleMode);
            }
        };

    </script>

</head>