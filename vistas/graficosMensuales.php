<?php
require_once '../cn.php';
$db = new cn();

//////////////////////////
// Movimientos últimos 6 meses (tabla)
$sql_6meses = "SELECT 
    COALESCE(u_prestamista.nombre, u_prestatario.nombre) AS usuario,
    p.nombre_producto AS nombre_producto,
    p.puede_devolverse AS puede_devolverse,
    m.tipo_movimiento AS tipo_movimiento,
    m.cantidad AS cantidad,
    m.fecha_movimiento AS fecha_movimiento,
    m.estado AS estado
FROM movimientos m
LEFT JOIN usuarios u_prestamista ON m.id_prestamista = u_prestamista.id_usuario
LEFT JOIN usuarios u_prestatario ON m.id_prestatario = u_prestatario.id_usuario
INNER JOIN productos p ON m.id_producto = p.id_producto
WHERE m.fecha_movimiento >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
ORDER BY m.fecha_movimiento DESC";

$result_6meses = $db->consulta($sql_6meses);

//////////////////////////
// Personas con más devoluciones mensuales (tabla)
$sql_dev_mes = "SELECT 
	u.nombre AS prestamista,
	DATE_FORMAT(m.fecha_movimiento, '%Y-%m') AS mes,
	COUNT(*) AS total_devoluciones
FROM movimientos m
JOIN usuarios u ON m.id_prestamista = u.id_usuario
JOIN productos p ON m.id_producto = p.id_producto
WHERE m.tipo_movimiento = 'Devolución'
  AND p.puede_devolverse = 1
GROUP BY u.id_usuario, mes
ORDER BY mes ASC, total_devoluciones DESC";

$result_dev_mes = $db->consulta($sql_dev_mes);

//////////////////////////
// Movimientos mensuales por tipo (gráfico)
$sql_mov_mes = "SELECT 
			  DATE_FORMAT(fecha_movimiento, '%Y-%m') AS mes,
			  tipo_movimiento,
			  SUM(cantidad) AS total_movimientos
			FROM movimientos
			GROUP BY mes, tipo_movimiento
			ORDER BY mes ASC";

$result_mov_mes = $db->consulta($sql_mov_mes);

$movimientos = [];
$meses = [];
while($row = $result_mov_mes->fetch_assoc()){
	$mes = $row['mes'];
	$tipo = $row['tipo_movimiento'];
	$total = (int)$row['total_movimientos'];
	if(!in_array($mes, $meses)) $meses[] = $mes;
	$movimientos[$tipo][$mes] = $total;
}
foreach($movimientos as $tipo => $data){
	foreach($meses as $mes){
		if(!isset($movimientos[$tipo][$mes])) $movimientos[$tipo][$mes] = 0;
	}
}
sort($meses);
foreach($movimientos as $tipo => &$data) ksort($data);

//////////////////////////
// Prestamistas que cumplen devoluciones (gráfico)
$sql_cumplen = "SELECT 
	u.nombre AS prestamista,
	COUNT(m.id_movimiento) AS devoluciones_realizadas
FROM movimientos m
JOIN usuarios u ON m.id_prestamista = u.id_usuario
JOIN productos p ON m.id_producto = p.id_producto
WHERE m.tipo_movimiento = 'Devolución'
  AND p.puede_devolverse = 1
GROUP BY u.id_usuario
ORDER BY devoluciones_realizadas DESC";

$result_cumplen = $db->consulta($sql_cumplen);
$prestamistas_cumplen = [];
$dev_realizadas = [];
while($row = $result_cumplen->fetch_assoc()){
	$prestamistas_cumplen[] = $row['prestamista'];
	$dev_realizadas[] = (int)$row['devoluciones_realizadas'];
}

//////////////////////////
// Prestamistas que casi no devuelven (gráfico)
$sql_no_devuelven = "SELECT 
	u.nombre AS prestamista,
	COUNT(m.id_movimiento) AS prestamos_no_devuelto
FROM movimientos m
JOIN usuarios u ON m.id_prestatario = u.id_usuario
JOIN productos p ON m.id_producto = p.id_producto
WHERE m.tipo_movimiento = 'Préstamo'
  AND p.puede_devolverse = 1
  AND m.id_movimiento NOT IN (
	  SELECT id_movimiento FROM movimientos 
	  WHERE tipo_movimiento = 'Devolución'
  )
GROUP BY u.id_usuario
ORDER BY prestamos_no_devuelto DESC";

