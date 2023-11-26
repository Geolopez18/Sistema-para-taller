<?php
include_once "includes/header.php";
require_once "../conexion.php";

$id_user = $_SESSION['idUser'];
$permiso = "ventas";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
}

if (isset($_GET['id'])) {
    $id_venta = $_GET['id'];
    $query = mysqli_query($conexion, "SELECT v.*, c.idcliente, c.nombre FROM ventas v INNER JOIN cliente c ON v.id_cliente = c.idcliente WHERE v.id = $id_venta");
    $venta = mysqli_fetch_assoc($query);
} else {
    // Manejar el caso en el que no se proporciona un ID de venta válido.
    header("Location: lista_ventas.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Actualizar la venta
    $nuevo_cliente = $_POST['cliente'];
    $nuevo_total = $_POST['total'];
    $nueva_fecha = $_POST['fecha'];

    $sql_actualizar_venta = "UPDATE ventas SET id_cliente = $nuevo_cliente, total = $nuevo_total, fecha = '$nueva_fecha' WHERE id = $id_venta";
    if (mysqli_query($conexion, $sql_actualizar_venta)) {
        // Redirige a la lista de ventas después de la edición exitosa.
        header("Location: lista_ventas.php");
        exit();
    } else {
        echo "Error al actualizar la venta: " . mysqli_error($conexion);
    }

    // Agregar productos a la venta
    $producto_nombre = $_POST['producto_nombre'];
    $producto_cantidad = $_POST['producto_cantidad'];

    $sql_insert_producto = "INSERT INTO productos (nombre, cantidad, id_venta) VALUES ('$producto_nombre', $producto_cantidad, $id_venta)";
    if (mysqli_query($conexion, $sql_insert_producto)) {
        // Producto agregado con éxito, puedes redirigir a la misma página o realizar otras acciones.
    } else {
        echo "Error al agregar el producto: " . mysqli_error($conexion);
    }
}
?>

<div class="container">
    <h2>Editar Factura</h2>
    <form action="" method="POST">
        <input type="hidden" name="id_venta" value="<?php echo $id_venta; ?>">
        <!-- Campos de edición -->
        <div class="form-group">
            <label for="cliente">Cliente:</label>
            <input type="text" class="form-control" id="cliente" name="cliente" value="<?php echo $venta['idcliente']; ?>">
        </div>
        <div class="form-group">
            <label for="total">Total:</label>
            <input type="text" class="form-control" id="total" name="total" value="<?php echo $venta['total']; ?>">
        </div>
        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="text" class="form-control" id="fecha" name="fecha" value="<?php echo $venta['fecha']; ?>">
        </div>
        
        <!-- Campos para agregar productos -->
        <h3>Agregar Producto</h3>
        <div class="form-group">
            <label for="producto_nombre">Nombre del Producto:</label>
            <input type="text" class="form-control" id="producto_nombre" name="producto_nombre">
        </div>
        <div class="form-group">
            <label for="producto_cantidad">Cantidad:</label>
            <input type="number" class="form-control" id="producto_cantidad" name="producto_cantidad">
        </div>
        
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

<?php include_once "includes/footer.php"; ?>