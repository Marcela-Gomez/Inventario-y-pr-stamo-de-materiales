<?php
require_once '../cn.php';
$db = new cn();

//////////////////////////////
// Consulta productos bajo stock
$sql_stock = "SELECT id_producto, nombre_producto, id_categoria, stock, precio
			  FROM productos
			  WHERE stock <= 5
			  ORDER BY stock ASC";
$result_stock = $db->consulta($sql_stock);

//////////////////////////////
// Consulta movimientos diarios por tipo
$sql_mov = "SELECT 
			 	DATE(fecha_movimiento) AS fecha,
			 	tipo_movimiento,
			 	SUM(cantidad) AS total_movimientos
			 FROM movimientos
			 GROUP BY fecha, tipo_movimiento
			 ORDER BY fecha ASC, tipo_movimiento";
$result_mov = $db->consulta($sql_mov);

// Preparar datos para Chart.js
$movimientos = [];
$fechas = [];
while($row = $result_mov->fetch_assoc()){
	$fecha = $row['fecha'];
	$tipo = $row['tipo_movimiento'];
	$total = (int)$row['total_movimientos'];

	if(!in_array($fecha, $fechas)) $fechas[] = $fecha;
	$movimientos[$tipo][$fecha] = $total;
}

// Completar con ceros
foreach($movimientos as $tipo => $data){
	foreach($fechas as $fecha){
		if(!isset($movimientos[$tipo][$fecha])){
			$movimientos[$tipo][$fecha] = 0;
		}
	}
}
sort($fechas);
foreach($movimientos as $tipo => &$data){
	ksort($data);
}

//////////////////////////////
// Consulta stock actual considerando movimientos
$sql_stock_actual = "SELECT 
			 	 	 	 p.nombre_producto, p.stock AS stock_actual
			 	 	 	FROM productos p
			 	 	 	LEFT JOIN movimientos m ON p.id_producto = m.id_producto
			 	 	 	GROUP BY p.id_producto, p.nombre_producto
			 	 	 	ORDER BY p.nombre_producto ASC";
$result_stock_actual = $db->consulta($sql_stock_actual);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard Inventario</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Librerías Necesarias para Generar PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- Librería para Excel -->
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

