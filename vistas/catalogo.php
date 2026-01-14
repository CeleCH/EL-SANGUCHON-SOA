<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>El Sanguchón | Catálogo</title>
    <link rel="stylesheet" href="../public/css/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="fondo-catalogo">

<header class="topbar">
    <div class="logo">
        🍔 <strong>El Sanguchón</strong>
    </div>

    <nav class="menu">
        <button onclick="mostrarCatalogo()">Catálogo</button>
        <button onclick="mostrarPromos()">Promos</button>
        <button onclick="mostrarNovedades()">Novedades</button>
        <button onclick="mostrarContacto()">Contáctanos</button>
    </nav>


     <div class="acciones-topbar">
        <a 
            href="https://wa.me/51923015606?text=Hola%20quiero%20hacer%20un%20pedido%20en%20El%20Sanguchón"
            target="_blank"
            class="btn-whatsapp"
            title="Escríbenos por WhatsApp"
        >
            🟢 WhatsApp
        </a>

        <button class="btn-carrito" onclick="toggleCarrito()">
            🛒 (<span id="contador">0</span>)
        </button>
    </div>
</header>

    <section class="hero">
    <div class="hero-overlay"></div>

    <div class="hero-contenido">
        <h1>🍔 El Sanguchón</h1>
        <p>El sabor que te llena de verdad</p>

        <div class="hero-buscador">
            <input 
                type="text"
                id="buscar"
                placeholder="¿Qué se te antoja hoy?"
                onkeyup="filtrarProductos()"
            >
            <button onclick="document.getElementById('productos').scrollIntoView({behavior:'smooth'})">
                Ordena aquí
            </button>
        </div>
    </div>
</section>

 

    <button class="btn-carrito" onclick="toggleCarrito()">
        🛒 Carrito (<span id="contador">0</span>)
    </button>
</header>



<main class="contenedor-productos" id="productos"></main>
   

<!-- Carrito lateral -->
<aside id="carrito" class="carrito">
    <h3>🛒 Tu pedido</h3>
    <div id="lista-carrito"></div>
    <p class="total">Total: S/ <span id="total">0.00</span></p>
    <button class="btn-pedido" onclick="enviarPedido()">Realizar pedido</button>
</aside>

<script>
let carrito = [];

let productosGlobal = [];

fetch("../api/productos.php")
    .then(res => res.json())
    .then(data => {
        productosGlobal = data;
        mostrarProductos(productosGlobal.slice(0, 6));
    });

function mostrarProductos(lista) {
    let html = "";
    lista.forEach(p => {
        html += `
            <div class="card">
                <img src="../public/img/${p.imagen}">
                <h3>${p.nombre}</h3>
                <p>${p.descripcion}</p>
                <strong>S/ ${p.precio}</strong>
                <button onclick='agregarCarrito(${JSON.stringify(p)})'>
                    Agregar
                </button>
            </div>
        `;
    });
    document.getElementById("productos").innerHTML = html;
}

function filtrarProductos() {
    const texto = document.getElementById("buscar").value.toLowerCase();

    if (texto === "") {
        mostrarProductos(productosGlobal.slice(0, 6));
        return;
    }

    const filtrados = productosGlobal.filter(p =>
        p.nombre.toLowerCase().includes(texto)
    );

    mostrarProductos(filtrados);
}
function mostrarCatalogo() {
    mostrarProductos(productosGlobal);
}

function mostrarPromos() {
    const promos = productosGlobal.filter(p => p.es_promo == 1);
    mostrarProductos(promos);
}

function mostrarNovedades() {
    const novedades = productosGlobal.filter(p => p.novedad == 1);
    mostrarProductos(novedades);
}

function mostrarContacto() {
    Swal.fire({
        title: 'Contáctanos',
        html: `
            📍 Nuevo Chimbote <br><br>
            📞 923 015 606 <br>
            📧 contacto@elsanguchon.com
        `,
        icon: 'info',
        confirmButtonColor: '#ff7a18'
    });
}


function agregarCarrito(producto) {
    const item = carrito.find(p => p.id == producto.id);
    if (item) {
        item.cantidad++;
    } else {
        producto.cantidad = 1;
        carrito.push(producto);
    }
    actualizarCarrito();
}

function actualizarCarrito() {
    let html = "";
    let total = 0;
    carrito.forEach(p => {
        total += p.precio * p.cantidad;
        html += `
            <div class="item-carrito">
                <span>${p.nombre} x${p.cantidad}</span>
                <span>S/ ${(p.precio * p.cantidad).toFixed(2)}</span>
            </div>
        `;
    });

    document.getElementById("lista-carrito").innerHTML = html;
    document.getElementById("total").innerText = total.toFixed(2);
    document.getElementById("contador").innerText = carrito.length;
}

function toggleCarrito() {
    document.getElementById("carrito").classList.toggle("mostrar");
}

function enviarPedido() {
    const usuario = JSON.parse(localStorage.getItem("usuario"));

    fetch("../api/pedidos.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            usuario_id: usuario.id,
            productos: carrito
        })
    })
    .then(res => res.json())
    .then(data => {
        Swal.fire({
    title: '¡Pedido enviado!',
    text: 'Tu pedido fue registrado correctamente 🍔',
    icon: 'success',
    confirmButtonText: 'Genial',
    confirmButtonColor: '#ff7a18'
});

        carrito = [];
        actualizarCarrito();
        toggleCarrito();
    });
}
</script>

</body>
</html>
