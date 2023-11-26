<?php
require_once '../../conexion.php';
require_once 'fpdf/fpdf.php';

// Crear una instancia de FPDF
$pdf = new FPDF('P', 'mm', 'letter');
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetTitle("Ventas");

// Establecer la zona horaria a Centroamérica (UTC-6)
date_default_timezone_set('America/Belize'); // UTC-6

// Establecer fuente y tamaño de fuente
$pdf->SetFont('Arial', 'B', 14);

// Obtener datos de configuración
$id = $_GET['v'];
$idcliente = $_GET['cl'];
$config = mysqli_query($conexion, "SELECT * FROM configuracion");
$datos = mysqli_fetch_assoc($config);

// Insertar el nombre de la empresa y el logo
$pdf->Cell(192, 10, utf8_decode($datos['nombre']), 0, 0, 'C');
$pdf->Image("../../assets/img/logo1.png", 180, 15, 30, 30, 'PNG');

// Fecha de emisión (con hora en la zona horaria de Centroamérica)
$pdf->Cell(8, 10, date('d-m-Y'), 0, 1, 'R');

// Restaurar la zona horaria a la configuración predeterminada
date_default_timezone_set('UTC'); // Opcional: Restaura a UTC si es necesario

// Datos de contacto de la empresa
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 5, utf8_decode("Teléfono: "), 0, 0, 'L');
$pdf->Cell(20, 5, $datos['telefono'], 0, 1, 'L');
$pdf->Cell(20, 5, utf8_decode("Dirección: "), 0, 0, 'L');
$pdf->Cell(20, 5, utf8_decode($datos['direccion']), 0, 1, 'L');
$pdf->Cell(20, 5, "Correo: ", 0, 0, 'L');
$pdf->Cell(20, 5, utf8_decode($datos['email']), 0, 1, 'L');
$pdf->Ln(10);

// Datos del cliente
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(195, 8, "Datos del Cliente", 1, 1, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(65, 8, utf8_decode('Nombre'), 1, 0, 'C');
$pdf->Cell(65, 8, utf8_decode('Teléfono'), 1, 0, 'C');
$pdf->Cell(65, 8, utf8_decode('Dirección'), 1, 1, 'C');

// Obtener datos del cliente
$clientes = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");
$datosC = mysqli_fetch_assoc($clientes);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(65, 8, utf8_decode($datosC['nombre']), 1, 0, 'C');
$pdf->Cell(65, 8, utf8_decode($datosC['telefono']), 1, 0, 'C');
$pdf->Cell(65, 8, utf8_decode($datosC['direccion']), 1, 1, 'C');
$pdf->Ln(10);

// Detalle de Producto
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(196, 8, "Detalle de Producto", 1, 1, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(20, 8, utf8_decode('N°'), 1, 0, 'C');
$pdf->Cell(25, 8, utf8_decode('Codigo'), 1, 0, 'C');
$pdf->Cell(60, 8, utf8_decode('Descripción'), 1, 0, 'C');
$pdf->Cell(25, 8, 'Cantidad', 1, 0, 'C');
$pdf->Cell(35, 8, 'Precio', 1, 0, 'C');
$pdf->Cell(31, 8, 'Subtotal', 1, 1, 'C');

$pdf->SetFont('Arial', '', 12); 
$contador = 1;
$total = 0;

// Obtener detalles de venta
$ventas = mysqli_query($conexion, "SELECT d.*, p.codproducto, p.descripcion, codigo FROM detalle_venta d INNER JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_venta = $id");

while ($row = mysqli_fetch_assoc($ventas)) {
    $pdf->Cell(20, 8, $contador, 1, 0, 'C');
    $pdf->Cell(25, 8, utf8_decode($row['codigo']), 1, 0, 'C');
    $pdf->Cell(60, 8, utf8_decode($row['descripcion']), 1, 0, 'C');
    $pdf->Cell(25, 8, $row['cantidad'], 1, 0, 'C');
    $pdf->Cell(35, 8, number_format($row['precio'], 2, '.', ','), 1, 0, 'C');
    $subtotal = $row['cantidad'] * $row['precio'];
    $pdf->Cell(31, 8, number_format($subtotal, 2, '.', ','), 1, 1, 'C');
    $contador++;
    $total += $subtotal;
}

// Mostrar el total al final de la tabla de productos
$pdf->SetFont('Arial', 'B', 12);

// Concatenar el símbolo de la moneda a las cifras de subtotal y total
$subtotal = "C$" . number_format($total, 2, '.', ',');
$total = "C$" . number_format($total, 2, '.', ',');

$pdf->Cell(176, 8, "Total:", 1, 0, 'R');
$pdf->Cell(20, 8, $total, 1, 1, 'C');

// Generar el PDF y mostrarlo en el navegador
$pdf->Output("ventas.pdf", "I");
?>


