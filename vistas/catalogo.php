<?php
require_once '../cn.php';
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}
$database = new cn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - Sistema de Préstamos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           🎨 PALETA ITCA-FEPADE
           ------------------------------------------------------------
           - Vino Principal:        #8B0000
           - Rojo Ladrillo:         #9B001F
           - Dorado/Ocre:           #B38C00
           - Café Suave:            #6F4E37
           - Fondo Claro:           #F8F5F0
           - Texto Oscuro:          #2B2B2B
           ============================================================ */
        :root {
            --vino: #8B0000;
            --rojo-ladrillo: #9B001F;
            --dorado: #B38C00;
            --cafe: #6F4E37;
            --fondo-claro: #F8F5F0;
            --texto-oscuro: #2B2B2B;
            --sombra-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--fondo-claro);
            color: var(--texto-oscuro);
            margin: 0;
            padding: 0;
        }

        header {
            background-color: var(--vino);
            color: white;
            text-align: center;
            padding: 40px 20px;
            box-shadow: var(--sombra-lg);
        }

        header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 2.5rem;
        }

        .controls-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 20px 20px;
            max-width: 1200px;
            margin: 0 auto;
            gap: 20px;
        }

        #search-input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid var(--cafe);
            font-size: 1rem;
            background-color: #fff;
            transition: 0.3s;
        }

        #search-input:focus {
            border-color: var(--vino);
            box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.15);
            outline: none;
        }

        .back-btn {
            background-color: var(--rojo-ladrillo);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: 0.3s;
            display: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        }

        .back-btn:hover {
            background-color: var(--vino);
            transform: translateY(-1px);
        }

        .categorias {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .categoria {
            background-color: #fff;
            width: 220px;
            padding: 25px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
            border: 2px solid transparent;
            color: var(--vino);
        }

        .categoria:hover {
            transform: translateY(-5px);
            box-shadow: var(--sombra-lg);
            border-color: var(--dorado);
        }

        .productos {
            display: none;
            padding: 30px;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .productos.show {
            display: flex;
        }

        .producto {
            background-color: #fff;
            width: 280px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 25px 20px;
            transition: 0.3s;
        }

        .producto:hover {
            box-shadow: var(--sombra-lg);
        }

        .producto h3 {
            margin: 0 0 10px;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--vino);
        }

        .producto p {
            font-size: 0.9rem;
            color: #5a5a5a;
            margin-bottom: 15px;
            min-height: 40px;
            line-height: 1.4;
        }

        .producto button {
            background-color: var(--dorado);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
            width: 100%;
            font-size: 0.95rem;
        }

        .producto button:hover {
            background-color: var(--rojo-ladrillo);
        }

        .stock-info {
            font-size: 0.8rem;
            margin: 10px 0;
            padding: 5px;
            border-radius: 4px;
            background-color: var(--fondo-claro);
        }

        .stock-disponible {
            color: var(--rojo-ladrillo);
            font-weight: 600;
        }

        .stock-agotado {
            color: var(--vino);
            font-weight: 600;
        }

        .btn-disabled {
            background-color: #b3b3b3 !important;
            cursor: not-allowed !important;
        }

        .icono-categoria {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 2rem;
            }
            .controls-container {
                flex-direction: column;
            }
            .back-btn {
                width: 100%;
            }
            .categoria, .producto {
                width: 100%;
                max-width: 350px;
            }
        }
    </style>
</head>
<body>
<header><h1>Catálogo de Productos</h1></header>

<div class="controls-container">
    <button class="back-btn" onclick="volverCategorias()">← Volver a Categorías</button>
    <div class="search-container">
        <input type="text" id="search-input" placeholder="Buscar producto por nombre o descripción..." onkeyup="filtrarProductosGlobal()">
    </div>
</div>

<div class="categorias" id="categorias">
    <?php
    $sql_categorias = "SELECT id_categoria, nombre_categoria FROM categorias";
    $result_categorias = $database->consulta($sql_categorias);
    if ($result_categorias && $result_categorias->num_rows > 0) {
        while($categoria = $result_categorias->fetch_assoc()) {
            $icono = $categoria['id_categoria'] == 1 ? '🏗️' : '🛠️';
            echo '<div class="categoria" data-category="'.$categoria['id_categoria'].'" onclick="mostrarProductos('.$categoria['id_categoria'].')">
                    <span class="icono-categoria">'.$icono.'</span>
                    '.htmlspecialchars($categoria['nombre_categoria']).'
                  </div>';
        }
    } else {
        echo '<p style="text-align: center; width: 100%; color: #666;">No hay categorías disponibles</p>';
    }
    ?>
