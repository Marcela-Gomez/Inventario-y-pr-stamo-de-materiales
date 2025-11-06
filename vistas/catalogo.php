
<?php
// catalogo.php - Versión sin imágenes
require_once '../cn.php';
session_start();

// Verificar sesión básica
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Crear instancia de la conexión
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
        /* TUS ESTILOS ORIGINALES COMPLETOS */
        :root {
            --primary-color: #3b82f6; 
            --secondary-color: #10b981; 
            --background-light: #f9fafb;
            --card-background: #ffffff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-light);
            margin: 0;
            padding: 0;
            color: var(--text-dark);
        }

        header {
            background-color: var(--primary-color); 
            color: white;
            text-align: center;
            padding: 40px 20px;
            box-shadow: var(--shadow-lg);
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

        .search-container {
            flex-grow: 1;
        }

        #search-input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        #search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); 
            outline: none;
        }

        .back-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.2s;
            display: none; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .back-btn:hover {
            background-color: #2563eb; 
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
            background-color: var(--card-background);
            width: 220px;
            padding: 25px 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, border 0.2s;
            font-weight: 600;
            font-size: 1.1rem;
            border: 2px solid transparent;
        }

        .categoria:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
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
            background-color: var(--card-background);
            width: 280px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 25px 20px;
            transition: box-shadow 0.3s;
            display: block; 
        }

        .producto:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.07);
        }

        /* QUITAMOS LOS ESTILOS DE IMAGEN */
        .producto h3 {
            margin: 0 0 10px;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary-color);
        }

        .producto p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 15px;
            min-height: 40px; 
            line-height: 1.4;
        }

        .producto button {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
            width: 100%;
            font-size: 0.95rem;
        }

        .producto button:hover {
            background-color: #059669; 
        }

        .no-results {
            display: none !important;
        }

        .stock-info {
            font-size: 0.8rem;
            margin: 10px 0;
            padding: 5px;
            border-radius: 4px;
            background-color: var(--background-light);
        }

        .stock-disponible {
            color: #10b981;
            font-weight: 600;
        }

        .stock-agotado {
            color: #ef4444;
            font-weight: 600;
        }

        .btn-disabled {
            background-color: #9ca3af !important;
            cursor: not-allowed !important;
        }

        .btn-disabled:hover {
            background-color: #9ca3af !important;
            transform: none !important;
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
                position: static;
                margin-top: 10px;
                width: 100%;
            }
            .categoria {
                width: 100%;
                max-width: 300px;
            }
            .producto {
                width: 100%;
                max-width: 350px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Catálogo de Productos</h1>
</header>

<div class="controls-container">
    <button class="back-btn" onclick="volverCategorias()">← Volver a Categorías</button>
    <div class="search-container">
        <input type="text" id="search-input" placeholder="Buscar producto por nombre o descripción..." onkeyup="filtrarProductosGlobal()">
    </div>
</div>

<div class="categorias" id="categorias">
    <?php
    // Cargar categorías desde la base de datos
    $sql_categorias = "SELECT id_categoria, nombre_categoria FROM categorias";
    $result_categorias = $database->consulta($sql_categorias);
    
    if ($result_categorias && $result_categorias->num_rows > 0) {
        while($categoria = $result_categorias->fetch_assoc()) {
            $icono = $categoria['id_categoria'] == 1 ? '🏗️' : '🛠️'; // Material o Herramienta
            echo '<div class="categoria" data-category="'.$categoria['id_categoria'].'" onclick="mostrarProductos('.$categoria['id_categoria'].')">
                    <span class="icono-categoria">'.$icono.'</span>
                    '.htmlspecialchars($categoria['nombre_categoria']).'
                  </div>';
        }
    } else {
        echo '<p style="text-align: center; width: 100%; color: var(--text-muted);">No hay categorías disponibles</p>';
    }
    ?>
</div>

<?php
// Cargar productos por categoría
$sql_categorias_productos = "SELECT id_categoria, nombre_categoria FROM categorias";
$result_cat_prod = $database->consulta($sql_categorias_productos);

if ($result_cat_prod && $result_cat_prod->num_rows > 0) {
    while($cat = $result_cat_prod->fetch_assoc()) {
        echo '<div class="productos" id="'.$cat['id_categoria'].'">';
        
        // Consulta corregida con nombres de columnas correctos
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
            echo '<p style="text-align: center; width: 100%; color: var(--text-muted);">No hay productos disponibles en esta categoría</p>';
        }
        echo '</div>';
    }
} else {
    echo '<p style="text-align: center; width: 100%; color: var(--text-muted);">No hay categorías con productos disponibles</p>';
}
?>

