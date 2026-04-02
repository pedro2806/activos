<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir CSV Clientes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Importar Clientes desde CSV</h5>
            </div>
            <div class="card-body">
                <form id="formSubirCsv">
                    <div class="mb-3">
                        <label class="form-label">Selecciona el archivo DimClientes.csv</label>
                        <input type="file" class="form-control" id="archivo_csv" accept=".csv" required>
                    </div>
                    <button type="submit" id="btnSubir" class="btn btn-primary">Subir e Importar</button>
                </form>
                <div id="status" class="mt-3 alert d-none"></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('formSubirCsv').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('btnSubir');
            const msg = document.getElementById('status');
            const fileInput = document.getElementById('archivo_csv');

            btn.disabled = true;
            btn.innerText = 'Procesando archivo...';

            const fd = new FormData();
            fd.append('action', 'subir_csv_clientes');
            fd.append('archivo_csv', fileInput.files[0]);

            try {
                const response = await fetch('acciones_clientes.php', {
                    method: 'POST',
                    body: fd
                });

                const result = await response.json();

                msg.className = `mt-3 alert alert-${result.status === 'success' ? 'success' : 'danger'}`;
                msg.innerText = result.message;
                msg.classList.remove('d-none');

            } catch (error) {
                msg.className = 'mt-3 alert alert-danger';
                msg.innerText = "Error en la petición.";
            } finally {
                btn.disabled = false;
                btn.innerText = 'Subir e Importar';
            }
        });
    </script>
</body>
</html>