</div>

<?php
$sql_categorias_productos = "SELECT id_categoria, nombre_categoria FROM categorias";
$result_cat_prod = $database->consulta($sql_categorias_productos);
if ($result_cat_prod && $result_cat_prod->num_rows > 0) {
    while($cat = $result_cat_prod->fetch_assoc()) {
        echo '<div class="productos" id="'.$cat['id_categoria'].'">';
        $sql_productos = "SELECT p.id_producto, p.nombre_producto as nombre, p.descripcion, p.stock, p.precio, p.tipo_producto
                          FROM productos p 
                          WHERE p.id_categoria = ".$cat['id_categoria']." AND p.estado = 'Disponible'";
        $result_productos = $database->consulta($sql_productos);
        if ($result_productos && $result_productos->num_rows > 0) {
            while($producto = $result_productos->fetch_assoc()) {
                $stock_class = $producto['stock'] > 0 ? 'stock-disponible' : 'stock-agotado';
                $btn_class = $producto['stock'] > 0 ? '' : 'btn-disabled';
                $btn_text = $producto['stock'] > 0 ? 'Solicitar Préstamo' : 'Sin Stock';
                $onclick = $producto['stock'] > 0 ? 'prestar('.$producto['id_producto'].')' : '';
                $icono_producto = $producto['tipo_producto'] == 'Perecedero' ? '⏰' : '📦';
                echo '<div class="producto" data-name="'.htmlspecialchars($producto['nombre']).'" 
                        data-desc="'.htmlspecialchars($producto['descripcion']).'" 
                        data-id="'.$producto['id_producto'].'">
                        <div style="font-size: 1.5rem; margin-bottom: 10px;">'.$icono_producto.'</div>
                        <h3>'.htmlspecialchars($producto['nombre']).'</h3>
                        <p>'.htmlspecialchars($producto['descripcion']).'</p>
                        <div class="stock-info '.$stock_class.'">
                            Disponible: '.$producto['stock'].' unidades<br>
                            Precio: $'.number_format($producto['precio'], 2).'
                        </div>
                        <button class="'.$btn_class.'" onclick="'.$onclick.'">'.$btn_text.'</button>
                      </div>';
            }
        } else {
            echo '<p style="text-align: center; width: 100%; color: #666;">No hay productos disponibles</p>';
        }
        echo '</div>';
    }
}
?>
<script>
let categoriaActiva = null;
const categoriasContainer = document.getElementById('categorias');
const backBtn = document.querySelector('.back-btn');
const searchInput = document.getElementById('search-input');
const allProductsContainers = document.querySelectorAll('.productos');

function mostrarProductos(id) {
    categoriasContainer.style.display = 'none';
    allProductsContainers.forEach(p => p.classList.remove('show'));
    const productosContainer = document.getElementById(id.toString());
    if (!productosContainer) return;
    productosContainer.classList.add('show');
    backBtn.style.display = 'inline-block';
    categoriaActiva = id;
    searchInput.value = '';
}

function volverCategorias() {
    allProductsContainers.forEach(p => p.classList.remove('show'));
    categoriasContainer.style.display = 'flex';
    backBtn.style.display = 'none';
    categoriaActiva = null;
    searchInput.value = '';
}

function filtrarProductosGlobal() {
    const filtro = searchInput.value.toLowerCase().trim();
    if (filtro === '') {
        categoriaActiva ? mostrarProductos(categoriaActiva) : volverCategorias();
        return;
    }
    categoriasContainer.style.display = 'none';
    backBtn.style.display = 'inline-block';
    allProductsContainers.forEach(container => {
        container.querySelectorAll('.producto').forEach(producto => {
            const nombre = producto.getAttribute('data-name')?.toLowerCase() || '';
            const desc = producto.getAttribute('data-desc')?.toLowerCase() || '';
            producto.style.display = (nombre.includes(filtro) || desc.includes(filtro)) ? 'block' : 'none';
        });
    });
}
function prestar(id) { window.location.href = 'crearPrestamo.php?id_producto=' + id; }
document.addEventListener('DOMContentLoaded', volverCategorias);
</script>
</body>
</html>
