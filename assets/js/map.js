document.addEventListener("DOMContentLoaded", function () {
  if (!document.getElementById("mapa")) return;
  const map = L.map("mapa").setView([18.7357, -70.1627], 8);
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);
  const markers = L.markerClusterGroup();
  const incidentModal = new bootstrap.Modal(
    document.getElementById("incidentModal")
  );

  async function cargarIncidencias(params = {}) {
    try {
      let url = (window.basePath || "") + "/api/get_incidents.php";
      const query = new URLSearchParams(params).toString();
      if (query) url += "?" + query;
      const response = await fetch(url);
      const incidencias = await response.json();
      markers.clearLayers();
      incidencias.forEach((inc) => {
        let customIcon;
        if (inc.icono_url) {
          customIcon = L.icon({
            iconUrl: inc.icono_url,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32],
          });
        }
        const marker = L.marker([inc.latitud, inc.longitud], {
          icon: customIcon,
        });
        marker.on("click", () => {
          let fotoHtml = inc.foto
            ? `<img src='${window.basePath}/${inc.foto}' class='img-fluid mb-2' style='max-height:180px;'>`
            : "";
          let redesHtml = inc.redes
            ? `<a href='${inc.redes}' target='_blank'>Ver en redes sociales</a>`
            : "";
          document.getElementById("incidentModalTitle").innerText = inc.titulo;
          document.getElementById("incidentModalBody").innerHTML = `
            ${fotoHtml}
            <p><strong>Descripción:</strong> ${inc.descripcion}</p>
            <p><strong>Tipos:</strong> <span class="badge bg-primary">${inc.tipos.replace(
              /,/g,
              '</span> <span class="badge bg-primary">'
            )}</span></p>
            <p><strong>Ubicación:</strong> ${inc.barrio}, ${inc.municipio}, ${
            inc.provincia
          }</p>
            <p><strong>Fecha:</strong> ${inc.fecha_ocurrencia}</p>
            <hr>
            <p><strong>Víctimas:</strong> ${inc.muertos} muertos, ${
            inc.heridos
          } heridos.</p>
            <p><strong>Pérdida estimada:</strong> RD$ ${Number(
              inc.perdida
            ).toLocaleString()}</p>
            ${redesHtml}
          `;
          incidentModal.show();
        });
        markers.addLayer(marker);
      });
      map.addLayer(markers);
    } catch (error) {
      console.error("Error al cargar incidencias:", error);
    }
  }

  // Filtros
  const filtrosForm = document.getElementById("filtrosForm");
  if (filtrosForm) {
    filtrosForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const params = {
        provincia: document.getElementById("filtroProvincia").value,
        tipo: document.getElementById("filtroTipo").value,
        fecha_inicio: document.getElementById("filtroFechaInicio").value,
        fecha_fin: document.getElementById("filtroFechaFin").value,
        titulo: document.getElementById("filtroTitulo").value,
      };
      cargarIncidencias(params);
    });
    // Cargar todas al limpiar
    filtrosForm.dispatchEvent(new Event("submit"));
  } else {
    cargarIncidencias();
  }
});
