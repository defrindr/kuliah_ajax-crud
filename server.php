<?php

/**
 * This program is really bad xD
 * not implementate best practice
 * and have many bug :(
 */

header('Content-Type: application/json; charset=utf-8');
$koneksi = mysqli_connect("localhost", "root", "defrindr", "db_kuliah_ajax") or die("Conection Lost !!!");
function action_insert($data)
{
    $columns = ["nama", "nrp", "prodi"];

    $query = "";
    foreach ($columns as $col) {
        $query .= "\"{$data[$col]}\",";
    }

    $query = substr($query, 0, strlen($query) - 1);
    $query = "insert into mahasiswa(nama,nrp,prodi) values ($query)";
    return $query;
}

function action_update($data)
{
    $columns = ["nama", "nrp", "prodi"];

    $query = "";
    foreach ($columns as $col) {
        $query .= "`$col`=\"{$data[$col]}\",";
    }

    $query = substr($query, 0, strlen($query) - 1);
    $query = "update mahasiswa set $query where id={$data['id']}";
    return $query;
}

function action_delete($id)
{
    $query = "delete from mahasiswa  where id={$id}";
    return $query;
}

function action_search($_search)
{
    $columns = ["nama", "nrp", "prodi"];

    $query = "";
    foreach ($columns as $col) {
        $query .= "`$col` like \"%{$_search}%\" or";
    }

    $query = substr($query, 0, strlen($query) - 3);
    $query = "select * from mahasiswa where $query";
    return $query;
}

function action_view($id)
{
    $query = "select * from mahasiswa where id='$id'";
    return $query;
}

$action = $_GET['action'] ?? "all";
switch ($action) {
    case 'insert':
        if (isset($_POST['id']) && intval($_POST['id']) != 0) {
            $status = mysqli_query($koneksi, action_update($_POST));
        } else {
            $status = mysqli_query($koneksi, action_insert($_POST));
        }
        $response = mysqli_query($koneksi, "select * from mahasiswa");
        break;
    case 'delete':
        $status = mysqli_query($koneksi, action_delete($_GET['id']));
        $response = mysqli_query($koneksi, "select * from mahasiswa");
        break;
    case 'view':
        $response = mysqli_query($koneksi, action_view($_GET['id']));
        break;
    case 'search':
        $response = mysqli_query($koneksi, action_search($_GET['search']));
        break;
    default:
        $response = mysqli_query($koneksi, "select * from mahasiswa");
        break;
}

mysqli_close($koneksi);

$html = "";
if ($response == null) {
    echo json_encode([
        "status" => isset($status) && $status,
        "html" => $html
    ]);
    die;
}
$rows = [];
while ($row = mysqli_fetch_array($response)) {
    $tombol = "
    <button class='btn btn-primary' onclick='update({$row['id']})'>Update</button>
    <button class='btn btn-danger' onclick='deletedata({$row['id']})'>Hapus</button>
    ";
    $html .= "<tr>
    <td>{$row['nrp']}</td>
    <td>{$row['nama']}</td>
    <td>{$row['prodi']}</td>
    <td>
    $tombol
    </td>
    </tr>";
    $rows[] = $row;
}

echo json_encode([
    "status" => isset($status) && $status,
    "html" => $html,
    "data" => $rows
]);
die;