<style>
	body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; padding: 20px; }
	h2 { text-align: center; color: #333; margin-top: 30px; }
	.container { max-width: 1000px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
	table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
	th, td { padding: 12px 20px; text-align: left; }
	th { background: linear-gradient(90deg, #4f46e5, #818cf8); color: #fff; text-transform: uppercase; letter-spacing: 0.05em; }
	tr:nth-child(even) { background: #f9f9f9; }
	tr:hover { background: #e0e7ff; transition: 0.3s; }
	.low-stock { color: #d14343; font-weight: bold; }
	canvas { max-width: 100%; }
	button { transition: 0.2s; }
	button:hover { opacity: 0.9; }
</style>
</head>
<body>

<!-- CONTENEDOR PRINCIPAL: TODO ESTO SERÁ CAPTURADO PARA EL PDF -->
<div id="reporte-inventario">

	<!-- Botones de Descarga -->
	<div class="container" style="text-align: right; margin-bottom: 0px; padding: 10px 20px;">
		<button onclick="generatePDF('reporte-inventario', 'Reporte_Inventario.pdf')" 
				style="background: #10b981; /* Verde */
					   color: white; 
					   padding: 10px 20px; 
					   border: none; 
					   border-radius: 6px; 
					   cursor: pointer; 
					   font-weight: bold;
					   box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
			Descargar Reporte PDF
		</button>

		<button onclick="exportToExcel('tabla-stock-bajo', 'Stock_Bajo.xlsx')" 
				style="background: #3b82f6; /* Azul */
					   color: white; 
					   padding: 10px 20px; 
					   border: none; 
					   border-radius: 6px; 
					   cursor: pointer; 
					   font-weight: bold;
					   box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-left: 10px;">
			Descargar Excel
		</button>
	</div>

	<div class="container">
		<h2>Productos Pendientes de abastecer Stock Bajo 5</h2>
		<table id="tabla-stock-bajo">
			<thead>
				<tr>
					<th>ID</th>
					<th>Producto</th>
					<th>Categoría</th>
					<th>Stock</th>
					<th>Precio</th>
				</tr>
			</thead>
			<tbody>
			<?php if ($result_stock && $result_stock->num_rows > 0): ?>
				<?php while($row = $result_stock->fetch_assoc()): ?>
					<tr>
						<td><?= $row['id_producto'] ?></td>
						<td><?= htmlspecialchars($row['nombre_producto']) ?></td>
						<td><?= $row['id_categoria'] ?></td>
						<td class="<?= $row['stock'] <= 2 ? 'low-stock' : '' ?>"><?= $row['stock'] ?></td>
						<td>$<?= number_format($row['precio'], 2) ?></td>
					</tr>
				<?php endwhile; ?>
			<?php else: ?>
				<tr><td colspan="5" style="text-align:center; color:#555;">No hay productos con stock bajo</td></tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>

	<div class="container">
		<h2>Movimientos Diarios por Tipo</h2>
		<canvas id="movimientosLineChart"></canvas>
	</div>

	<div class="container">
		<h2>Stock Actual por Producto</h2>
		<table id="tabla-stock-actual">
			<thead>
				<tr>
					<th>Producto</th>
					<th>Stock Actual</th>
				</tr>
			</thead>
			<tbody>
			<?php if ($result_stock_actual && $result_stock_actual->num_rows > 0): ?>
				<?php while($row = $result_stock_actual->fetch_assoc()): ?>
					<tr>
						<td><?= htmlspecialchars($row['nombre_producto']) ?></td>
						<td><?= $row['stock_actual'] ?></td>
					</tr>
				<?php endwhile; ?>
			<?php else: ?>
				<tr><td colspan="2" style="text-align:center; color:#555;">No hay datos de stock actual</td></tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>

</div> <!-- Fin de reporte-inventario -->

<script>
// Chart.js Movimientos
const ctx = document.getElementById('movimientosLineChart').getContext('2d');
const chart = new Chart(ctx, {
	type: 'line',
	data: {
		labels: <?= json_encode($fechas) ?>,
		datasets: [
			<?php foreach($movimientos as $tipo => $data): ?>
			{
				label: "<?= $tipo ?>",
				data: <?= json_encode(array_values($data)) ?>,
				fill: false,
				tension: 0.1,
				borderColor: "<?= $tipo === 'Préstamo' ? 'rgba(79, 70, 229, 1)' : 'rgba(34, 197, 94, 1)' ?>",
				backgroundColor: "<?= $tipo === 'Préstamo' ? 'rgba(79, 70, 229, 0.2)' : 'rgba(34, 197, 94, 0.2)' ?>",
				borderWidth: 2,
				pointRadius: 4
			},
			<?php endforeach; ?>
		]
	},
	options: {
		responsive: true,
		plugins: {
			legend: { position: 'top' },
			title: { display: true, text: 'Movimientos Diarios (Préstamos y Devoluciones)' }
		},
		scales: {
			y: { beginAtZero: true, ticks: { stepSize: 1 } },
			x: { ticks: { maxRotation: 90, minRotation: 45 } }
		}
	}
});

// Función para PDF
function generatePDF(elementId, filename) {
    const input = document.getElementById(elementId);
    const button = document.querySelector('button[onclick*="generatePDF"]');
    if (button) { button.disabled = true; button.textContent = 'Generando...'; }

    setTimeout(() => {
        html2canvas(input, { scale: 2, useCORS: true }).then(canvas => {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4'); 
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 200; 
            const pageHeight = 290;
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            let position = 5;

            pdf.addImage(imgData, 'PNG', 5, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft > 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 5, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            pdf.save(filename);
            if (button) { button.disabled = false; button.textContent = 'Descargar Reporte PDF'; }
        }).catch(error => {
            console.error("Error al generar el PDF:", error);
            if (button) { button.disabled = false; button.textContent = 'Descargar Reporte PDF'; }
            alert("Ocurrió un error al intentar generar el PDF. Revisa la consola.");
        });
    }, 100); 
}

// Función para Excel
function exportToExcel(tableId, filename = 'Reporte.xlsx') {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.table_to_sheet(table); // Convierte tabla HTML en hoja
    XLSX.utils.book_append_sheet(wb, ws, 'Reporte');
    XLSX.writeFile(wb, filename);
}
</script>

</body>
</html>