<div id="global-no-results" style="text-align: center; color: var(--text-muted); font-size: 1.5rem; padding: 50px; display: none;">
    No se encontró ningún producto con ese criterio de búsqueda.
</div>

<script>
    let categoriaActiva = null; 
    const categoriasContainer = document.getElementById('categorias');
    const backBtn = document.querySelector('.back-btn');
    const searchInput = document.getElementById('search-input');
    const allProductsContainers = document.querySelectorAll('.productos');
    const globalNoResults = document.getElementById('global-no-results'); 

    function mostrarProductos(id) {
        globalNoResults.style.display = 'none'; 
        categoriasContainer.style.display = 'none';
        allProductsContainers.forEach(p => p.classList.remove('show'));
        document.querySelectorAll('.producto').forEach(p => p.style.display = 'block'); 

        const productosContainer = document.getElementById(id.toString());
        if (!productosContainer) {
             console.warn(`Contenedor de productos para categoría '${id}' no encontrado.`);
             volverCategorias();
             return;
        }
        productosContainer.classList.add('show');
        
        backBtn.style.display = 'inline-block';
        categoriaActiva = id;
        searchInput.value = '';
    }

    function volverCategorias() {
        allProductsContainers.forEach(p => p.classList.remove('show'));
        globalNoResults.style.display = 'none';
        categoriasContainer.style.display = 'flex';
        backBtn.style.display = 'none';
        categoriaActiva = null;
        searchInput.value = '';
        document.querySelectorAll('.producto').forEach(p => p.style.display = 'block');
    }

    function filtrarProductosGlobal() {
        const filtro = searchInput.value.toLowerCase().trim();
        let productosEncontrados = 0;

        globalNoResults.style.display = 'none'; 
        
        if (filtro === '') {
            if (categoriaActiva) {
                mostrarProductos(categoriaActiva); 
            } else {
                volverCategorias(); 
            }
            return;
        }

        categoriasContainer.style.display = 'none';
        backBtn.style.display = 'inline-block';
        
        allProductsContainers.forEach(container => {
            let productosVisiblesEnCategoria = 0;

            container.querySelectorAll('.producto').forEach(producto => {
                const nombre = producto.getAttribute('data-name')?.toLowerCase() || '';
                const descripcion = producto.getAttribute('data-desc')?.toLowerCase() || '';

                if (nombre.includes(filtro) || descripcion.includes(filtro)) {
                    producto.style.display = 'block'; 
                    productosVisiblesEnCategoria++;
                    productosEncontrados++;
                } else {
                    producto.style.display = 'none'; 
                }
            });

            if (productosVisiblesEnCategoria > 0) {
                container.classList.add('show');
            } else {
                container.classList.remove('show');
            }
        });

        if (productosEncontrados === 0) {
            globalNoResults.style.display = 'block';
            allProductsContainers.forEach(p => p.classList.remove('show'));
        }
    }

    searchInput.onkeyup = filtrarProductosGlobal;

    function prestar(productoId) {
        // Redirigir a la página de crear préstamo existente
        window.location.href = 'crearPrestamo.php?id_producto=' + productoId;
    }

    document.addEventListener('DOMContentLoaded', () => {
        volverCategorias(); 
    });
</script>

</body>
</html>