$result_no_devuelven = $db->consulta($sql_no_devuelven);
$prestamistas_no_dev = [];
$prestamos_pendientes = [];
while($row = $result_no_devuelven->fetch_assoc()){
	$prestamistas_no_dev[] = $row['prestamista'];
	$prestamos_pendientes[] = (int)$row['prestamos_no_devuelto'];
}

//////////////////////////
// Prestamistas que más prestan (gráfico)
$sql_mas_presta = "SELECT 
	u.nombre AS prestamista,
	COUNT(*) AS total_prestamos
FROM movimientos m
JOIN usuarios u ON m.id_prestamista = u.id_usuario
WHERE m.tipo_movimiento = 'Préstamo'
GROUP BY u.id_usuario
ORDER BY total_prestamos DESC";

$result_mas_presta = $db->consulta($sql_mas_presta);
$prestamistas_mas = [];
$totales_prestamos = [];
while($row = $result_mas_presta->fetch_assoc()){
	$prestamistas_mas[] = $row['prestamista'];
	$totales_prestamos[] = (int)$row['total_prestamos'];
}

$colores = [
    "Prestamo"   => "rgba(79,70,229,0.7)",
    "Devolucion" => "rgba(34,197,94,0.7)",
    "Salida"     => "rgba(239,68,68,0.7)",
    "Compra"    => "rgba(16,185,129,0.7)"
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard Movimientos</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Librerías Necesarias para PDF y Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

<style>
	body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 20px; }
	.container { max-width: 1000px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
	h2 { text-align: center; color: #333; margin-top: 30px; }
	table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
	th, td { padding: 10px; text-align: left; }
	th { background: #4f46e5; color: #fff; }
	tr:nth-child(even) { background: #f9f9f9; }
	tr:hover { background: #e0e7ff; }
	button { padding:10px 20px; border:none; border-radius:6px; cursor:pointer; font-weight:bold; margin-right:10px; }
	button:hover { opacity:0.9; }
</style>
</head>
<body>

<div id="reporte-movimientos">

	<!-- Botones de descarga -->
	<div class="container" style="text-align:right;">
		<button onclick="generatePDF('reporte-movimientos','Reporte_Movimientos_Mensuales.pdf')" style="background:#ef4444; color:white;">
			Descargar PDF
		</button>

		<button onclick="exportToExcel('tabla-dev-mensuales','Devoluciones_Mensuales.xlsx')" style="background:#3b82f6; color:white;">
			Descargar Excel Devoluciones Mensuales
		</button>

		<button onclick="exportToExcel('tabla-ultimos-6-meses','Movimientos_6Meses.xlsx')" style="background:#10b981; color:white;">
			Descargar Excel Últimos 6 Meses
		</button>
	</div>

	<div class="container">
		<h2>Movimientos Mensuales por Tipo</h2>
		<canvas id="movimientosChart"></canvas>
	</div>

	<div class="container">
		<h2>Prestamistas que cumplen con devoluciones</h2>
		<canvas id="cumplenChart"></canvas>
	</div>

	<div class="container">
		<h2>Prestamistas que no devuelven</h2>
		<canvas id="noDevuelvenChart"></canvas>
	</div>

	<div class="container">
		<h2>Prestamistas que más prestan</h2>
		<canvas id="masPrestanChart"></canvas>
	</div>

	<div class="container">
		<h2>Devoluciones mensuales por prestamista</h2>
		<table id="tabla-dev-mensuales">
			<thead>
				<tr>
					<th>Prestamista</th>
					<th>Mes</th>
					<th>Total Devoluciones</th>
				</tr>
			</thead>
			<tbody>
				<?php if($result_dev_mes && $result_dev_mes->num_rows>0): ?>
					<?php while($row=$result_dev_mes->fetch_assoc()): ?>
						<tr>
							<td><?= htmlspecialchars($row['prestamista']) ?></td>
							<td><?= $row['mes'] ?></td>
							<td><?= $row['total_devoluciones'] ?></td>
						</tr>
					<?php endwhile; ?>
				<?php else: ?>
					<tr><td colspan="3" style="text-align:center;">No hay datos</td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<div class="container">
		<h2>Movimientos últimos 6 meses</h2>
		<table id="tabla-ultimos-6-meses">
			<thead>
				<tr>
					<th>Usuario</th>
					<th>Producto</th>
					<th>Puede Devolver</th>
					<th>Tipo Movimiento</th>
					<th>Cantidad</th>
					<th>Fecha</th>
					<th>Estado</th>
				</tr>
			</thead>
			<tbody>
				<?php if($result_6meses && $result_6meses->num_rows>0): ?>
					<?php while($row=$result_6meses->fetch_assoc()): ?>
						<tr>
							<td><?= htmlspecialchars($row['usuario']) ?></td>
							<td><?= htmlspecialchars($row['nombre_producto']) ?></td>
							<td><?= $row['puede_devolverse'] ? 'Sí':'No' ?></td>
							<td><?= $row['tipo_movimiento'] ?></td>
							<td><?= $row['cantidad'] ?></td>
							<td><?= $row['fecha_movimiento'] ?></td>
							<td><?= $row['estado'] ?></td>
						</tr>
					<?php endwhile; ?>
				<?php else: ?>
					<tr><td colspan="7" style="text-align:center;">No hay datos</td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

</div>

<script>
// Charts
const ctxMov = document.getElementById('movimientosChart').getContext('2d');
new Chart(ctxMov, {
	type:'bar',
	data:{
		labels: <?= json_encode($meses) ?>,
		datasets:[
			<?php foreach($movimientos as $tipo=>$data): ?>
			{
				label:"<?= $tipo ?>",
				data: <?= json_encode(array_values($data)) ?>,
				backgroundColor: "<?= $colores[$tipo] ?? 'rgba(107,114,128,0.7)' ?>",
				borderColor: "<?= str_replace('0.7','1',$colores[$tipo] ?? 'rgba(107,114,128,0.7)') ?>",
				borderWidth:1
			},
			<?php endforeach; ?>
		]
	},
	options:{responsive:true}
});

const ctxCumplen = document.getElementById('cumplenChart').getContext('2d');
new Chart(ctxCumplen,{
	type:'bar',
	data:{
		labels: <?= json_encode($prestamistas_cumplen) ?>,
		datasets:[{
			label:'Devoluciones Realizadas',
			data: <?= json_encode($dev_realizadas) ?>,
			backgroundColor:'rgba(34,197,94,0.7)',
			borderColor:'rgba(34,197,94,1)',
			borderWidth:1
		}]
	},
	options:{responsive:true}
});

const ctxNoDev = document.getElementById('noDevuelvenChart').getContext('2d');
new Chart(ctxNoDev,{
	type:'bar',
	data:{
		labels: <?= json_encode($prestamistas_no_dev) ?>,
		datasets:[{
			label:'Préstamos No Devueltos',
			data: <?= json_encode($prestamos_pendientes) ?>,
			backgroundColor:'rgba(239,68,68,0.7)',
			borderColor:'rgba(220,38,38,1)',
			borderWidth:1
		}]
	},
	options:{responsive:true}
});

const ctxMasPrestan = document.getElementById('masPrestanChart').getContext('2d');
new Chart(ctxMasPrestan,{
	type:'bar',
	data:{
		labels: <?= json_encode($prestamistas_mas) ?>,
		datasets:[{
			label:'Total Préstamos',
			data: <?= json_encode($totales_prestamos) ?>,
			backgroundColor:'rgba(79,70,229,0.7)',
			borderColor:'rgba(79,70,229,1)',
			borderWidth:1
		}]
	},
	options:{responsive:true}
});

// PDF
function generatePDF(elementId, filename){
	const input=document.getElementById(elementId);
	const button=document.querySelector('button[onclick*="generatePDF"]');
	if(button){ button.disabled=true; button.textContent='Generando...'; }
	setTimeout(()=>{
		html2canvas(input,{scale:2,useCORS:true}).then(canvas=>{
			const {jsPDF}=window.jspdf;
			const pdf=new jsPDF('p','mm','a4');
			const imgData=canvas.toDataURL('image/png');
			const imgWidth=200,pageHeight=290;
			const imgHeight=canvas.height*imgWidth/canvas.width;
			let heightLeft=imgHeight,position=5;
			pdf.addImage(imgData,'PNG',5,position,imgWidth,imgHeight);
			heightLeft-=pageHeight;
			while(heightLeft>0){
				position=heightLeft-imgHeight;
				pdf.addPage();
				pdf.addImage(imgData,'PNG',5,position,imgWidth,imgHeight);
				heightLeft-=pageHeight;
			}
			pdf.save(filename);
			if(button){ button.disabled=false; button.textContent='Descargar PDF'; }
		}).catch(e=>{
			console.error(e);
			if(button){ button.disabled=false; button.textContent='Descargar PDF'; }
			alert('Error al generar PDF');
		});
	},100);
}

// Excel
function exportToExcel(tableId, filename='Reporte.xlsx'){
	const table=document.getElementById(tableId);
	const wb=XLSX.utils.book_new();
	const ws=XLSX.utils.table_to_sheet(table);
	XLSX.utils.book_append_sheet(wb,ws,'Reporte');
	XLSX.writeFile(wb,filename);
}
</script>

</body>
</html>