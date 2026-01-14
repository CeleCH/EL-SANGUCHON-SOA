<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador | El Sanguchón</title>
    <link rel="stylesheet" href="../public/css/estilos.css">
</head>
<body class="fondo-catalogo">

<!-- 🔐 PROTECCIÓN ADMIN -->
<script>
const usuario = JSON.parse(localStorage.getItem("usuario"));

if (!usuario || usuario.rol !== "admin") {
    window.location.href = "login.php";
}
</script>

<!-- HEADER -->
<header class="topbar">
    <h2>🧑‍💼 Panel Administrador</h2>
</header>

<!-- CONTENIDO -->
<main class="panel-admin">

    <!-- DASHBOARD -->
    <section class="dashboard">
        <div class="card-dashboard">
            <h4>Total pedidos</h4>
            <p id="totalPedidos">0</p>
        </div>

        <div class="card-dashboard">
            <h4>Ventas totales</h4>
            <p>S/ <span id="ventasTotales">0.00</span></p>
        </div>

        <div class="card-dashboard">
            <h4>Pedidos entregados</h4>
            <p id="entregados">0</p>
        </div>
    </section>

    <!-- GRÁFICA -->
   <section class="graficas-admin">
    <div class="grafica">
        <canvas id="graficoVentas"></canvas>
    </div>
    <div class="grafica">
        <canvas id="graficoCircular"></canvas>
    </div>
</section>


    <!-- EXPORTAR -->
    <button class="btn-excel" onclick="exportarExcel()">
        📊 Exportar a Excel
    </button>

<div class="filtro-tabla">
    <input 
        type="text" 
        id="buscarPedido" 
        placeholder="Buscar por cliente o estado..."
        onkeyup="filtrarTabla()"
    >
</div>


    <!-- TABLA -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="tabla-pedidos"></tbody>
    </table>



<hr style="margin:40px 0">

<h3>📦 Gestión de productos</h3>

<form class="form-producto" id="formProducto" enctype="multipart/form-data">
    <div class="campo">
        <label>📦 Nombre</label>
        <input name="nombre" required>
    </div>

    <div class="campo">
        <label>💰 Precio</label>
        <input name="precio" type="number" step="0.01" required>
    </div>

    <div class="campo">
        <label>📝 Descripción</label>
        <textarea 
            name="descripcion" 
            rows="3" 
            placeholder="Describe el producto..."
            required
        ></textarea>
    </div>

    <div class="campo">
        <label>🖼 Imagen</label>
        <input name="imagen" type="file" accept="image/*" required>
    </div>

    <button type="submit" class="btn-agregar">
        ➕ Agregar producto
    </button>
</form>




<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody id="tabla-productos"></tbody>
</table>



</main>

<!-- 📊 LIBRERÍA GRÁFICAS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JS -->
<script>
let pedidosGlobal = [];

// CARGAR PEDIDOS
fetch("../api/pedidos.php?accion=listar")
    .then(res => res.json())
    .then(data => {
        pedidosGlobal = data;
        cargarTabla(data);
        cargarDashboard(data);
        cargarGrafica(data);
        cargarGraficoCircular(data);

    });

// TABLA
function cargarTabla(data) {
    let html = "";
    data.forEach(p => {
        html += `
            <tr>
                <td>${p.id}</td>
                <td>${p.cliente}</td>
                <td>S/ ${p.total}</td>

                <td>
    <span class="estado-${p.estado}">
        ${p.estado}
    </span>
</td>

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

function filtrarTabla() {
    const texto = document.getElementById("buscarPedido").value.toLowerCase();
    const filtrados = pedidosGlobal.filter(p =>
        p.cliente.toLowerCase().includes(texto) ||
        p.estado.toLowerCase().includes(texto)
    );
    cargarTabla(filtrados);
}

// DASHBOARD
function cargarDashboard(data) {
    document.getElementById("totalPedidos").innerText = data.length;

    let totalVentas = 0;
    let entregados = 0;

    data.forEach(p => {
        totalVentas += parseFloat(p.total);
        if (p.estado === "entregado") entregados++;
    });

    document.getElementById("ventasTotales").innerText = totalVentas.toFixed(2);
    document.getElementById("entregados").innerText = entregados;
}

// GRÁFICA
function cargarGrafica(data) {
    const estados = {
        pendiente: 0,
        preparando: 0,
        entregado: 0
    };

    data.forEach(p => estados[p.estado]++);

    new Chart(document.getElementById("graficoVentas"), {
        type: "bar",
        data: {
            labels: ["Pendiente", "Preparando", "Entregado"],
            datasets: [{
                label: "Pedidos",
                data: [
                    estados.pendiente,
                    estados.preparando,
                    estados.entregado
                ],
                backgroundColor: [
                    "#ffc107", // pendiente
                    "#17a2b8", // preparando
                    "#28a745"  // entregado
                ],
                borderRadius: 8
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function cargarGraficoCircular(data) {
    const estados = { pendiente: 0, preparando: 0, entregado: 0 };

    data.forEach(p => estados[p.estado]++);

    new Chart(document.getElementById("graficoCircular"), {
        type: "doughnut",
        data: {
            labels: ["Pendiente", "Preparando", "Entregado"],
            datasets: [{
                data: [
                    estados.pendiente,
                    estados.preparando,
                    estados.entregado
                ],
                backgroundColor: ["#ffc107", "#17a2b8", "#28a745"]
            }]
        }
    });
}


// CAMBIAR ESTADO
function cambiarEstado(id, estado) {
    fetch("../api/pedidos.php?accion=estado", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            pedido_id: id,
            estado: estado
        })
    })
    .then(() => alert("Estado actualizado"));
}

// EXPORTAR EXCEL
function exportarExcel() {
    let csv = "ID,Cliente,Total,Estado\n";

    pedidosGlobal.forEach(p => {
        csv += `${p.id},${p.cliente},${p.total},${p.estado}\n`;
    });

    const blob = new Blob([csv], { type: "text/csv" });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = "ventas_el_sanguchon.csv";
    a.click();
}

// ================= PRODUCTOS =================

// CARGAR PRODUCTOS
fetch("../api/productos.php")
    .then(res => res.json())
    .then(data => {
        let html = "";
        data.forEach(p => {
            html += `
                <tr>
                    <td>${p.nombre}</td>
                    <td>S/ ${p.precio}</td>
                    <td>
                        <button onclick="eliminarProducto(${p.id})">❌</button>
                    </td>
                </tr>
            `;
        });
        document.getElementById("tabla-productos").innerHTML = html;
    });

// AGREGAR PRODUCTO
function agregarProducto() {
    const nombre = document.getElementById("nombreProd").value;
    const precio = document.getElementById("precioProd").value;
    const imagen = document.getElementById("imgProd").value;

    if (!nombre || !precio) {
        alert("Completa los datos");
        return;
    }

    fetch("../api/productos.php?accion=agregar", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            nombre: nombre,
            precio: precio,
            imagen: imagen
        })
    }).then(() => location.reload());
}

// ELIMINAR PRODUCTO
function eliminarProducto(id) {
    if (!confirm("¿Eliminar producto?")) return;

    fetch("../api/productos.php?accion=eliminar", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
    }).then(() => location.reload());
}

document.getElementById("formProducto").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("../api/productos.php?accion=agregar", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(() => {
        alert("Producto agregado correctamente");
        this.reset();
        cargarProductos(); // refresca la tabla si la tienes
    });
});


</script>

</body>
</html>
