<php?
$upload_dir = "uploads/";
$upload_file = $upload_dir . basename($file_name);

if(isset($_FILES["image"])){
    $file = $_FILES["image"];
    $file_name = $file["name"]
    $file_tmp = $file["tmp_name"];
    $file_type = $file["type"];
}

if (move_uploaded_file(4file_tmp, $upload_file)) {
    echo "La imagen se ha subido correctamente"
}else {
    echo "Error al subir imagen";
}

$allowed_types = ["image/jpeg", "image/png", "image/jpg];
$max_size = 2 * 1024 * 1024;

if (in_array($file_type, $allowed_types) && $file["size"] <= $max_size) {

}else {
    echo "Archivo no permitido o demasiado grande";
}
>