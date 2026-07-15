<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Inicio J - Jose Manuel Rojas</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
                min-height: 100vh;
                color: #fff;
            }
            .hero-card {
                border: none;
                border-radius: 2rem;
                background: rgba(255, 255, 255, 0.12);
                backdrop-filter: blur(18px);
                box-shadow: 0 24px 65px rgba(0, 0, 0, 0.18);
            }
            .badge-custom {
                background: rgba(255, 255, 255, 0.18);
                color: #fff;
            }
            .btn-outline-light {
                border-color: rgba(255, 255, 255, 0.75);
            }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="card hero-card p-4 p-lg-5 text-center text-white">
                        <div class="mb-4">
                            <span class="badge badge-custom px-3 py-2 text-uppercase fw-semibold">José Manuel Rojas</span>
                        </div>
                        <h1 class="display-5 fw-bold mb-3">Bienvenido a tu panel personal</h1>
                        <p class="lead mb-4 opacity-85">Explora la página de prueba creada para ti con un estilo moderno, claro y enfocado en experiencia.</p>

                        @if (session('message'))
                            <div class="alert alert-light text-dark shadow-sm rounded-4">
                                {{ session('message') }}
                            </div>
                        @endif

                        <form action="{{ route('inicioJ.store') }}" method="POST" class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                            @csrf
                            <button type="submit" class="btn btn-light btn-lg px-4 rounded-pill">Probar controlador</button>
                        </form>

                        <div class="mt-5 text-start text-white-75">
                            <h6 class="mb-2">Detalles</h6>
                            <p class="mb-1">Controlador: <strong>InicioJController</strong></p>
                            <p class="mb-0">Ruta de prueba: <strong>/inicioJ</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
