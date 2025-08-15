<?php
$page_title = 'Mapa de Incidencias';
include 'includes/header.php';
require_once 'includes/db.php';
$provincias = $pdo->query("SELECT * FROM Provincia ORDER BY nombre")->fetchAll();
$tipos = $pdo->query("SELECT * FROM Tipo_Incidencia ORDER BY nombre")->fetchAll();
?>

<div class="p-5 mb-4 bg-light rounded-3">
  <h1 class="display-5 fw-bold">Mapa de Incidencias Recientes</h1>
  <p class="col-md-8 fs-4">Visualización de eventos reportados y validados.</p>
</div>

<div class="mb-3">
  <button class="btn btn-outline-primary mb-2" type="button" id="toggleFiltros">Mostrar Filtros</button>
  <form id="filtrosForm" class="card card-body mb-3" style="display:none;">
    <div class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label mb-0">Provincia</label>
        <select name="provincia" id="filtroProvincia" class="form-select">
          <option value="">Todas</option>
          <?php foreach ($provincias as $prov): ?>
            <option value="<?= $prov['id_provincia'] ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label mb-0">Tipo</label>
        <select name="tipo" id="filtroTipo" class="form-select">
          <option value="">Todos</option>
          <?php foreach ($tipos as $tipo): ?>
            <option value="<?= $tipo['id_tipo'] ?>"><?= htmlspecialchars($tipo['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label mb-0">Rango de Fecha</label>
        <div class="input-group">
          <input type="date" name="fecha_inicio" id="filtroFechaInicio" class="form-control">
          <input type="date" name="fecha_fin" id="filtroFechaFin" class="form-control">
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label mb-0">Título</label>
        <input type="text" name="titulo" id="filtroTitulo" class="form-control" placeholder="Buscar por título">
      </div>
    </div>
    <div class="mt-3 text-end">
      <button type="submit" class="btn btn-primary">Buscar</button>
      <button type="button" class="btn btn-secondary" id="limpiarFiltros">Limpiar</button>
    </div>
  </form>
</div>

<div id="mapa" class="rounded shadow mb-4"></div>

<!-- Modal para detalles -->
<div class="modal fade" id="incidentModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="incidentModalTitle"></h5><button type="button" class="btn-close"
          data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="incidentModalBody"></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php
$extra_js = '<script src="/assets/js/map.js"></script>';
include 'includes/footer.php';
?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('toggleFiltros');
    const form = document.getElementById('filtrosForm');
    btn.addEventListener('click', function () {
      if (form.style.display === 'none') {
        form.style.display = '';
        btn.textContent = 'Ocultar Filtros';
      } else {
        form.style.display = 'none';
        btn.textContent = 'Mostrar Filtros';
      }
    });
    document.getElementById('limpiarFiltros').addEventListener('click', function () {
      form.reset();
      form.dispatchEvent(new Event('submit'));
    });
  });
</script>