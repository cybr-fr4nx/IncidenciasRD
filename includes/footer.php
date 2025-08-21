</main>
<footer class="bg-dark text-white text-center p-3 mt-4">
    <p>&copy; <?= date('Y') ?> Incidencias RD. Todos los derechos reservados.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="<?= isset($basePath) ? $basePath : '' ?>/assets/js/main.js"></script>
<?php if (isset($extra_js)) {
    // Reemplazar /assets/ por $basePath/assets/ si es necesario
    echo str_replace('/assets/', (isset($basePath) ? $basePath : '') . '/assets/', $extra_js);
} ?>
</body>

</html>