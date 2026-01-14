
<script>
let pedidosGlobal = [];

fetch("../api/pedidos.php?accion=listar")
    .then(res => res.json())
    .then(data => {
        pedidosGlobal = data;
        cargarTabla(data);
        cargarDashboard(data);
        cargarGrafica(data);
    });

function cargarTabla(data) {
    let html = "";
    data.forEach(p => {
        html += `
            <tr>
                <td>${p.id}</td>
                <td>${p.cliente}</td>
                <td>S/ ${p.total}</td>
                <td>${p.estado}</td>
                <td>
                    <select onchange="cambiarEstado(${p.id}, this.value)">
                        <option value="pendiente" ${p.estado=='pendiente'?'selected':''}>Pendiente</option>
                        <option value="preparando" ${p.estado=='preparando'?'selected':''}>Preparando</option>
                        <option value="entregado" ${p.estado=='entregado'?'selected':''}>Entregado</option>
                    </select>
                </td>
            </tr>
        `;
    });
    document.getElementById("tabla-pedidos").innerHTML = html;
}
