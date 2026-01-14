function login() {
    const correo = document.getElementById("correo").value;
    const password = document.getElementById("password").value;

    fetch("../api/auth.php?accion=login", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            correo: correo,
            password: password
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.exito) {
            localStorage.setItem("usuario", JSON.stringify(data.usuario));

            if (data.usuario.rol === "admin") {
                           
                window.location.href = "administrador.php";
            } else {
                window.location.href = "catalogo.php";
            }

        } else {
            document.getElementById("mensaje").innerText = "Credenciales incorrectas";
        }
    })
    .catch(() => {
        document.getElementById("mensaje").innerText = "Error de conexión";
    });
}
